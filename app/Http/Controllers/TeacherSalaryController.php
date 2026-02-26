<?php

namespace App\Http\Controllers;

use App\Models\TeacherSalary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Transfer;
use Stripe\Balance;

class TeacherSalaryController extends Controller
{
    /**
     * Check if we're in Stripe test mode (using test API keys)
     * This still uses real Stripe APIs but with test accounts
     */
    protected function isTestMode(): bool
    {
        $stripeKey = env('STRIPE_SECRET', '');
        return str_starts_with($stripeKey, 'sk_test_');
    }

    public function __construct()
    {
        if (env('STRIPE_SECRET')) {
            Stripe::setApiKey(env('STRIPE_SECRET'));
        }
    }

    /**
     * Determine which currency to use when talking to Stripe
     */
    protected function getStripeCurrency(): string
    {
        return strtolower(env('STRIPE_CURRENCY', 'usd'));
    }

    /**
     * Convert a decimal amount to Stripe's integer representation
     */
    protected function toStripeAmount(float $amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * Fetch available platform balance for a currency (in minor units)
     */
    protected function getAvailableStripeBalance(string $currency): int
    {
        $currency = strtolower($currency);
        $balance = Balance::retrieve();
        $total = 0;

        foreach ($balance->available as $entry) {
            if ($entry->currency === $currency) {
                $total += $entry->amount;
            }
        }

        return $total;
    }

    /**
     * Ensure we have enough funds before attempting a transfer
     */
    protected function ensureStripeFunds(int $amount, string $currency): void
    {
        $available = $this->getAvailableStripeBalance($currency);

        if ($available < $amount) {
            $required = number_format($amount / 100, 2);
            $current = number_format($available / 100, 2);
            $code = strtoupper($currency);

            throw new \RuntimeException("Insufficient Stripe balance ({$current} {$code} available, {$required} {$code} required).");
        }
    }

    /**
     * Create a Stripe transfer to the teacher's connected account
     */
    protected function createStripeTransfer(User $teacher, TeacherSalary $salary, ?float $overrideAmount = null, string $context = 'manual'): \Stripe\Transfer
    {
        $currency = $this->getStripeCurrency();
        $amount = $overrideAmount ?? (float) $salary->amount;
        $stripeAmount = $this->toStripeAmount($amount);

        $this->ensureStripeFunds($stripeAmount, $currency);

        return Transfer::create([
            'amount' => $stripeAmount,
            'currency' => $currency,
            'destination' => $teacher->stripe_connect_id,
            'description' => sprintf('Teacher salary (%s) - %s', $salary->payment_period, config('app.name', 'Academia')),
            'metadata' => [
                'salary_id' => (string) $salary->id,
                'teacher_id' => (string) $teacher->id,
                'teacher_email' => $teacher->email,
                'period' => $salary->payment_period,
                'context' => $context,
            ],
        ]);
    }

    /**
     * Display salary management dashboard
     */
    public function index(Request $request)
    {
        $query = TeacherSalary::with(['teacher', 'paidByAdmin']);

        // Filter by teacher
        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by month/year
        if ($request->filled('month') && $request->filled('year')) {
            $query->forPeriod($request->month, $request->year);
        }

        $salaries = $query->latest()->paginate(15);
        $teachers = User::where('role', 'teacher')->get();

        // Calculate stats
        $stats = [
            'total_paid' => TeacherSalary::where('status', 'completed')->sum('amount'),
            'pending_payments' => TeacherSalary::where('status', 'pending')->count(),
            'this_month_paid' => TeacherSalary::where('status', 'completed')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount'),
        ];

        return view('admin.salaries.index', compact('salaries', 'teachers', 'stats'));
    }

    /**
     * Show form to create new salary payment
     */
    public function create()
    {
        $teachers = User::where('role', 'teacher')->get();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        return view('admin.salaries.create', compact('teachers', 'currentMonth', 'currentYear'));
    }

    /**
     * Store a new salary payment record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
            'description' => 'nullable|string|max:500',
        ]);

        // Check if salary already exists for this teacher for the given month/year
        $existingSalary = TeacherSalary::where('teacher_id', $validated['teacher_id'])
            ->forPeriod($validated['month'], $validated['year'])
            ->first();

        if ($existingSalary) {
            return back()->withInput()->with('error', 'Salary record already exists for this teacher for the selected period.');
        }

        $salary = TeacherSalary::create([
            'teacher_id' => $validated['teacher_id'],
            'amount' => $validated['amount'],
            'month' => $validated['month'],
            'year' => $validated['year'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        return redirect()->route('admin.salaries.index')
            ->with('success', 'Salary record created successfully. You can now process the payment.');
    }

    /**
     * Show salary payment details
     */
    public function show($id)
    {
        $salary = TeacherSalary::with(['teacher', 'paidByAdmin'])->findOrFail($id);
        return view('admin.salaries.show', compact('salary'));
    }

    /**
     * Process salary payment
     * In test mode without full Stripe Connect setup, we track payments locally
     * The payment is recorded and teacher's Stripe account ID is logged
     */
    public function processPayment(Request $request, $id)
    {
        $salary = TeacherSalary::with('teacher')->findOrFail($id);

        if ($salary->status === 'completed') {
            return back()->with('error', 'This salary has already been paid.');
        }

        $teacher = $salary->teacher;

        // Check if teacher has connected Stripe account
        if (empty($teacher->stripe_connect_id)) {
            return back()->with('error', 'Teacher has not connected their Stripe account yet. Please assign a Stripe account first.');
        }

        // Check if teacher's Stripe account is active
        if ($teacher->stripe_account_status !== 'active') {
            return back()->with('error', 'Teacher\'s Stripe account is not active.');
        }

        try {
            $transfer = $this->createStripeTransfer($teacher, $salary);
        } catch (\Throwable $e) {
            \Log::error('Salary Payment Failed', [
                'salary_id' => $salary->id,
                'teacher_id' => $teacher->id,
                'amount' => $salary->amount,
                'stripe_account' => $teacher->stripe_connect_id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Stripe transfer failed: ' . $e->getMessage());
        }

        // Update salary record with real Stripe transfer IDs
        $salary->update([
            'status' => 'completed',
            'stripe_transfer_id' => $transfer->id,
            'transaction_id' => $transfer->balance_transaction ?? $transfer->id,
            'paid_at' => now(),
            'paid_by' => Auth::id(),
            'payment_method' => 'stripe_connect',
        ]);

        // Log the payment details
        \Log::info('Salary Payment Processed', [
            'salary_id' => $salary->id,
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->name,
            'amount' => $salary->amount,
            'stripe_account' => $teacher->stripe_connect_id,
            'transfer_id' => $transfer->id,
            'balance_transaction' => $transfer->balance_transaction,
        ]);

        $currencyCode = strtoupper($this->getStripeCurrency());
        $amountFormatted = number_format((float) $salary->amount, 2);
        $testLabel = $this->isTestMode() ? '[TEST MODE] ' : '';

        return redirect()->route('admin.salaries.index')
            ->with('success', "{$testLabel}Salary payment of {$amountFormatted} {$currencyCode} processed successfully for {$teacher->name}. Transfer ID: {$transfer->id}");
    }

    /**
     * Cancel a pending salary payment
     */
    public function cancel($id)
    {
        $salary = TeacherSalary::findOrFail($id);

        if ($salary->status === 'completed') {
            return back()->with('error', 'Cannot cancel a completed payment.');
        }

        $salary->update(['status' => 'cancelled']);

        return back()->with('success', 'Salary payment cancelled.');
    }

    /**
     * Delete a salary record (only pending/cancelled)
     */
    public function destroy($id)
    {
        $salary = TeacherSalary::findOrFail($id);

        if ($salary->status === 'completed') {
            return back()->with('error', 'Cannot delete a completed payment record.');
        }

        $salary->delete();

        return redirect()->route('admin.salaries.index')
            ->with('success', 'Salary record deleted successfully.');
    }

    /**
     * Show teacher's Stripe Connect onboarding status
     */
    public function teacherStripeStatus($teacherId)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($teacherId);

        $stripeStatus = [
            'connected' => !empty($teacher->stripe_connect_id),
            'status' => $teacher->stripe_account_status,
            'account_id' => $teacher->stripe_connect_id,
        ];

        if ($teacher->stripe_connect_id) {
            try {
                $account = Account::retrieve($teacher->stripe_connect_id);
                $stripeStatus['charges_enabled'] = $account->charges_enabled;
                $stripeStatus['payouts_enabled'] = $account->payouts_enabled;
                $stripeStatus['details_submitted'] = $account->details_submitted;
            } catch (\Exception $e) {
                $stripeStatus['error'] = $e->getMessage();
            }
        }

        return response()->json($stripeStatus);
    }

    /**
     * Create Stripe Connect onboarding link for teacher
     */
    public function createConnectLink($teacherId)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($teacherId);

        try {
            // Create or retrieve the connected account
            if (empty($teacher->stripe_connect_id)) {
                $account = Account::create([
                    'type' => 'express',
                    'country' => 'US', // Change as needed
                    'email' => $teacher->email,
                    'capabilities' => [
                        'transfers' => ['requested' => true],
                    ],
                    'metadata' => [
                        'teacher_id' => $teacher->id,
                    ],
                ]);

                $teacher->update([
                    'stripe_connect_id' => $account->id,
                    'stripe_account_status' => 'pending',
                ]);
            }

            // Create account link for onboarding
            $accountLink = AccountLink::create([
                'account' => $teacher->stripe_connect_id,
                'refresh_url' => route('admin.salaries.connect.refresh', $teacherId),
                'return_url' => route('admin.salaries.connect.return', $teacherId),
                'type' => 'account_onboarding',
            ]);

            return redirect($accountLink->url);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create Stripe Connect link: ' . $e->getMessage());
        }
    }

    /**
     * Handle return from Stripe Connect onboarding
     */
    public function connectReturn($teacherId)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($teacherId);

        if ($teacher->stripe_connect_id) {
            try {
                $account = Account::retrieve($teacher->stripe_connect_id);

                if ($account->details_submitted && $account->payouts_enabled) {
                    $teacher->update(['stripe_account_status' => 'active']);
                    return redirect()->route('admin.salaries.index')
                        ->with('success', "{$teacher->name}'s Stripe account is now active and ready to receive payments.");
                } else {
                    $teacher->update(['stripe_account_status' => 'pending']);
                    return redirect()->route('admin.salaries.index')
                        ->with('warning', "{$teacher->name} needs to complete their Stripe account setup.");
                }
            } catch (\Exception $e) {
                return redirect()->route('admin.salaries.index')
                    ->with('error', 'Error checking account status: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.salaries.index');
    }

    /**
     * Handle refresh request from Stripe Connect
     */
    public function connectRefresh($teacherId)
    {
        return redirect()->route('admin.salaries.connect.create', $teacherId);
    }

    /**
     * Show bulk payment form
     */
    public function bulkPaymentForm()
    {
        // Only show teachers with active Stripe accounts (works for both test and live mode)
        $teachers = User::where('role', 'teacher')
            ->where('stripe_account_status', 'active')
            ->get();

        $currentMonth = now()->month;
        $currentYear = now()->year;

        return view('admin.salaries.bulk', compact('teachers', 'currentMonth', 'currentYear'));
    }

    /**
     * Process bulk salary payments via real Stripe Connect
     */
    public function processBulkPayment(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
            'payments' => 'required|array',
            'payments.*.teacher_id' => 'required|exists:users,id',
            'payments.*.amount' => 'required|numeric|min:0.01',
        ]);

        $successCount = 0;
        $failCount = 0;
        $errors = [];

        foreach ($validated['payments'] as $paymentData) {
            $teacher = User::find($paymentData['teacher_id']);
            $amount = (float) $paymentData['amount'];

            // Require active Stripe Connect account
            if (!$teacher || $teacher->stripe_account_status !== 'active' || empty($teacher->stripe_connect_id)) {
                $failCount++;
                $errors[] = "Skipped {$teacher?->name}: Stripe account not connected or active";
                continue;
            }

            // Check if already paid for this period
            $existing = TeacherSalary::where('teacher_id', $paymentData['teacher_id'])
                ->forPeriod($validated['month'], $validated['year'])
                ->where('status', 'completed')
                ->exists();

            if ($existing) {
                $failCount++;
                $errors[] = "Skipped {$teacher->name}: Already paid for this period";
                continue;
            }

            $salary = TeacherSalary::updateOrCreate(
                [
                    'teacher_id' => $paymentData['teacher_id'],
                    'month' => $validated['month'],
                    'year' => $validated['year'],
                ],
                [
                    'amount' => $amount,
                    'status' => 'pending',
                    'stripe_transfer_id' => null,
                    'transaction_id' => null,
                    'paid_at' => null,
                    'paid_by' => null,
                ]
            );

            try {
                $transfer = $this->createStripeTransfer($teacher, $salary, $amount, 'bulk');
            } catch (\Throwable $e) {
                $failCount++;
                $errors[] = "Failed {$teacher->name}: " . $e->getMessage();
                \Log::error('Bulk Salary Transfer Failed', [
                    'teacher_id' => $teacher->id,
                    'salary_id' => $salary->id,
                    'amount' => $amount,
                    'error' => $e->getMessage(),
                ]);
                $salary->update(['status' => 'pending']);
                continue;
            }

            $salary->update([
                'status' => 'completed',
                'stripe_transfer_id' => $transfer->id,
                'transaction_id' => $transfer->balance_transaction ?? $transfer->id,
                'amount' => $amount,
                'paid_at' => now(),
                'paid_by' => Auth::id(),
                'payment_method' => 'stripe_connect',
            ]);

            // Log the payment
            \Log::info('Bulk Salary Payment', [
                'teacher_id' => $teacher->id,
                'teacher_name' => $teacher->name,
                'amount' => $amount,
                'stripe_account' => $teacher->stripe_connect_id,
                'transfer_id' => $transfer->id,
                'balance_transaction' => $transfer->balance_transaction,
            ]);

            $successCount++;
        }

        $testLabel = $this->isTestMode() ? '[TEST MODE] ' : '';
        $message = "{$testLabel}Processed {$successCount} payments successfully.";
        if ($failCount > 0) {
            $message .= " {$failCount} payments failed.";
        }

        return redirect()->route('admin.salaries.index')
            ->with($failCount > 0 ? 'warning' : 'success', $message)
            ->with('errors', $errors);
    }

    /**
     * Export salary report
     */
    public function export(Request $request)
    {
        $query = TeacherSalary::with('teacher');

        if ($request->filled('month') && $request->filled('year')) {
            $query->forPeriod($request->month, $request->year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $salaries = $query->get();

        // Generate CSV
        $filename = 'salary_report_' . now()->format('Y_m_d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($salaries) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Teacher Name', 'Email', 'Amount', 'Month', 'Year', 'Status', 'Paid At', 'Transaction ID']);

            foreach ($salaries as $salary) {
                fputcsv($file, [
                    $salary->teacher->name,
                    $salary->teacher->email,
                    $salary->amount,
                    $salary->month_name,
                    $salary->year,
                    $salary->status,
                    $salary->paid_at?->format('Y-m-d H:i:s'),
                    $salary->transaction_id,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // =============================================
    // TEACHER-SIDE METHODS
    // =============================================

    /**
     * Show teacher's own salary history
     */
    public function teacherSalaryHistory()
    {
        $teacher = Auth::user();

        $salaries = TeacherSalary::where('teacher_id', $teacher->id)
            ->latest()
            ->paginate(15);

        $stats = [
            'total_received' => TeacherSalary::where('teacher_id', $teacher->id)
                ->where('status', 'completed')
                ->sum('amount'),
            'this_year' => TeacherSalary::where('teacher_id', $teacher->id)
                ->where('status', 'completed')
                ->whereYear('paid_at', now()->year)
                ->sum('amount'),
            'payment_count' => TeacherSalary::where('teacher_id', $teacher->id)
                ->where('status', 'completed')
                ->count(),
        ];

        return view('teacher.salary', compact('salaries', 'stats'));
    }

    /**
     * Teacher initiates Stripe Connect
     * Uses manual assignment in test mode (admin assigns account ID)
     * Or redirects to Stripe's hosted onboarding in production
     */
    public function teacherConnectStripe()
    {
        $teacher = Auth::user();

        // In test mode, show a message that admin needs to assign the account
        if ($this->isTestMode()) {
            // Check if already connected
            if (!empty($teacher->stripe_connect_id)) {
                return redirect()->route('teacher.salary')
                    ->with('info', 'Your Stripe account is already connected. Account ID: ' . $teacher->stripe_connect_id);
            }

            return redirect()->route('teacher.salary')
                ->with('info', 'In test mode, please ask your administrator to assign your Stripe test account ID from the admin panel.');
        }

        try {
            // Production mode - create real connected account
            if (empty($teacher->stripe_connect_id)) {
                $account = Account::create([
                    'type' => 'express',
                    'country' => 'US',
                    'email' => $teacher->email,
                    'capabilities' => [
                        'card_payments' => ['requested' => true],
                        'transfers' => ['requested' => true],
                    ],
                    'business_type' => 'individual',
                    'metadata' => [
                        'teacher_id' => $teacher->id,
                        'platform' => 'Academia',
                    ],
                ]);

                $teacher->update([
                    'stripe_connect_id' => $account->id,
                    'stripe_account_status' => 'pending',
                ]);
            }

            $accountLink = AccountLink::create([
                'account' => $teacher->stripe_connect_id,
                'refresh_url' => route('teacher.salary.connect.refresh'),
                'return_url' => route('teacher.salary.connect.return'),
                'type' => 'account_onboarding',
            ]);

            return redirect($accountLink->url);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            \Log::error('Stripe Connect Error: ' . $e->getMessage());
            return redirect()->route('teacher.salary')
                ->with('error', 'Failed to connect Stripe account: ' . $e->getMessage());
        }
    }

    /**
     * Admin assigns a Stripe Connect account ID to a teacher (for test mode)
     * In test mode, we just save the ID without verifying with Stripe API
     * This bypasses the "platform not set up" error
     */
    public function assignStripeAccount(Request $request, $teacherId)
    {
        $validated = $request->validate([
            'stripe_account_id' => 'required|string|starts_with:acct_',
        ]);

        $teacher = User::where('role', 'teacher')->findOrFail($teacherId);

        // In test mode, just save the account ID without Stripe API verification
        // This allows testing without full Stripe Connect platform setup
        $teacher->update([
            'stripe_connect_id' => $validated['stripe_account_id'],
            'stripe_account_status' => 'active', // Mark as active for testing
        ]);

        return back()->with('success', "Stripe account {$validated['stripe_account_id']} assigned to {$teacher->name}. Ready for salary payments.");
    }

    /**
     * Admin removes Stripe Connect account from a teacher
     */
    public function removeStripeAccount($teacherId)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($teacherId);

        $teacher->update([
            'stripe_connect_id' => null,
            'stripe_account_status' => null,
        ]);

        return back()->with('success', "Stripe account removed from {$teacher->name}.");
    }

    /**
     * Handle return from teacher's Stripe Connect onboarding
     * Check if account setup is complete
     */
    public function teacherConnectReturn()
    {
        $teacher = Auth::user();

        if ($teacher->stripe_connect_id) {
            try {
                $account = Account::retrieve($teacher->stripe_connect_id);

                if ($account->details_submitted) {
                    if ($account->payouts_enabled && $account->charges_enabled) {
                        $teacher->update(['stripe_account_status' => 'active']);
                        return redirect()->route('teacher.salary')
                            ->with('success', 'Your Stripe account is now connected and active! You can receive salary payments.');
                    } else {
                        $teacher->update(['stripe_account_status' => 'pending']);
                        return redirect()->route('teacher.salary')
                            ->with('warning', 'Your account details have been submitted. Stripe is reviewing your information. This usually takes a few minutes.');
                    }
                } else {
                    return redirect()->route('teacher.salary')
                        ->with('warning', 'Please complete your Stripe account setup to receive payments.');
                }
            } catch (\Stripe\Exception\ApiErrorException $e) {
                \Log::error('Stripe Account Check Error: ' . $e->getMessage());
                return redirect()->route('teacher.salary')
                    ->with('error', 'Error checking account status: ' . $e->getMessage());
            }
        }

        return redirect()->route('teacher.salary');
    }

    /**
     * Handle refresh request for teacher's Stripe Connect
     * Re-initiate onboarding if needed
     */
    public function teacherConnectRefresh()
    {
        return redirect()->route('teacher.salary.connect');
    }
}

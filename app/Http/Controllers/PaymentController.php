<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * Show the checkout page for a course
     */
    public function checkout($courseId)
    {
        $course = Course::findOrFail($courseId);
        $user = Auth::user();

        // Check if already enrolled
        $isEnrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($isEnrolled) {
            return redirect()->route('courses.show', $courseId)
                ->with('info', 'You are already enrolled in this course.');
        }

        return view('payments.checkout', compact('course'));
    }

    /**
     * Create a Stripe Checkout Session
     */
    public function createCheckoutSession(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        $user = Auth::user();

        // Check if already enrolled
        $isEnrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($isEnrolled) {
            return redirect()->route('courses.show', $courseId)
                ->with('info', 'You are already enrolled in this course.');
        }

        try {
            // Convert BDT to smallest unit (paisa) - Stripe uses smallest currency unit
            // Note: Stripe doesn't support BDT directly, so we'll use USD
            // For production, you'd need to handle currency conversion
            $amountInCents = (int) ($course->price * 100);

            $checkoutSession = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $course->title,
                            'description' => $course->description ?? 'Course enrollment',
                        ],
                        'unit_amount' => $amountInCents,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.success', ['course' => $courseId]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancel', ['course' => $courseId]),
                'customer_email' => $user->email,
                'metadata' => [
                    'course_id' => $course->id,
                    'user_id' => $user->id,
                ],
            ]);

            return redirect($checkoutSession->url);
        } catch (\Exception $e) {
            return back()->with('error', 'Payment initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful payment
     */
    public function success(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        $user = Auth::user();
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('courses.show', $courseId)
                ->with('error', 'Invalid payment session.');
        }

        try {
            // Retrieve the session from Stripe
            $session = StripeSession::retrieve($sessionId);

            // Verify payment was successful
            if ($session->payment_status !== 'paid') {
                return redirect()->route('courses.show', $courseId)
                    ->with('error', 'Payment was not completed.');
            }

            // Check if already enrolled (prevent double enrollment)
            $existingEnrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if ($existingEnrollment) {
                return redirect()->route('student.dashboard')
                    ->with('info', 'You are already enrolled in this course.');
            }

            // Check if payment already recorded
            $existingPayment = Payment::where('transaction_id', $session->payment_intent)->first();
            if ($existingPayment) {
                return redirect()->route('student.dashboard')
                    ->with('success', 'You are already enrolled in this course!');
            }

            // Create enrollment
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'status' => 'active',
            ]);

            // Create payment record
            Payment::create([
                'user_id' => $user->id,
                'enrollment_id' => $enrollment->id,
                'amount' => $course->price,
                'transaction_id' => $session->payment_intent,
                'status' => 'completed',
                'stripe_session_id' => $sessionId,
                'payment_method' => 'stripe',
            ]);

            return redirect()->route('student.dashboard')
                ->with('success', 'Payment successful! You are now enrolled in "' . $course->title . '".');
        } catch (\Exception $e) {
            return redirect()->route('courses.show', $courseId)
                ->with('error', 'Error processing payment: ' . $e->getMessage());
        }
    }

    /**
     * Handle cancelled payment
     */
    public function cancel($courseId)
    {
        return redirect()->route('courses.show', $courseId)
            ->with('info', 'Payment was cancelled. You can try again when you\'re ready.');
    }
}

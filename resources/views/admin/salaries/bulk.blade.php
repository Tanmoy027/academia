<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Bulk Salary Payment') }}
            </h2>
            <a href="{{ route('admin.salaries.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-300 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Salaries
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            {{-- Flash Messages --}}
            @if (session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-red-700">{{ session('error') }}</span>
                </div>
            </div>
            @endif

            @if($teachers->count() === 0)
            <div class="bg-amber-50 rounded-xl p-6 text-center">
                <svg class="w-16 h-16 mx-auto text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-amber-800">No Teachers with Active Stripe Accounts</h3>
                <p class="mt-2 text-amber-600">Bulk payment requires teachers to have connected Stripe accounts.</p>
                <a href="{{ route('admin.salaries.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition">
                    Go Back & Connect Teachers
                </a>
            </div>
            @else
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-pink-50">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Process Multiple Salary Payments
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">Pay multiple teachers at once for the same period</p>
                </div>

                <form action="{{ route('admin.salaries.bulk.process') }}" method="POST" class="p-6">
                    @csrf

                    {{-- Month and Year Selection --}}
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="month" class="block text-sm font-medium text-gray-700 mb-2">
                                Payment Month <span class="text-red-500">*</span>
                            </label>
                            <select name="month" id="month" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                    @endfor
                            </select>
                        </div>
                        <div>
                            <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                                Payment Year <span class="text-red-500">*</span>
                            </label>
                            <select name="year" id="year" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @for($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    {{-- Teachers List --}}
                    <div class="border rounded-xl overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <label class="flex items-center">
                                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="ml-2 text-xs font-medium text-gray-500 uppercase">Select All</span>
                                        </label>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Base Salary</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Amount</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($teachers as $index => $teacher)
                                <tr class="hover:bg-gray-50 teacher-row">
                                    <td class="px-6 py-4">
                                        <input type="checkbox"
                                            name="payments[{{ $index }}][selected]"
                                            class="teacher-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            data-index="{{ $index }}">
                                        <input type="hidden" name="payments[{{ $index }}][teacher_id]" value="{{ $teacher->id }}">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span class="text-indigo-600 font-semibold">{{ substr($teacher->name, 0, 1) }}</span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $teacher->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $teacher->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        ৳{{ number_format($teacher->base_salary ?? 0, 2) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">৳</span>
                                            </div>
                                            <input type="number"
                                                name="payments[{{ $index }}][amount]"
                                                step="0.01"
                                                min="0.01"
                                                value="{{ $teacher->base_salary ?? '' }}"
                                                class="amount-input w-32 pl-8 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                placeholder="0.00">
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Total Summary --}}
                    <div class="mt-6 p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-gray-600">Selected Teachers:</span>
                                <span id="selected-count" class="ml-2 font-semibold text-indigo-600">0</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Total Amount:</span>
                                <span id="total-amount" class="ml-2 text-xl font-bold text-indigo-600">৳0.00</span>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="mt-6 flex items-center justify-end gap-4">
                        <a href="{{ route('admin.salaries.index') }}"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button type="submit"
                            id="submit-btn"
                            disabled
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            Process Selected Payments
                        </button>
                    </div>
                </form>
            </div>

            {{-- Warning Card --}}
            <div class="mt-6 bg-amber-50 rounded-xl p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-amber-800">Important</h3>
                        <div class="mt-2 text-sm text-amber-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>All payments will be processed immediately via Stripe</li>
                                <li>Ensure you have sufficient balance in your Stripe account</li>
                                <li>Each payment will be recorded with the selected month/year</li>
                                <li>Teachers who already received payment for this period will be skipped</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.teacher-checkbox');
            const amountInputs = document.querySelectorAll('.amount-input');
            const selectedCount = document.getElementById('selected-count');
            const totalAmount = document.getElementById('total-amount');
            const submitBtn = document.getElementById('submit-btn');

            function updateTotals() {
                let count = 0;
                let total = 0;

                checkboxes.forEach((checkbox, index) => {
                    if (checkbox.checked) {
                        count++;
                        const amount = parseFloat(amountInputs[index].value) || 0;
                        total += amount;
                    }
                });

                selectedCount.textContent = count;
                totalAmount.textContent = '৳' + total.toFixed(2);
                submitBtn.disabled = count === 0;
            }

            selectAll.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateTotals();
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateTotals);
            });

            amountInputs.forEach(input => {
                input.addEventListener('input', updateTotals);
            });
        });
    </script>
</x-app-layout>
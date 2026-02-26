<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Create Salary Payment') }}
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
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- Error Messages --}}
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

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Salary Payment Details
                    </h3>
                </div>

                <form action="{{ route('admin.salaries.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf

                    {{-- Teacher Selection --}}
                    <div>
                        <label for="teacher_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Select Teacher <span class="text-red-500">*</span>
                        </label>
                        <select name="teacher_id" id="teacher_id" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('teacher_id') border-red-500 @enderror">
                            <option value="">Choose a teacher...</option>
                            @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}"
                                data-salary="{{ $teacher->base_salary ?? 0 }}"
                                data-status="{{ $teacher->stripe_account_status }}"
                                {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                                @if($teacher->stripe_account_status === 'active')
                                ✓
                                @elseif($teacher->stripe_account_status === 'pending')
                                (Stripe Pending)
                                @else
                                (No Stripe)
                                @endif
                            </option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p id="stripe-warning" class="mt-2 text-sm text-amber-600 hidden">
                            ⚠️ This teacher hasn't connected their Stripe account yet. Payment cannot be processed until they do.
                        </p>
                    </div>

                    {{-- Amount --}}
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Amount (BDT) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">৳</span>
                            </div>
                            <input type="number" name="amount" id="amount" step="0.01" min="0.01"
                                value="{{ old('amount') }}" required
                                class="w-full pl-8 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('amount') border-red-500 @enderror"
                                placeholder="0.00">
                        </div>
                        @error('amount')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Month and Year --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="month" class="block text-sm font-medium text-gray-700 mb-2">
                                Month <span class="text-red-500">*</span>
                            </label>
                            <select name="month" id="month" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ old('month', $currentMonth) == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                    @endfor
                            </select>
                        </div>
                        <div>
                            <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                                Year <span class="text-red-500">*</span>
                            </label>
                            <select name="year" id="year" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @for($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ old('year', $currentYear) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description (Optional)
                        </label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Add any notes about this payment...">{{ old('description') }}</textarea>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('admin.salaries.index') }}"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            Create Salary Record
                        </button>
                    </div>
                </form>
            </div>

            {{-- Info Card --}}
            <div class="mt-6 bg-blue-50 rounded-xl p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">How it works</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Create a salary record with the payment amount and period</li>
                                <li>The record will be saved with "Pending" status</li>
                                <li>Process the payment from the salary list to transfer funds via Stripe</li>
                                <li>Teachers must have a connected Stripe account to receive payments</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('teacher_id').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const status = selected.dataset.status;
            const salary = selected.dataset.salary;
            const warning = document.getElementById('stripe-warning');
            const amountField = document.getElementById('amount');

            // Show warning if no Stripe account
            if (status !== 'active' && this.value) {
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }

            // Auto-fill base salary if available
            if (salary && parseFloat(salary) > 0) {
                amountField.value = parseFloat(salary).toFixed(2);
            }
        });
    </script>
</x-app-layout>
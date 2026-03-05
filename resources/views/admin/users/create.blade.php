<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Validation Errors -->
            @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <!-- Heroicon name: mini/x-circle -->
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul role="list" class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf

                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Teacher Information</h3>
                                <p class="mt-1 text-sm text-gray-500">Admins can only create teacher accounts from this panel. Share login credentials with the teacher securely after creating the account.</p>
                            </div>

                            <hr class="border-gray-200">

                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">

                                <!-- Name -->
                                <div class="sm:col-span-3">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="text" name="name" id="name" autocomplete="name" required value="{{ old('name') }}" placeholder="John Doe"
                                            class="block w-full rounded-md border-gray-300 pl-3 focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2">
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Role</label>
                                    <div class="mt-2 px-3 py-2 rounded-md bg-indigo-50 text-sm text-indigo-700 font-semibold inline-flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        Teacher (default)
                                    </div>
                                    <input type="hidden" name="role" value="teacher">
                                </div>

                                <!-- Email -->
                                <div class="sm:col-span-6">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="email" name="email" id="email" autocomplete="email" required value="{{ old('email') }}" placeholder="user@example.com"
                                            class="block w-full rounded-md border-gray-300 pl-3 focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2">
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="sm:col-span-3">
                                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="password" name="password" id="password" required
                                            class="block w-full rounded-md border-gray-300 pl-3 focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2">
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div class="sm:col-span-3">
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="password" name="password_confirmation" id="password_confirmation" required
                                            class="block w-full rounded-md border-gray-300 pl-3 focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-8 pt-5 border-t border-gray-200 flex items-center justify-end">
                            <a href="{{ route('admin.users') }}" class="mr-3 bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
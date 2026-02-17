<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Course') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Validation Errors -->
            @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Whoops!</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Course Title:</label>
                            <input type="text" name="title" id="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description:</label>
                            <textarea name="description" id="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="teacher_id" class="block text-gray-700 text-sm font-bold mb-2">Assign Teacher:</label>
                            <select name="teacher_id" id="teacher_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">-- Select Teacher --</option>
                                @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Price (৳):</label>
                            <input type="number" name="price" id="price" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-gray-700 text-sm font-bold mb-2">Start Date: <span class="text-red-500">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                <p class="text-xs text-gray-500 mt-1">Students cannot enroll after this date</p>
                            </div>
                            <div>
                                <label for="end_date" class="block text-gray-700 text-sm font-bold mb-2">End Date: <span class="text-red-500">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                <p class="text-xs text-gray-500 mt-1">When the course will be completed</p>
                            </div>
                        </div>

                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded">
                            <p class="text-sm text-blue-700">
                                <i class="bi bi-info-circle me-1"></i>
                                <strong>Duration:</strong> Will be calculated automatically based on start and end dates.
                            </p>
                        </div>

                        <!-- Class Type Selection -->
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Class Type: <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-3 gap-4">
                                <label class="relative flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors" id="class-type-online-label">
                                    <input type="radio" name="class_type" value="online" class="sr-only" id="class-type-online" onchange="toggleClassTypeUI()">
                                    <div class="text-center">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span class="font-semibold">Online</span>
                                        <p class="text-xs text-gray-500 mt-1">Virtual classes only</p>
                                    </div>
                                </label>
                                <label class="relative flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors border-blue-500 bg-blue-50" id="class-type-offline-label">
                                    <input type="radio" name="class_type" value="offline" class="sr-only" id="class-type-offline" checked onchange="toggleClassTypeUI()">
                                    <div class="text-center">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span class="font-semibold">Offline</span>
                                        <p class="text-xs text-gray-500 mt-1">In-person classes only</p>
                                    </div>
                                </label>
                                <label class="relative flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors" id="class-type-hybrid-label">
                                    <input type="radio" name="class_type" value="hybrid" class="sr-only" id="class-type-hybrid" onchange="toggleClassTypeUI()">
                                    <div class="text-center">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                        </svg>
                                        <span class="font-semibold">Both (Hybrid)</span>
                                        <p class="text-xs text-gray-500 mt-1">Mix of online & offline</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Online Schedule (shown when online or hybrid is selected) -->
                        <div class="mb-6 hidden" id="online-schedule-section">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="font-bold text-blue-800 mb-4">
                                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Online Class Schedule
                                </h4>
                                <span class="block text-xs text-gray-500 mb-2 font-bold uppercase tracking-wide">Select Days</span>
                                <div class="flex flex-wrap gap-4 mb-4">
                                    <label class="inline-flex items-center"><input type="checkbox" name="online_days[]" value="Mon" class="form-checkbox text-blue-600"><span class="ml-2">Mon</span></label>
                                    <label class="inline-flex items-center"><input type="checkbox" name="online_days[]" value="Tue" class="form-checkbox text-blue-600"><span class="ml-2">Tue</span></label>
                                    <label class="inline-flex items-center"><input type="checkbox" name="online_days[]" value="Wed" class="form-checkbox text-blue-600"><span class="ml-2">Wed</span></label>
                                    <label class="inline-flex items-center"><input type="checkbox" name="online_days[]" value="Thu" class="form-checkbox text-blue-600"><span class="ml-2">Thu</span></label>
                                    <label class="inline-flex items-center"><input type="checkbox" name="online_days[]" value="Fri" class="form-checkbox text-blue-600"><span class="ml-2">Fri</span></label>
                                    <label class="inline-flex items-center"><input type="checkbox" name="online_days[]" value="Sat" class="form-checkbox text-blue-600"><span class="ml-2">Sat</span></label>
                                    <label class="inline-flex items-center"><input type="checkbox" name="online_days[]" value="Sun" class="form-checkbox text-blue-600"><span class="ml-2">Sun</span></label>
                                </div>
                                <div class="flex items-center">
                                    <span class="block text-xs text-gray-500 mr-2 font-bold uppercase tracking-wide">Select Time</span>
                                    <input type="time" name="online_time" class="shadow appearance-none border rounded py-1 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                            </div>
                        </div>

                        <!-- Offline Schedule (shown when offline or hybrid is selected) -->
                        <div class="mb-6" id="offline-schedule-section">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <h4 class="font-bold text-green-800 mb-4">
                                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Offline Class Schedule
                                </h4>
                                <span class="block text-xs text-gray-500 mb-2 font-bold uppercase tracking-wide">Select Days</span>
                                <div class="flex flex-wrap gap-4 mb-4">
                                    <label class="inline-flex items-center"><input type="checkbox" name="offline_days[]" value="Mon" class="form-checkbox text-green-600"><span class="ml-2">Mon</span></label>
                                    <label class="inline-flex items-center"><input type="checkbox" name="offline_days[]" value="Tue" class="form-checkbox text-green-600"><span class="ml-2">Tue</span></label>
                                    <label class="inline-flex items-center"><input type="checkbox" name="offline_days[]" value="Wed" class="form-checkbox text-green-600"><span class="ml-2">Wed</span></label>
                                    <label class="inline-flex items-center"><input type="checkbox" name="offline_days[]" value="Thu" class="form-checkbox text-green-600"><span class="ml-2">Thu</span></label>
                                    <label class="inline-flex items-center"><input type="checkbox" name="offline_days[]" value="Fri" class="form-checkbox text-green-600"><span class="ml-2">Fri</span></label>
                                    <label class="inline-flex items-center"><input type="checkbox" name="offline_days[]" value="Sat" class="form-checkbox text-green-600"><span class="ml-2">Sat</span></label>
                                    <label class="inline-flex items-center"><input type="checkbox" name="offline_days[]" value="Sun" class="form-checkbox text-green-600"><span class="ml-2">Sun</span></label>
                                </div>
                                <div class="flex items-center">
                                    <span class="block text-xs text-gray-500 mr-2 font-bold uppercase tracking-wide">Select Time</span>
                                    <input type="time" name="offline_time" class="shadow appearance-none border rounded py-1 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Course Image:</label>
                            <input type="file" name="image" id="image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <div class="mb-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="form-checkbox h-5 w-5 text-green-600" checked>
                                <span class="ml-2 text-gray-700 font-bold">Is Active?</span>
                            </label>
                            <p class="text-sm text-gray-500 ml-7">Uncheck to hide this course from students.</p>
                        </div>

                        <div class="flex items-center justify-end mt-6 pt-4 border-t border-gray-200">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-3 px-6 rounded shadow-lg transform transition hover:scale-105 focus:outline-none focus:shadow-outline">
                                Create Course
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleClassTypeUI() {
            const online = document.getElementById('class-type-online');
            const offline = document.getElementById('class-type-offline');
            const hybrid = document.getElementById('class-type-hybrid');
            const onlineSection = document.getElementById('online-schedule-section');
            const offlineSection = document.getElementById('offline-schedule-section');

            // Reset all labels
            document.getElementById('class-type-online-label').classList.remove('border-blue-500', 'bg-blue-50');
            document.getElementById('class-type-offline-label').classList.remove('border-blue-500', 'bg-blue-50');
            document.getElementById('class-type-hybrid-label').classList.remove('border-blue-500', 'bg-blue-50');

            // Highlight selected and show/hide schedule sections
            if (online.checked) {
                document.getElementById('class-type-online-label').classList.add('border-blue-500', 'bg-blue-50');
                onlineSection.classList.remove('hidden');
                offlineSection.classList.add('hidden');
            } else if (offline.checked) {
                document.getElementById('class-type-offline-label').classList.add('border-blue-500', 'bg-blue-50');
                onlineSection.classList.add('hidden');
                offlineSection.classList.remove('hidden');
            } else if (hybrid.checked) {
                document.getElementById('class-type-hybrid-label').classList.add('border-blue-500', 'bg-blue-50');
                onlineSection.classList.remove('hidden');
                offlineSection.classList.remove('hidden');
            }
        }
    </script>
</x-app-layout>
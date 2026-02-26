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

                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Online Class Schedule</label>
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
        // No additional JS is required for schedule inputs now that classes are online-only
    </script>
</x-app-layout>
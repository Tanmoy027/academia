<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Course') }}
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
                    <form action="{{ route('admin.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Course Details</h3>
                                <p class="mt-1 text-sm text-gray-500">Update the information for {{ $course->title }}.</p>
                            </div>

                            <hr class="border-gray-200">

                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">

                                <!-- Title -->
                                <div class="sm:col-span-4">
                                    <label for="title" class="block text-sm font-medium text-gray-700">Course Title</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="text" name="title" id="title" required value="{{ old('title', $course->title) }}"
                                            class="block w-full rounded-md border-gray-300 pl-3 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2">
                                    </div>
                                </div>

                                <!-- Teacher -->
                                <div class="sm:col-span-2">
                                    <label for="teacher_id" class="block text-sm font-medium text-gray-700">Assigned Teacher</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <select name="teacher_id" id="teacher_id"
                                            class="block w-full rounded-md border-gray-300 pl-3 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2">
                                            <option value="">-- Select Teacher --</option>
                                            @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ $course->teacher_id == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="sm:col-span-6">
                                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                    <div class="mt-1">
                                        <textarea id="description" name="description" rows="3"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3">{{ old('description', $course->description) }}</textarea>
                                    </div>
                                </div>

                                <!-- Price -->
                                <div class="sm:col-span-2">
                                    <label for="price" class="block text-sm font-medium text-gray-700">Price (৳)</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">৳</span>
                                        </div>
                                        <input type="number" name="price" id="price" step="0.01" required value="{{ old('price', $course->price) }}"
                                            class="block w-full rounded-md border-gray-300 pl-7 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2">
                                    </div>
                                </div>

                                <!-- Duration -->
                                <div class="sm:col-span-2">
                                    <label for="duration" class="block text-sm font-medium text-gray-700">Duration</label>
                                    <input type="text" name="duration" id="duration" placeholder="e.g. 10 Weeks" value="{{ old('duration', $course->duration) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 pl-3">
                                </div>

                                <!-- Schedule -->
                                <div class="sm:col-span-2">
                                    <label for="schedule" class="block text-sm font-medium text-gray-700">Schedule</label>
                                    <input type="text" name="schedule" id="schedule" placeholder="e.g. Mon/Wed 10am" value="{{ old('schedule', $course->schedule) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 pl-3">
                                </div>

                                <!-- Image Upload -->
                                <div class="sm:col-span-6">
                                    <label class="block text-sm font-medium text-gray-700">Course Image</label>
                                    <div class="mt-2 flex items-center gap-x-4">
                                        @if($course->image_url)
                                        <div class="relative group">
                                            <img src="{{ $course->image_url }}" alt="Current course image" class="h-24 w-40 object-cover rounded-lg border border-gray-200">
                                            <span class="absolute bottom-0 right-0 bg-white text-xs px-1 rounded border shadow-sm">Current</span>
                                        </div>
                                        @else
                                        <div class="h-24 w-40 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50 text-gray-400">
                                            No Image
                                        </div>
                                        @endif
                                        <div class="flex-1">
                                            <input type="file" name="image" id="image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                            <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 2MB. Upload to replace current image.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Is Active Toggle -->
                                <div class="sm:col-span-6">
                                    <div class="flex items-start">
                                        <div class="flex h-5 items-center">
                                            <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $course->is_active) ? 'checked' : '' }}
                                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="is_active" class="font-medium text-gray-700">Active Course</label>
                                            <p class="text-gray-500">Uncheck to hide this course from students.</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-8 pt-5 border-t border-gray-200 flex items-center justify-end">
                            <a href="{{ route('admin.courses') }}" class="mr-3 bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
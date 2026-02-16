<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Notice') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold">Edit Notice</h3>
                        <a href="{{ route('admin.notices') }}" class="text-indigo-600 hover:text-indigo-800">
                            &larr; Back to Notices
                        </a>
                    </div>

                    <form action="{{ route('admin.notices.update', $notice->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                <input type="text" name="title" id="title" value="{{ old('title', $notice->title) }}" class="w-full border rounded p-2 @error('title') border-red-500 @enderror" required>
                                @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select name="category" id="category" class="w-full border rounded p-2" required>
                                    @foreach($categories as $value => $label)
                                    <option value="{{ $value }}" {{ old('category', $notice->category) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                            <textarea name="content" id="content" class="w-full border rounded p-2 @error('content') border-red-500 @enderror" rows="6" required>{{ old('content', $notice->content) }}</textarea>
                            @error('content')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <label for="target_role" class="block text-sm font-medium text-gray-700 mb-1">Target Audience</label>
                                <select name="target_role" id="target_role" class="w-full border rounded p-2">
                                    <option value="all" {{ old('target_role', $notice->target_role) == 'all' ? 'selected' : '' }}>Everyone (Students & Teachers)</option>
                                    <option value="student" {{ old('target_role', $notice->target_role) == 'student' ? 'selected' : '' }}>Students Only</option>
                                    <option value="teacher" {{ old('target_role', $notice->target_role) == 'teacher' ? 'selected' : '' }}>Teachers Only</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="is_active" class="form-checkbox h-5 w-5 text-indigo-600" {{ old('is_active', $notice->is_active) ? 'checked' : '' }}>
                                    <span class="ml-2 text-gray-700">Active (Visible to users)</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex space-x-4">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded font-medium">
                                Update Notice
                            </button>
                            <a href="{{ route('admin.notices') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded font-medium">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Preview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Notice Info</h4>
                    <p class="text-xs text-gray-400">Created: {{ $notice->created_at->format('M d, Y h:i A') }}</p>
                    <p class="text-xs text-gray-400">Last Updated: {{ $notice->updated_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
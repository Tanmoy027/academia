<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <!-- Students -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Total Students</div>
                    <div class="text-2xl font-bold">{{ $stats['total_students'] }}</div>
                </div>
                <!-- Teachers -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Total Teachers</div>
                    <div class="text-2xl font-bold">{{ $stats['total_teachers'] }}</div>
                </div>
                <!-- Courses -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Active Courses</div>
                    <div class="text-2xl font-bold">{{ $stats['total_courses'] }}</div>
                </div>
                <!-- Revenue -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Total Revenue</div>
                    <div class="text-2xl font-bold">৳{{ number_format($stats['total_revenue'], 2) }}</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Management Links -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Management</h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.users') }}" class="block px-4 py-2 bg-blue-50 text-blue-700 rounded hover:bg-blue-100">
                            Manage Users (Students & Teachers)
                        </a>
                        <a href="{{ route('admin.courses') }}" class="block px-4 py-2 bg-green-50 text-green-700 rounded hover:bg-green-100">
                            Manage Courses & Schedules
                        </a>
                        <a href="{{ route('admin.notices') }}" class="block px-4 py-2 bg-purple-50 text-purple-700 rounded hover:bg-purple-100">
                            📢 Notice & Announcements
                        </a>
                    </div>
                </div>

                <!-- Recent Enrollments -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Recent Enrollments</h3>
                    @if($stats['recent_enrollments']->isEmpty())
                    <p class="text-gray-500">No recent enrollments.</p>
                    @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($stats['recent_enrollments'] as $enrollment)
                        <li class="py-2">
                            <div class="text-sm font-medium text-gray-900">{{ $enrollment->user->name }}</div>
                            <div class="text-sm text-gray-500">Enrolled in {{ $enrollment->course->title }}</div>
                            <div class="text-xs text-gray-400">{{ $enrollment->created_at->diffForHumans() }}</div>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
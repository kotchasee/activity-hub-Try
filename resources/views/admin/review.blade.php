<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Review - Statistics & Analytics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- General Stats -->
                    <h3 class="text-lg font-semibold mb-4">General Statistics</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <div class="bg-blue-100 dark:bg-blue-900 p-4 rounded">
                            <h4 class="font-bold">Total Activities</h4>
                            <p class="text-2xl">{{ $stats['total_activities'] }}</p>
                        </div>
                        <div class="bg-green-100 dark:bg-green-900 p-4 rounded">
                            <h4 class="font-bold">Approved Activities</h4>
                            <p class="text-2xl">{{ $stats['approved_activities'] }}</p>
                        </div>
                        <div class="bg-yellow-100 dark:bg-yellow-900 p-4 rounded">
                            <h4 class="font-bold">Pending Activities</h4>
                            <p class="text-2xl">{{ $stats['pending_activities'] }}</p>
                        </div>
                        <div class="bg-purple-100 dark:bg-purple-900 p-4 rounded">
                            <h4 class="font-bold">Total Views</h4>
                            <p class="text-2xl">{{ $stats['total_views'] }}</p>
                        </div>
                        <div class="bg-red-100 dark:bg-red-900 p-4 rounded">
                            <h4 class="font-bold">Total Registrations</h4>
                            <p class="text-2xl">{{ $stats['total_registrations'] }}</p>
                        </div>
                        <div class="bg-indigo-100 dark:bg-indigo-900 p-4 rounded">
                            <h4 class="font-bold">Avg Views per Activity</h4>
                            <p class="text-2xl">{{ $stats['avg_views_per_activity'] }}</p>
                        </div>
                    </div>

                    <!-- Tag Stats -->
                    <h3 class="text-lg font-semibold mb-4">Tag Statistics (Activities Count)</h3>
                    <div class="overflow-x-auto mb-8">
                        <table class="min-w-full bg-white dark:bg-gray-800">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 border">Tag Name</th>
                                    <th class="px-4 py-2 border">Activities Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tagStats as $tag)
                                <tr>
                                    <td class="px-4 py-2 border">{{ $tag->name }}</td>
                                    <td class="px-4 py-2 border">{{ $tag->activities_count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Tag View Stats -->
                    <h3 class="text-lg font-semibold mb-4">Tag View Statistics</h3>
                    <div class="overflow-x-auto mb-8">
                        <table class="min-w-full bg-white dark:bg-gray-800">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 border">Tag Name</th>
                                    <th class="px-4 py-2 border">Activity Count</th>
                                    <th class="px-4 py-2 border">Total Views</th>
                                    <th class="px-4 py-2 border">Avg Views</th>
                                    <th class="px-4 py-2 border">Total Registrations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tagViewStats as $stat)
                                <tr>
                                    <td class="px-4 py-2 border">{{ $stat['tag_name'] }}</td>
                                    <td class="px-4 py-2 border">{{ $stat['activity_count'] }}</td>
                                    <td class="px-4 py-2 border">{{ $stat['total_views'] }}</td>
                                    <td class="px-4 py-2 border">{{ $stat['avg_views'] }}</td>
                                    <td class="px-4 py-2 border">{{ $stat['total_registrations'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Top Viewed Activities -->
                    <h3 class="text-lg font-semibold mb-4">Top Viewed Activities</h3>
                    <div class="overflow-x-auto mb-8">
                        <table class="min-w-full bg-white dark:bg-gray-800">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 border">Title</th>
                                    <th class="px-4 py-2 border">Date</th>
                                    <th class="px-4 py-2 border">View Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topViewedActivities as $activity)
                                <tr>
                                    <td class="px-4 py-2 border">{{ $activity->title }}</td>
                                    <td class="px-4 py-2 border">{{ $activity->date }}</td>
                                    <td class="px-4 py-2 border">{{ $activity->view_count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Top Registration Activities -->
                    <h3 class="text-lg font-semibold mb-4">Top Registration Activities</h3>
                    <div class="overflow-x-auto mb-8">
                        <table class="min-w-full bg-white dark:bg-gray-800">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 border">Title</th>
                                    <th class="px-4 py-2 border">Date</th>
                                    <th class="px-4 py-2 border">Registrations Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topRegistrationActivities as $activity)
                                <tr>
                                    <td class="px-4 py-2 border">{{ $activity->title }}</td>
                                    <td class="px-4 py-2 border">{{ $activity->date }}</td>
                                    <td class="px-4 py-2 border">{{ $activity->registrations_count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
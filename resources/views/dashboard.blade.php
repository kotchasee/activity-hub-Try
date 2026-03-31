<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200">
            All Activities
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- 🔥 Top 3 Hot Activities -->
            <div class="mb-10">
                <h3 class="text-2xl font-bold mb-6 text-center">🔥 Top 3 Hot Activities</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($hotActivities as $activity)
                    <div class="bg-gradient-to-r from-red-500 to-orange-500 text-white rounded-lg p-6 shadow-lg">
                        <h4 class="text-xl font-semibold mb-2">{{ $activity->title }}</h4>
                        <p class="text-sm mb-2">{{ $activity->date }}</p>
                        <p class="text-2xl font-bold">{{ $activity->view_count }} views</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Monthly Calendar -->
            <div class="mb-10">
                <h3 class="text-2xl font-bold mb-6 text-center">Monthly Calendar</h3>
                <div class="space-y-4">
                    @forelse($monthlyActivities as $activity)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border-l-4 border-blue-500">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $activity->title }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $activity->description }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-500">Location: {{ $activity->location }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $activity->date }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-500">{{ $activity->view_count }} views</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-gray-500 dark:text-gray-400">No activities this month.</p>
                    @endforelse
                </div>
            </div>

            <!--  Search Form -->
            <form method="GET" action="{{ route('dashboard') }}" class="mb-6 flex gap-2 flex-wrap">

                <input type="text" name="search" placeholder="search..."
                    value="{{ request('search') }}"
                    class="border rounded px-3 py-2">

                <input type="date" name="date"
                    value="{{ request('date') }}"
                    class="border rounded px-3 py-2">

                <select name="tag" class="border rounded px-3 py-2">
                    <option value="">Tags</option>
                    @foreach($tags as $tag)
                        <option value="{{ $tag->id }}"
                            {{ request('tag') == $tag->id ? 'selected' : '' }}>
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="bg-blue-500 text-white px-4 rounded">
                   search
                </button>

            </form>

            <!-- 🔍 Result Text -->
            @if(request()->hasAny(['search','tag','date']))
                <p class="mb-4 text-gray-600">
                    🔍 แสดงผลการค้นหา
                </p>
            @endif

            <!-- Activities Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                @forelse($activities as $activity)

                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden hover:scale-105 transition">

                    @if($activity->image)
                        <img src="{{ asset('storage/'.$activity->image) }}"
                            class="w-full h-48 object-cover">
                    @endif

                    <div class="p-5">

                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">
                            {{ $activity->title }}
                        </h3>

                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            📅 {{ $activity->date }}
                        </p>

                        <p class="text-sm text-red-500 font-bold">
                            ⏳ ปิดรับสมัคร: {{ $activity->registration_deadline }}
                        </p>

                        <!-- Tags -->
                        <div class="mt-2">
                            @foreach($activity->tags as $tag)
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1">
                                    #{{ $tag->name }}
                                </span>
                            @endforeach
                        </div>

                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                            📍 {{ $activity->location }}
                        </p>

                        <a href="/activities/{{ $activity->id }}"
                            class="text-blue-600 font-semibold hover:underline mt-2 inline-block">
                            View Details
                        </a>

                    </div>
                </div>

                @empty
                    <p class="text-gray-500">ไม่พบกิจกรรม</p>
                @endforelse

            </div>

        </div>
    </div>

</x-app-layout>
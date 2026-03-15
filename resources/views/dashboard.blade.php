<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            All Activities
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if(session('success'))
                    <div style="background:#d1fae5;padding:10px;margin-bottom:20px;">
                        {{ session('success') }}
                    </div>
                @endif

            <!-- Activities Container -->
            <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:20px;">

                @foreach($activities as $activity)

                <!-- Activity Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        <!-- Activity Image -->
                        <div>
                            @if($activity->image)
                            <img src="{{ asset('storage/'.$activity->image) }}" alt="activity image" style="width:100%;">
                            @endif
                        </div>

                        <!-- Activity Info -->
                        <div>

                            <h3 style="font-size:20px; font-weight:bold;">
                                {{ $activity->title }}
                            </h3>

                            <p>
                                Date: {{ $activity->date }}
                            </p>

                            <p>
                                Location: {{ $activity->location }}
                            </p>

                            <a href="/activities/{{ $activity->id }}">
                                View Details
                            </a>

                        </div>

                    </div>

                </div>

                @endforeach

            </div>

        </div>

    </div>
</x-app-layout>
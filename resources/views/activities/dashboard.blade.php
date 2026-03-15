<x-app-layout>
    <x-slot name="header">
        <h2>
            All Activities
        </h2>
    </x-slot>

    <div>

        <h1>All Activities</h1>

        <!-- Activities Container -->
        <div>

            @foreach($activities as $activity)

            <!-- Activity Card -->
            <div style="border:1px solid black; padding:10px; margin:10px;">

                @if(session('success'))
            <div>
                {{ session('success') }}
            </div>
                @endif

                @if($activity->image)
                <img src="{{ asset('storage/'.$activity->image) }}" width="300">
                @endif

                <h3>{{ $activity->title }}</h3>

                <p>{{ $activity->description }}</p>

                <p>Date: {{ $activity->date }}</p>

                <p>Location: {{ $activity->location }}</p>

                <a href="/activities/{{ $activity->id }}">
                    View Details
                </a>

            </div>

            @endforeach

        </div>

    </div>

</x-app-layout>
<x-app-layout>

    <x-slot name="header">
        <h2>My Activities</h2>
    </x-slot>

    <div style="padding:20px;">

        @foreach($registrations as $reg)

        <div style="border:1px solid black; margin:10px; padding:10px;">

            <h3>{{ $reg->activity->title }}</h3>

            <p>Date: {{ $reg->activity->date }}</p>

            <p>Location: {{ $reg->activity->location }}</p>

            <p>Status: {{ $reg->status }}</p>

            <a href="/activities/{{ $reg->activity->id }}">
                <button>View Detail</button>
            </a>

        </div>

        @endforeach

    </div>

</x-app-layout>
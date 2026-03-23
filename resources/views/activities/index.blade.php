<x-app-layout>
    <h1>All Activities</h1>

    @if(session('success'))
        <div style="color:green;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="color:red;">
            {{ session('error') }}
        </div>
    @endif

    @foreach($activities as $activity)

    <div style="border:1px solid black; padding:10px; margin:10px;">

        <h2>{{ $activity->title }}</h2>

        @if($activity->image)
        <img src="{{ asset('storage/' . $activity->image) }}" width="300">
        @endif

        <p>{{ $activity->description }}</p>

        <p>Date: {{ $activity->date }}</p>

        <p>Location: {{ $activity->location }}</p>

        @php
            $registered = \App\Models\Registration::where('user_id', auth()->id())
                ->where('activity_id', $activity->id)
                ->first();
        @endphp

        @if($registered)
            <button disabled style="background:gray; color:white;">
                สมัครแล้ว
            </button>
        @else
            <form method="POST" action="/activities/{{ $activity->id }}/register">
                @csrf
                <button type="submit">
                    สมัครกิจกรรม
                </button>
            </form>
        @endif

    </div>

    @endforeach
</x-app-layout>
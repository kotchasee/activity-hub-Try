<x-app-layout>

    <h1>{{ $activity->title }}</h1>

    <img src="{{ asset('storage/'.$activity->image) }}" width="300">

    <p>{{ $activity->description }}</p>
    
    <p>Date: {{ $activity->date }}</p>
    <p>Location: {{ $activity->location }}</p>

    @php
        $registration= \App\Models\Registration::where('user_id', auth()->id())
            ->where('activity_id', $activity->id)
            ->first();
    @endphp

    @if($registration)
        @if($registration->status == 'pending')
            <button disabled>รออนุมัติ</button>
        @elseif($registration->status == 'approved')
            <button disabled>สมัครแล้ว</button>
        @elseif($registration->status == 'rejected')
            <button disabled>ถูกปฏิเสธ</button>
       @endif
    @else
        <form method="POST" action="/activities/{{ $activity->id }}/register">
           @csrf
           <button type="submit">สมัครกิจกรรม</button>
       </form>
    @endif
    @if(auth()->user()->role === 'admin')
    <form method="POST" action="/admin/activities/{{ $activity->id }}">
        @csrf
        @method('DELETE')
        <button style="background:red;">Delete activities</button>
    </form>
    @endif

</x-app-layout>
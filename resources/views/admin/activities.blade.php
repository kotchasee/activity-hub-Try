<x-app-layout>

<h1>Approve Activities</h1>

@foreach($activities as $act)

<div style="border:1px solid black; margin:10px; padding:10px;">

    <h3>{{ $act->title }}</h3>

    <p>Date: {{ $act->date }}</p>

    <p>Status: {{ $act->status }}</p>

    <form method="POST" action="/admin/activities/{{ $act->id }}/approve">
        @csrf
        <button>Approve</button>
    </form>

    <form method="POST" action="/admin/activities/{{ $act->id }}/reject">
        @csrf
        <button>Reject</button>
    </form>

</div>

@endforeach

</x-app-layout>
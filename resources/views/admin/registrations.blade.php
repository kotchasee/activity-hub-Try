<x-app-layout>

<h1>Manage Registrations</h1>

@foreach($registrations as $reg)

<div style="border:1px solid black; margin:10px; padding:10px;">

    <h3>{{ $reg->activity->title }}</h3>

    <p>User: {{ $reg->user->name }} ({{ $reg->user->email }})</p>

    <p>Status: {{ $reg->status }}</p>

    <p>Registered at: {{ $reg->created_at }}</p>

    @if($reg->status === 'pending')
    <form method="POST" action="/admin/registrations/{{ $reg->id }}/approve" style="display: inline;">
        @csrf
        <button>Approve</button>
    </form>

    <form method="POST" action="/admin/registrations/{{ $reg->id }}/reject" style="display: inline;">
        @csrf
        <button>Reject</button>
    </form>
    @endif

</div>

@endforeach

</x-app-layout>
<h1>My Activities</h1>

@foreach($registrations as $reg)

<div>

Activity ID : {{ $reg->activity_id }}

<br>

Status : {{ $reg->status }}

</div>

@endforeach
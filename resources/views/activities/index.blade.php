<!DOCTYPE html>
<html>
<head>
    <title>All Activities</title>
</head>
<body>

<h1>All Activities</h1>

@foreach($activities as $activity)

<div style="border:1px solid black; padding:10px; margin:10px;">



    <h2>{{ $activity->title }}</h2>

    @if($activity->image)
    <img src="{{ asset('storage/' . $activity->image) }}" width="300">
    @endif

    <p>{{ $activity->description }}</p>

    <p>Date: {{ $activity->date }}</p>

    <p>Location: {{ $activity->location }}</p>

</div>

@endforeach

</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>Card</title>
</head>
<body>

<h1>{{ $activity->title }}</h1>

<img src="{{ asset('storage/'.$activity->image) }}">

<p>{{ $activity->description }}</p>

<p>Date: {{ $activity->date }}</p>

<p>Location: {{ $activity->location }}</p>
<form action="/activities/{{ $activity->id }}/register" method="POST">
@csrf
<button type="submit">Join Activity</button>
</form>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>Create Activity</title>
</head>
<body>

<h1>Create Activity</h1>

@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

<form action="/create-activity" method="POST" enctype="multipart/form-data">
@csrf

<label>Title</label><br>
<input type="text" name="title"><br><br>

<label>Description</label><br>
<textarea name="description"></textarea><br><br>

<label>Date</label><br>
<input type="date" name="date"><br><br>

<label>Location</label><br>
<input type="text" name="location"><br><br>

<label>Cover Image</label><br>
<input type="file" name="image"><br><br>

<button type="submit">Create Activity</button>

</form>

</body>
</html>
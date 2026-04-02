<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Activity Hub</title>

@vite(['resources/css/app.css','resources/js/app.js'])

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

</head>

<body class="bg-gray-50 font-sans">

<!-- NAVBAR -->
<nav class="bg-orange-500 text-white px-4 sm:px-6 py-4 shadow-sm">

    <div class="max-w-6xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="text-lg font-bold text-gray-800">
            ActivityHub
        </div>

        <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4 text-sm">

            <a href="/" class="text-gray-600 hover:text-black">Home</a>
            <a href="/activities" class="text-gray-600 hover:text-black">Events</a>
            <a href="/dashboard" class="text-gray-600 hover:text-black">Dashboard</a>

            <a href="/login"
            class="bg-orange-500 text-white px-4 py-2 rounded-lg shadow hover:bg-orange-400 transition">
            Login
            </a>

            <a href="{{ route('register') }}"
            class="border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
            Register
            </a>

        </div>
    </div>

</nav>


<!-- HERO -->
<div class="max-w-6xl mx-auto mt-16 px-6 text-center pb-20">

    <h1 class="text-4xl md:text-5xl font-bold text-gray-800 leading-tight">
        Welcome to <br>
        <span class="text-orange-500">Activity Hub</span>
    </h1>

    <p class="text-gray-500 mt-4 max-w-xl mx-auto">
        Discover and join exciting activities, workshops, and events easily in one place
    </p>

    <div class="mt-6 flex flex-col sm:flex-row justify-center gap-4">

        <a href="/activities"
        class="bg-orange-500 text-white px-6 py-3 rounded-xl shadow hover:scale-105 transition text-center">
        Get Started Free →
        </a>

        <a href="/activities"
        class="border border-gray-300 px-6 py-3 rounded-xl hover:bg-gray-100 transition text-center">
        Browse Activities
        </a>

    </div>

</div>

<!-- FOOTER -->
<footer class="bg-white text-gray-500 text-center p-4 mt-10 border-t">
    © 2026 ActivityHub
</footer>


</body>
</html>
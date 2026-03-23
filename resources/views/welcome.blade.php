<!DOCTYPE html>
<html lang="en">

    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Activity Hub</title>

    @vite(['resources/css/app.css','resources/js/app.js'])
    </head>

    <body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-blue-600 text-white p-4 flex justify-between items-center">

    <div class="text-lg font-bold">
    ActivityHub
    </div>

    <div class="space-x-4 text-sm">

    <a href="/" class="hover:underline">Home</a>
    <a href="/activities" class="hover:underline">Events</a>
    <a href="/dashboard" class="hover:underline">Dashboard</a>

    <!-- ปุ่ม Login เด่น -->
    <a href="/login"
    class="bg-yellow-400 text-black px-4 py-2 rounded-lg font-bold shadow hover:bg-yellow-500 transition">

    Login

    </a>

    </div>

    </nav>


    <!-- Intro -->
    <div class="text-center mt-16 px-6">

    <h1 class="text-3xl md:text-5xl font-bold mb-4">
    Welcome to Activity Hub
    </h1>

    <p class="text-gray-600 text-sm md:text-lg">
    เว็บไซต์สำหรับค้นหาและเข้าร่วมกิจกรรมต่าง ๆ ในมหาลัยได้ง่ายในที่เดียว
    </p>

    </div>


    <!-- Features -->
    <div class="max-w-5xl mx-auto mt-10 grid grid-cols-1 md:grid-cols-2 gap-6 px-6">

    <div class="bg-white p-6 rounded shadow text-center">
    <h2 class="font-bold text-lg mb-2">📅 ดูกิจกรรม</h2>
    <a href="/activities">
        <p class="text-gray-600">
            ดูรายการกิจกรรมทั้งหมด
        </p> 
    </a>
    </div>

    <div class="bg-white p-6 rounded shadow text-center">
    <h2 class="font-bold text-lg mb-2">🔍 ค้นหากิจกรรม</h2>
    <p class="text-gray-600">ค้นหากิจกรรมที่สนใจ</p>
    </div>

    <div class="bg-white p-6 rounded shadow text-center">
    <h2 class="font-bold text-lg mb-2">⭐ ให้คะแนน</h2>
    <p class="text-gray-600">รีวิวและให้คะแนนกิจกรรม</p>
    </div>

    <div class="bg-white p-6 rounded shadow text-center">
    <h2 class="font-bold text-lg mb-2">➕ สร้างกิจกรรม</h2>
    <p class="text-gray-600">สร้างกิจกรรมของคุณเอง</p>
    </div>

    </div>


    <!-- ปุ่ม Login กลางหน้า (เด่นมาก) -->
    <div class="text-center mt-10">

    <a href="/login"
    class="bg-red-500 text-white px-8 py-3 text-lg rounded-lg font-bold shadow hover:bg-red-600 transition">

    Login Now

    </a>

    </div>


    <!-- Footer -->
    <footer class="bg-gray-800 text-white text-center p-4 mt-10">
    © 2026 ActivityHub
    </footer>


    </body>
</html>
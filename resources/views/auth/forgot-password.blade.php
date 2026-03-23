<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Forgot Password - ActivityHub</title>

@vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">

<!-- 🔵 Navbar -->
<nav class="bg-blue-600 text-white p-4 flex justify-between">
    <div class="text-lg font-bold">
        ActivityHub
    </div>

    <a href="/login" class="hover:underline">
        Login
    </a>
</nav>

<!-- 🔥 Content -->
<div class="flex-grow flex items-center justify-center">

    <x-guest-layout>

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400 text-center">
            Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email -->
            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email"
                    class="block mt-1 w-full"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex justify-end mt-4">
                <x-primary-button>
                    Send Reset Link
                </x-primary-button>
            </div>
        </form>

    </x-guest-layout>

</div>

<!-- 🔻 Footer -->
<footer class="bg-gray-800 text-white text-center p-4">
    © 2026 ActivityHub
</footer>

</body>
</html>
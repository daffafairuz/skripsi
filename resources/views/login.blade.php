<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - AquaPakcoy</title>
    @vite('resources/css/app.css')
    <!-- Google Fonts for premium typography -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-emerald-500 via-teal-600 to-cyan-700 flex items-center justify-center min-h-screen px-4 py-8">

<div class="bg-white/95 backdrop-blur-md p-8 sm:p-10 rounded-3xl shadow-2xl w-full max-w-md border border-white/20 transition-all duration-300 hover:shadow-emerald-950/10">

    <!-- Header / Brand -->
    <div class="text-center mb-8">
        <div class="inline-flex p-3 bg-emerald-50 text-emerald-600 rounded-2xl mb-4 shadow-inner">
            <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 5H9l-1-5z"/>
            </svg>
        </div>
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">AquaPakcoy</h2>
        <p class="text-sm text-gray-500 mt-2">Masuk ke dasbor pemantauan akuaponik Anda</p>
    </div>

    <!-- Error Alert -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 p-4 mb-6 rounded-2xl text-sm flex items-start gap-3">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="/login" class="space-y-5">
        @csrf

        <!-- Email -->
        <div>
            <label class="block mb-2 text-sm font-semibold text-gray-700 tracking-wide">Email Address</label>
            <div class="relative">
                <input type="email" name="email" 
                       class="w-full border border-gray-200 bg-gray-50/50 px-4 py-3 rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 focus:bg-white transition-all text-sm text-gray-800" 
                       placeholder="nama@email.com" required>
            </div>
        </div>

        <!-- Password -->
        <div>
            <label class="block mb-2 text-sm font-semibold text-gray-700 tracking-wide">Password</label>
            <div class="relative">
                <input type="password" name="password" 
                       class="w-full border border-gray-200 bg-gray-50/50 px-4 py-3 rounded-2xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 focus:bg-white transition-all text-sm text-gray-800" 
                       placeholder="••••••••" required>
            </div>
        </div>

        <!-- Button -->
        <button class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white py-3.5 px-4 rounded-2xl font-bold text-sm shadow-lg shadow-emerald-500/20 hover:shadow-xl hover:shadow-emerald-500/30 transition-all duration-200 transform active:scale-95 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:ring-offset-2">
            Masuk Sekarang
        </button>
    </form>

</div>

</body>
</html>
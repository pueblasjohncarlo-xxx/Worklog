<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 • Forbidden</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-purple-900 via-indigo-950 to-black text-gray-100 flex items-center justify-center p-6">
    <div class="max-w-xl w-full bg-white rounded-2xl shadow-2xl border border-gray-200 p-8 text-center">
        <div class="text-5xl font-extrabold text-indigo-700 mb-2">403</div>
        <div class="text-2xl font-bold text-gray-900 mb-3">Access Forbidden</div>
        <p class="text-gray-700 mb-6 font-medium">Wala kay permiso sa page nga imong gi-access. Palihug balik sa dashboard o login.</p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ url('/') }}" class="px-6 py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 w-full sm:w-auto text-sm">Go to Home</a>
            @auth
                <a href="{{ route('dashboard') }}" class="px-6 py-3 rounded-xl bg-gray-100 text-gray-900 font-bold hover:bg-gray-200 border border-gray-200 w-full sm:w-auto text-sm">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl bg-gray-100 text-gray-900 font-bold hover:bg-gray-200 border border-gray-200 w-full sm:w-auto text-sm">Sign in</a>
            @endauth
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex flex-col items-center justify-center min-h-screen">
    <h1 class="text-5xl font-bold text-green-400 mb-8">📱 App Store Backend</h1>
    <div class="space-x-4">
        @if (Route::has('admin.login'))
            <a href="{{ route('admin.login') }}" class="bg-teal-600 hover:bg-teal-700 px-6 py-3 rounded-xl font-bold transition">Admin Login</a>
        @endif
        <a href="/api/apps" class="bg-gray-700 hover:bg-gray-600 px-6 py-3 rounded-xl font-bold transition">View API Apps</a>
    </div>
</body>
</html>

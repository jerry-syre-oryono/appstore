<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-800 flex items-center justify-center h-screen">
    <div class="bg-white/10 backdrop-blur-lg p-8 rounded-2xl w-96 border border-white/20">
        <h2 class="text-2xl font-bold text-white mb-6 text-center">Admin Login</h2>
        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <input type="email" name="email" placeholder="Email" class="w-full p-3 rounded-lg bg-white/20 text-white mb-4" required>
            <input type="password" name="password" placeholder="Password" class="w-full p-3 rounded-lg bg-white/20 text-white mb-4" required>
            <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 p-3 rounded-lg font-semibold">Login</button>
        </form>
    </div>
</body>
</html>

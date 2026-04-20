<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - App Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <style>
        body { background: linear-gradient(135deg, #0F172A, #1E1B4B); color: #e2e8f0; }
        .glass-card { background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); border-radius: 24px; border: 1px solid rgba(255,255,255,0.2); }
        .btn-primary { background: #0A6E6E; transition: all 0.2s; }
        .btn-primary:hover { background: #0D8C8C; transform: scale(1.02); }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen">
        <nav class="glass-card m-4 p-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-green-400">📱 App Store Admin</h1>
            <div>
                <span class="mr-4">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-red-400 hover:text-red-300">Logout</button>
                </form>
            </div>
        </nav>
        <main class="p-6">
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>

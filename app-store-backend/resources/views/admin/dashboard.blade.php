@extends('layouts.admin')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="glass-card p-6 text-center">
        <h3 class="text-gray-400">Total Apps</h3>
        <p class="text-4xl font-bold text-green-400" id="totalApps">0</p>
    </div>
    <div class="glass-card p-6 text-center">
        <h3 class="text-gray-400">Total Users</h3>
        <p class="text-4xl font-bold text-blue-400" id="totalUsers">0</p>
    </div>
    <div class="glass-card p-6 text-center">
        <h3 class="text-gray-400">Pending Submissions</h3>
        <p class="text-4xl font-bold text-yellow-400" id="pendingSubmissions">0</p>
    </div>
</div>
<div class="glass-card p-6">
    <canvas id="installChart" height="100"></canvas>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    gsap.from(".glass-card", { duration: 0.8, y: 30, opacity: 0, stagger: 0.2 });
    fetch('/api/admin/stats', {
        headers: { 'Authorization': 'Bearer {{ auth()->user()->createToken('admin-token')->plainTextToken }}' }
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('totalApps').innerText = data.total_apps;
        document.getElementById('totalUsers').innerText = data.total_users;
        document.getElementById('pendingSubmissions').innerText = data.pending_submissions;
        const ctx = document.getElementById('installChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.installs.map(i => i.date),
                datasets: [{ label: 'Installs', data: data.installs.map(i => i.count), borderColor: '#0A6E6E', fill: false }]
            }
        });
    });
</script>
@endsection

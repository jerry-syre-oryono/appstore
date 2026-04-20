<?php
use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/login', [DashboardController::class, 'loginForm'])->name('admin.login');
Route::post('/admin/login', [DashboardController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [DashboardController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
});

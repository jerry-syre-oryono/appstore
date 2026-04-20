<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AppController;
use App\Http\Controllers\API\SubmissionController;
use App\Http\Controllers\API\PushTokenController;
use App\Http\Controllers\API\Admin\AppManagementController;
use App\Http\Controllers\API\Admin\SubmissionReviewController;
use App\Http\Controllers\API\Admin\StatsController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/apps', [AppController::class, 'index']);
    Route::get('/apps/{id}', [AppController::class, 'show']);
    Route::get('/apps/check-update', [AppController::class, 'checkUpdate']);
    Route::post('/user/installed', [AppController::class, 'reportInstalled']);
    Route::post('/submissions/upload-apk', [SubmissionController::class, 'uploadApk']);
    Route::post('/submissions', [SubmissionController::class, 'store']);
    Route::get('/submissions', [SubmissionController::class, 'index']);
    Route::post('/push-token', [PushTokenController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::post('/apps', [AppManagementController::class, 'storeApp']);
    Route::post('/apps/{appId}/versions', [AppManagementController::class, 'uploadVersion']);
    Route::get('/submissions', [SubmissionReviewController::class, 'index']);
    Route::patch('/submissions/{id}', [SubmissionReviewController::class, 'review']);
    Route::get('/stats', [StatsController::class, 'index']);
});

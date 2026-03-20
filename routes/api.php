<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;


// Route::get('/user', function (Request $request) {
//     return "Hello there I am API";
// });


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function() {

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);

    // Authenticated user notifications
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread', [NotificationController::class, 'unread']);
    Route::patch('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // RBAC (Role-based protection)

    Route::middleware('role:admin') -> group(function(){
        Route::apiResource('users', UserController::class);
    });
});



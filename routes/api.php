<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\ValueApiController;
use App\Http\Controllers\Api\InvoiceApiController;

// Public (no token required)
Route::get('/getTimezone', function () {
    dd(
    now(),
    now()->toDateTimeString(),
    date_default_timezone_get()
);
    dd(config('app.timezone'));
});
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);


// Protected (token required)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/profileData', [ApiController::class, 'profileData']);     

    Route::get('/messages', [ApiController::class, 'messages']);     
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/news', [ApiController::class, 'getNews']);     // get chat with a user
    Route::get('/signals', [ApiController::class, 'getSignals']);     // get chat with a user
    // Chat
    Route::post('/chat/send', [ApiController::class, 'sendMessage']);    // send message
    Route::get('/chats', [ApiController::class, 'getChat']);     // get chat with a user
    Route::get('/chat/users', [ApiController::class, 'getUsersWithChats']); // list all users chatting with admin
    Route::get('/values', [ValueApiController::class, 'active']);
    Route::get('/invoices', [InvoiceApiController::class, 'index']);
    Route::get('/user/notifications', [ApiController::class, 'getUserNotifications']);
    
    
    Route::post('/packages/purchase', [ApiController::class, 'purchase']);
});

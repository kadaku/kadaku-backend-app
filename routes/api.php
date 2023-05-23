<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\SocialAuthController;
use App\Http\Controllers\ResetPassword;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/**
 * Brand App 
*/
Route::middleware(['api_key'])->group(function () {
    // rute-rute yang memerlukan autentikasi API key
    Route::get('/brand', [BrandController::class, 'index']);

    Route::controller(ResetPassword::class)->group(function () {
        Route::post('/reset-password/send-mail', 'send_mail');
        Route::post('/reset-password/validate-token', 'validate_token');
        Route::post('/reset-password/change', 'change');
    });
});

Route::get('email/verify/{id}', [AuthController::class, 'verify'])->name('user.verify'); 

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');

    
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('/user')->group(function () {
            Route::get('/', 'profile');
            Route::put('/{id}', 'update_profile');
        });

        Route::get('/logout', 'logout');
    });

    Route::group(['middleware' => ['throttle:6,1', 'auth:sanctum']], function () {
        Route::post('/email/verify/resend', 'resend_verify')->name('verification.resend');
    });
});


Route::controller(SocialAuthController::class)->group(function () {
    Route::get('/auth/login/{service}', 'redirect');
    Route::get('/auth/login/{service}/callback', 'callback');
});


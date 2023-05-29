<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\CategoriesController;
use App\Http\Controllers\API\InvitationsController;
use App\Http\Controllers\API\RegionsController;
use App\Http\Controllers\API\SocialAuthController;
use App\Http\Controllers\API\XenditController;
use App\Http\Controllers\ResetPassword;
use Illuminate\Support\Facades\Route;

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
    Route::get('/categories', [CategoriesController::class, 'list']);
    Route::controller(RegionsController::class)->group(function () {
        Route::get('/regions/province', 'propinsi');
        Route::get('/regions/province/{no}', 'propinsi');
        Route::get('/regions/city', 'kabupaten');
        Route::get('/regions/city/{no_province}', 'kabupaten');
        Route::get('/regions/city/{no_province}/{no_city}', 'kabupaten');
        Route::get('/regions/district', 'kecamatan');
        Route::get('/regions/district/{no_province}', 'kecamatan');
        Route::get('/regions/district/{no_province}/{no_city}', 'kecamatan');
        Route::get('/regions/district/{no_province}/{no_city}/{no_district}', 'kecamatan');
        Route::get('/regions/subdistrict', 'kelurahan');
        Route::get('/regions/subdistrict/{no_province}', 'kelurahan');
        Route::get('/regions/subdistrict/{no_province}/{no_city}', 'kelurahan');
        Route::get('/regions/subdistrict/{no_province}/{no_city}/{no_district}', 'kelurahan');
        Route::get('/regions/subdistrict/{no_province}/{no_city}/{no_district}/{no_subdistrict}', 'kelurahan');
    });

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
            Route::put('/', 'update_profile');
            Route::post('/avatar', 'update_avatar');
        });

        Route::post('/logout', 'logout');
    });

    Route::group(['middleware' => ['throttle:6,1', 'auth:sanctum']], function () {
        Route::post('/email/verify/resend', 'resend_verify')->name('verification.resend');
    });
});

// INVITATIONS
Route::controller(InvitationsController::class)->group(function () {    
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('/invitations')->group(function () {
            Route::post('/', 'create');
        });
    });
});


Route::controller(SocialAuthController::class)->group(function () {
    Route::get('/auth/login/{service}', 'redirect');
    Route::get('/auth/login/{service}/callback', 'callback');
});

// XENDIT
Route::post('/payment/xendit', [XenditController::class, 'payment']);
Route::post('/payment/xendit/callback', [XenditController::class, 'payment']);
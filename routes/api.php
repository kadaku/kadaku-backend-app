<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BrandController;
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
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    
    Route::middleware('auth:sanctum')->group(function () {

        Route::prefix('/profile')->group(function () {
            Route::get('/', 'profile');
            Route::put('/{id}', 'update_profile');
        });

        Route::get('/logout', 'logout');
    });

});

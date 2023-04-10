<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['namespace' => 'App\Http\Controllers'], function()
{   
    Route::group(['middleware' => ['guest']], function() {
        /**
         * Login Routes
         */
        Route::get('/login', 'LoginController@show')->name('login.show');
        Route::post('/login', 'LoginController@login')->name('login.perform');
    });

    Route::group(['middleware' => ['admin']], function() {
        /**
         * Logout Routes
         */
        Route::get('/logout', 'LogoutController@perform')->name('logout.perform');

        /**
         * Dashboard Routes
         */
        Route::get('/', 'DashboardController@index')->name('dashboard.index');
        Route::get('/dashboard', 'DashboardController@index')->name('dashboard.index');


        /** 
         * Brand
        */
        Route::get('/brand', 'BrandController@index');
        Route::get('/brand/show/{id}', 'BrandController@show');
        Route::post('/brand/store', 'BrandController@store');

        /** 
         * Admin Menu
        */
        Route::get('/admin-menu', 'AdminMenuController@index');
        Route::get('/admin-menu/list', 'AdminMenuController@list');
        Route::post('/admin-menu/store', 'AdminMenuController@store');
        Route::get('/admin-menu/show/{id}', 'AdminMenuController@show');
        Route::delete('/admin-menu/destroy/{id}', 'AdminMenuController@destroy');

        /** 
         * User Group & Privileges
        */
        Route::get('/privileges', 'PrivilegesController@index');
        Route::get('/privileges/list', 'PrivilegesController@list');
        Route::get('/privileges/list-privileges', 'PrivilegesController@list_privileges');
        Route::get('/privileges/show/{id}', 'PrivilegesController@show');
        Route::post('/privileges/store', 'PrivilegesController@store');
        Route::delete('/privileges/destroy/{id}', 'PrivilegesController@destroy');

        /** 
         * Accounts
        */
        Route::get('/accounts', 'AccountsController@index');
        Route::get('/accounts/list', 'AccountsController@list');
        Route::get('/accounts/show/{id}', 'AccountsController@show');
        Route::post('/accounts/update-status', 'AccountsController@update');
    });
});
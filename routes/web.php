<?php

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

Route::group(['namespace' => 'App\Http\Controllers'], function () {
	Route::group(['middleware' => ['guest']], function () {
		/**
		 * Login Routes
		 */
		Route::get('/panel-admin-kadaku', 'LoginController@show')->name('login.show');
		Route::post('/panel-admin-kadaku', 'LoginController@login')->name('login.perform');

		Route::get('/', function () {
			return view('home.index');
		});
	});

	Route::group(['middleware' => ['admin']], function () {
		/** 
		 * Dev
		 */
		Route::get('/developer/scraping', 'DevController@scraping');
		Route::get('/developer/download', 'DevController@download');

		/**
		 * Logout Routes
		 */
		Route::get('/logout', 'LogoutController@perform')->name('logout.perform');

		/**
		 * Dashboard Routes
		 */
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
		 * Accounts
		 */
		Route::get('/accounts', 'AccountsController@index');
		Route::get('/accounts/list', 'AccountsController@list');
		Route::get('/accounts/show/{id}', 'AccountsController@show');
		Route::post('/accounts/update-status', 'AccountsController@update');
		Route::post('/accounts/store', 'AccountsController@store');
		Route::delete('/accounts/destroy/{id}', 'AccountsController@destroy');
		Route::post('/accounts/reset-password', 'AccountsController@reset_password');

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
		 * Customers
		 */
		Route::get('/customers', 'CustomersController@index');
		Route::get('/customers/list', 'CustomersController@list');
		Route::get('/customers/show/{id}', 'CustomersController@show');
		Route::post('/customers/update-status', 'CustomersController@update');

		/** 
		 * Masterdata -> Addons
		 */
		Route::get('/addons', 'AddonsController@index');
		Route::get('/addons/list', 'AddonsController@list');
		Route::get('/addons/show/{id}', 'AddonsController@show');
		Route::post('/addons/update-status', 'AddonsController@update');
		Route::post('/addons/store', 'AddonsController@store');
		Route::delete('/addons/destroy/{id}', 'AddonsController@destroy');
		
		/** 
		 * Masterdata -> Categories
		 */
		Route::get('/categories', 'CategoriesController@index');
		Route::get('/categories/list', 'CategoriesController@list');
		Route::get('/categories/show/{id}', 'CategoriesController@show');
		Route::post('/categories/update-status', 'CategoriesController@update');
		Route::post('/categories/store', 'CategoriesController@store');
		Route::delete('/categories/destroy/{id}', 'CategoriesController@destroy');

		/** 
		 * Masterdata -> Theme Type
		 */
		Route::get('/themes-type', 'ThemeTypeController@index');
		Route::get('/themes-type/list', 'ThemeTypeController@list');
		Route::get('/themes-type/show/{id}', 'ThemeTypeController@show');
		Route::post('/themes-type/update-status', 'ThemeTypeController@update');
		Route::post('/themes-type/store', 'ThemeTypeController@store');
		Route::delete('/themes-type/destroy/{id}', 'ThemeTypeController@destroy');
		
		/** 
		 * Masterdata -> Coupons
		 */
		Route::get('/coupons', 'CouponsController@index');
		Route::get('/coupons/list', 'CouponsController@list');
		Route::get('/coupons/show/{id}', 'CouponsController@show');
		Route::post('/coupons/update-status', 'CouponsController@update');
		Route::post('/coupons/store', 'CouponsController@store');
		Route::delete('/coupons/destroy/{id}', 'CouponsController@destroy');

		/** 
		 * Masterdata -> Categories Musics
		 */
		Route::get('/categories-musics', 'CategoriesMusicsController@index');
		Route::get('/categories-musics/list', 'CategoriesMusicsController@list');
		Route::get('/categories-musics/show/{id}', 'CategoriesMusicsController@show');
		Route::post('/categories-musics/update-status', 'CategoriesMusicsController@update');
		Route::post('/categories-musics/store', 'CategoriesMusicsController@store');
		Route::delete('/categories-musics/destroy/{id}', 'CategoriesMusicsController@destroy');
		
		/** 
		 * Masterdata -> Musics
		 */
		Route::get('/musics', 'MusicsController@index');
		Route::get('/musics/list', 'MusicsController@list');
		Route::get('/musics/show/{id}', 'MusicsController@show');
		Route::post('/musics/update-status', 'MusicsController@update');
		Route::post('/musics/store', 'MusicsController@store');
		Route::delete('/musics/destroy/{id}', 'MusicsController@destroy');
		Route::get('/musics/sync-musics', 'MusicsController@scraping_musics');
		Route::get('/musics/sync-file-musics', 'MusicsController@scraping_file_musics');
	});
});

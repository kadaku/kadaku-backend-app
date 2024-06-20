<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BlogsController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\CategoriesController;
use App\Http\Controllers\API\CouponsController;
use App\Http\Controllers\API\InvitationsController;
use App\Http\Controllers\API\LayoutsController;
use App\Http\Controllers\API\MasterdataController;
use App\Http\Controllers\API\MusicsController;
use App\Http\Controllers\API\RegionsController;
use App\Http\Controllers\API\SocialAuthController;
use App\Http\Controllers\API\ThemesController;
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
	Route::get('/invitations/domain/{slug}', [InvitationsController::class, 'get_by_domain']);
	Route::post('/invitations/wish', [InvitationsController::class, 'store_wish_by_domain']);
	Route::get('/invitations/wishes/domain/{slug}/{invitation_id}/{per_fetch}/{current_total}', [InvitationsController::class, 'get_wishes_by_domain']);
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
		Route::post('/reset-password/check', 'check');
		Route::post('/reset-password/change', 'change');
	});

	Route::controller(ThemesController::class)->group(function () {
		Route::get('/themes', 'list');
		Route::post('/theme', 'getTheme');
		Route::put('/theme/component/update', 'updateComponent');
	});

	Route::controller(BlogsController::class)->group(function () {
		Route::get('/blogs', 'list');
		Route::post('/blog', 'show');
	});
});

Route::controller(MasterdataController::class)->group(function () {
	Route::get('/packages', 'list_packages');
	Route::get('/addons', 'list_addons');
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

Route::middleware('auth:sanctum')->group(function () {
	// PAYMENTS
	Route::controller(XenditController::class)->group(function () {
		Route::prefix('/payment')->group(function () {
			Route::prefix('/xendit')->group(function () {
				Route::prefix('/invoice_checkout')->group(function () {
					Route::post('/premium_account_activation', 'checkout_invoice_premium_account_activation');
				});
			});
		});
	});

	Route::middleware(['ensure.premium.account'])->group(function () {
		// INVITATIONS
		Route::controller(InvitationsController::class)->group(function () {
			Route::prefix('/invitations')->group(function () {
				Route::post('/check-available-domain', 'check_available_domain')->withoutMiddleware(['ensure.premium.account']);
				Route::get('/', 'list')->withoutMiddleware(['ensure.premium.account']);
				Route::get('/{id}', 'get');
				Route::get('/{id}/all', 'get_all');
				Route::post('/', 'create');
				Route::put('/', 'update');
				Route::post('/store/image', 'store_image');
				Route::post('/destroy/image', 'destroy_image');
				Route::post('/store/cover', 'store_cover');
				Route::post('/store/background-custom', 'store_background_custom');
				Route::post('/destroy/background-custom', 'destroy_background_custom');
				Route::post('/store/background-screen-guests', 'store_background_screen_guests');
				Route::post('/destroy/background-screen-guests', 'destroy_background_screen_guests');
			});
		});
	});

	// COUPONS
	Route::controller(CouponsController::class)->group(function () {
		Route::prefix('/coupons')->group(function () {
			Route::post('/apply', 'check_valid');
			Route::get('/available', 'list');
		});
	});

	// MUSICS
	Route::controller(MusicsController::class)->group(function () {
		Route::get('/categories-musics', 'list_categories_musics');
		Route::get('/musics', 'list_musics');
	});

	// LAYOUTS
	Route::controller(LayoutsController::class)->group(function () {
		Route::get('/categories-layouts', 'list_categories_layouts');
		Route::get('/layouts', 'list_layouts');
	});
});


Route::controller(SocialAuthController::class)->group(function () {
	Route::get('/auth/login/{service}', 'redirect');
	Route::get('/auth/login/{service}/callback', 'callback');
});

// PUBLIC CALLBACKS
Route::controller(XenditController::class)->group(function () {
	Route::prefix('/payment')->group(function () {
		Route::prefix('/xendit')->group(function () {
			Route::prefix('/invoice_callback')->group(function () {
				Route::post('/premium_account_activation', 'invoice_callback');
			});
		});
	});
});

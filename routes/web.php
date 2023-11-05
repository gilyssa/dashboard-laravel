<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\CitiesController;
use App\Http\Controllers\PriceBandsController;
use App\Http\Controllers\EnterprisesController;
use App\Http\Controllers\DeliverersController;
use App\Http\Controllers\PostingsController;
use App\Http\Controllers\EnterprisePriceRangesController;
use App\Http\Controllers\ClosuresController;




use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware' => 'auth'], function () {

	Route::redirect('/', 'posting-management-register');

	Route::get('dashboard', function () {
		return redirect('posting-management-register');
	})->name('dashboard');


	Route::get('billing', function () {
		return view('billing');
	})->name('billing');

	Route::get('profile', function () {
		return view('profile');
	})->name('profile');

	Route::get('rtl', function () {
		return view('rtl');
	})->name('rtl');

	Route::get('tables', function () {
		return view('tables');
	})->name('tables');

	Route::get('virtual-reality', function () {
		return view('virtual-reality');
	})->name('virtual-reality');

	Route::get('static-sign-in', function () {
		return view('static-sign-in');
	})->name('sign-in');

	Route::get('static-sign-up', function () {
		return view('static-sign-up');
	})->name('sign-up');

	Route::get('/user-profile', [InfoUserController::class, 'create']);
	Route::post('/user-profile', [InfoUserController::class, 'updateLoggedUser']);
	Route::post('/user-profile-update/{id}', [InfoUserController::class, 'store']);
	Route::get('/user-profile-update/{id}', [InfoUserController::class, 'updateUser']);

	Route::get('/user-management', [InfoUserController::class, 'show']);
	Route::get('/user-management-removed', [InfoUserController::class, 'showRemoved']);
	Route::post('/user-management/{id}', [InfoUserController::class, 'destroy'])->name('users.delete');
	Route::post('/user-management/recover/{id}', [InfoUserController::class, 'recover'])->name('users.recover');

	Route::get('/logout', [SessionsController::class, 'destroy']);
	Route::get('/login', function () {
		return view('dashboard');
	})->name('sign-up');

	Route::post('/register', [RegisterController::class, 'store']);
	Route::get('/register', [RegisterController::class, 'create']);


	Route::get('/city-management-register', [CitiesController::class, 'create']);
	Route::get('/city-management-update/{id}', [CitiesController::class, 'update']);
	Route::post('/city-management-edit/{id}', [CitiesController::class, 'updateCity']);
	Route::post('/city-management-new', [CitiesController::class, 'store']);
	Route::get('/city-management', [CitiesController::class, 'show']);
	Route::get('/city-management-removed', [CitiesController::class, 'showRemoved']);
	Route::post('/city-management/{id}', [CitiesController::class, 'destroy']);
	Route::post('/city-management/recover/{id}', [CitiesController::class, 'recover']);

	Route::get('/priceband-management-register', [PriceBandsController::class, 'create']);
	Route::get('/priceband-management-update/{id}', [PriceBandsController::class, 'update']);
	Route::post('/priceband-management-edit/{id}', [PriceBandsController::class, 'updatepriceband']);
	Route::post('/priceband-management-new', [PriceBandsController::class, 'store']);
	Route::get('/priceband-management', [PriceBandsController::class, 'show']);
	Route::get('/priceband-management-removed', [PriceBandsController::class, 'showRemoved']);
	Route::post('/priceband-management/{id}', [PriceBandsController::class, 'destroy']);
	Route::post('/priceband-management/recover/{id}', [PriceBandsController::class, 'recover']);

	Route::get('/enterprise-management-register', [EnterprisesController::class, 'create']);
	Route::get('/enterprise-management-update/{id}', [EnterprisesController::class, 'update']);
	Route::post('/enterprise-management-edit/{id}', [EnterprisesController::class, 'updateenterprise']);
	Route::post('/enterprise-management-new', [EnterprisesController::class, 'store']);
	Route::get('/enterprise-management', [EnterprisesController::class, 'show']);
	Route::get('/enterprise-management-removed', [EnterprisesController::class, 'showRemoved']);
	Route::post('/enterprise-management/{id}', [EnterprisesController::class, 'destroy']);
	Route::post('/enterprise-management/recover/{id}', [EnterprisesController::class, 'recover']);

	Route::get('/deliverer-management-register', [DeliverersController::class, 'create']);
	Route::get('/deliverer-management-update/{id}', [DeliverersController::class, 'update']);
	Route::post('/deliverer-management-edit/{id}', [DeliverersController::class, 'updatedeliverer']);
	Route::post('/deliverer-management-new', [DeliverersController::class, 'store']);
	Route::get('/deliverer-management', [DeliverersController::class, 'show']);
	Route::get('/deliverer-management-removed', [DeliverersController::class, 'showRemoved']);
	Route::post('/deliverer-management/{id}', [DeliverersController::class, 'destroy']);
	Route::post('/deliverer-management/recover/{id}', [DeliverersController::class, 'recover']);

	Route::get('/posting-management-register', [PostingsController::class, 'create'])->name('register');
	Route::get('/posting-management-update/{id}', [PostingsController::class, 'update']);
	Route::post('/posting-management-edit/{id}', [PostingsController::class, 'updatePosting'])->name('postingEdit');;
	Route::post('/posting-management-new', [PostingsController::class, 'store'])->name('postingManagementNew');
	Route::get('/posting-management', [PostingsController::class, 'show'])->name('postings.show');
	Route::get('/posting-management-removed', [PostingsController::class, 'showRemoved']);
	Route::post('/posting-management/{id}', [PostingsController::class, 'destroy']);
	Route::post('/posting-management/recover/{id}', [PostingsController::class, 'recover']);
	Route::post('/preventduplicated', [PostingsController::class, 'preventDuplicated'])->name('preventduplicated');

	Route::get('/enterprise-price-range-management-register', [EnterprisePriceRangesController::class, 'create']);
	Route::post('/enterprise-price-range-management-new', [EnterprisePriceRangesController::class, 'store']);
	Route::get('/enterprise-price-range-management', [EnterprisePriceRangesController::class, 'show']);
	Route::get('/enterprise-price-range-management-removed', [EnterprisePriceRangesController::class, 'showRemoved']);
	Route::post('/enterprise-price-range-management/{id}', [EnterprisePriceRangesController::class, 'destroy']);
	Route::post('/enterprise-price-range-management/recover/{id}', [EnterprisePriceRangesController::class, 'recover']);

	Route::get('/closures', [ClosuresController::class, 'show'])->name('closures');
});




Route::group(['middleware' => 'guest'], function () {
	Route::get('/login', [SessionsController::class, 'create']);
	Route::post('/session', [SessionsController::class, 'store']);
	Route::get('/login/forgot-password', [ResetController::class, 'create']);
	Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
	Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
	Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');
});

Route::get('/login', function () {
	return view('session/login-session');
})->name('login');

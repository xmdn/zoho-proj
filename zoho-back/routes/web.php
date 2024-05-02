<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Controllers\DealAccountController;
use App\Http\Middleware\ZohoTokenMiddleware;

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


Route::get('oauth', function () {
    $clientId = env('ZOHO_API_KEY');
    $redirectUri = env('APP_URL') . '/oauth/callback';
    $scope = 'ZohoCRM.modules.leads.ALL%2CZohoCRM.modules.deals.ALL%2CZohoCRM.settings.ALL%2CZohoCRM.modules.ALL%2CZohoCRM.users.ALL'; // Adjust the scope as needed

    // Redirect the user to Zoho CRM OAuth authorization URL
    return redirect()->away(env('ZOHO_API_ACCOUNTS') . "/oauth/v2/auth?response_type=code&client_id=$clientId&scope=$scope&redirect_uri=$redirectUri&access_type=offline");
})->name('login');

Route::post('api/logout', [ApiAuthController::class, 'logout'])->name('api.logout');


Route::get('/oauth/callback', [ApiAuthController::class, 'handleCallback'])->name('api.oauth.callback');


Route::middleware('zoho.token')->prefix('api')->group(function () {
    Route::post('/deal', [DealAccountController::class, 'createDeal'])->name('api.create.deal');
    Route::put('/deal/{id}', [DealAccountController::class, 'updateDeal'])->name('api.update.deal');
    Route::post('/account', [DealAccountController::class, 'createAcc'])->name('api.create.acount');
    Route::get('/stages', [DealAccountController::class, 'getStages'])->name('api.get.stages');
    Route::get('/accounts', [DealAccountController::class, 'getAccounts'])->name('api.get.accounts');
    Route::get('/deals', [DealAccountController::class, 'getDeals'])->name('api.get.deals');
    Route::get('deals-full', [DealAccountController::class, 'index'])->name('deals.index');
});

Route::get('/', HomeController::class)->name('home');


Route::middleware('zoho.token')->group(function () {
    Route::get('/deal/{id}', [HomeController::class, 'getDealById'])->name('api.get.deal.by.id');
    Route::get('/deals', [HomeController::class, 'loadPage'])->name('deals')->defaults('page', 'DealRecord');
    Route::get('/account', [HomeController::class, 'loadPage'])->name('account')->defaults('page', 'AccRecord');
});
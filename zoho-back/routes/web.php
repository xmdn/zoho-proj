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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/vue', function () {
//     return view('vue');
// });
Route::post('/register', function (Request $request) {
    $userData = $request->only(['name', 'email', 'password']);

    // Send user registration data to Zoho CRM API
    $response = Http::post('https://www.zohoapis.com/crm/v2/users', [
        'data' => [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => $userData['password'],
        ]
    ]);

    // Check the response from Zoho CRM API and handle accordingly
    if ($response->successful()) {
        // Registration successful, return success response
        return response()->json(['message' => 'Registration successful'], 200);
    } else {
        // Registration failed, return error response
        return response()->json(['message' => 'Registration failed'], $response->status());
    }
})->name('register');

Route::get('oauth', function () {
    $clientId = '1000.1M352HL7V79DSKGV1QMR4YR0C6S45V';
    $redirectUri = 'http://192.168.31.122:1002/oauth/callback';
    $scope = 'ZohoCRM.modules.leads.ALL%2CZohoCRM.modules.deals.ALL%2CZohoCRM.settings.ALL%2CZohoCRM.modules.ALL%2CZohoCRM.users.ALL'; // Adjust the scope as needed

    // Redirect the user to Zoho CRM OAuth authorization URL
    return redirect()->away("https://accounts.zoho.eu/oauth/v2/auth?response_type=code&client_id=$clientId&scope=$scope&redirect_uri=$redirectUri&access_type=offline");
})->name('login');

Route::post('api/logout', [ApiAuthController::class, 'logout'])->name('api.logout');

// Route::post('/logout', function (Request $request) {
//     $request->user()->tokens()->delete();
//     return response()->json(['message' => 'Logged out successfully']);
// })->middleware('auth:sanctum')->name('logout');


// Protected route that requires authentication
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// })->name('user');


Route::get('/oauth/callback', [ApiAuthController::class, 'handleCallback'])->name('api.oauth.callback');
Route::get('/succes', [ApiAuthController::class, 'handleSuccess'])->name('api.oauth.success');



Route::middleware('token.refresh')->group(function () {
    // Routes that require token refresh mechanism
});

// Route::get('api/deals', [DealAccountController::class, 'index'])->name('deals.index');
// Route::group(['middleware' => ZohoTokenMiddleware::class], function () {
//     // Define endpoints that require Zoho OAuth token in header
//     // Route::get('api/deals-full', [DealAccountController::class, 'index'])->name('deals.index');
//     // Add more endpoints as needed
// });
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
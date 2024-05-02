<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\ApiAuthController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route for user registration
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
});

// Route for user login
Route::get('/login', function () {
    $clientId = '1000.1M352HL7V79DSKGV1QMR4YR0C6S45V';
    $redirectUri = 'http://192.168.31.122:8007/oauth/callback';
    $scope = 'ZohoCreator.report.READ'; // Adjust the scope as needed

    // Redirect the user to Zoho CRM OAuth authorization URL
    return redirect()->away("https://accounts.zoho.com/oauth/v2/auth?response_type=code&client_id=$clientId&scope=$scope&redirect_uri=$redirectUri&access_type=offline");
});

// Route::get('/oauth/callback', 'ApiAuthController@handleCallback')->name('api.oauth.callback');

Route::post('/deal-account', 'App\Http\Controllers\DealAccountController@store');

// Protected route that requires authentication
Route::middleware('auth:api')->get('/user', function (Request $request) {
    // This route can only be accessed by authenticated users
    return $request->user();
});
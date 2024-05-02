<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;

class ApiAuthController extends Controller
{
    public function handleCallback(Request $request)
    {
        $code = $request->query('code');
        // return $code;

        $response = Http::asForm()->post(env('ZOHO_API_ACCOUNTS') . '/oauth/v2/token', [
            'code' => $code,
            'client_id' => env('ZOHO_API_KEY'),
            'client_secret' => env('ZOHO_API_SECRET'),
            'redirect_uri' => env('APP_URL') . '/oauth/callback',
            'grant_type' => 'authorization_code',
        ]);
        // return $response;

        if ($response->successful()) {
            // console.log($response->json());
            // Check if 'access_token' key exists in the response
            if ($response->json()['access_token'] ?? null) {
                $accessToken = $response->json()['access_token'];
                $refreshToken = $response->json()['refresh_token'];
                 // Store the access token in the session
                Session::put('accessToken', $accessToken);
                Session::put('refreshToken', $refreshToken);
                
                // Redirect to the success route
                return redirect()->route('home');
            } else {
                return response()->json(['error' => 'Access token not found in response'], 500);
            }
        } else {
            return response()->json(['error' => 'Failed to exchange code for access token'], $response->status());
        }
    }
    public function logout()
    {
        // Remove the access token from the session
        Session::forget('accessToken');

        // Redirect to the login page or any other appropriate route
        return redirect()->route('home');
    }

    public function handleSuccess(Request $request)
    {
        // dd(Session::all());
        // Your logic for handling the success page
        return Inertia::render('success');
    }

}

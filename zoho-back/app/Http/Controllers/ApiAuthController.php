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

        $response = Http::asForm()->post('https://accounts.zoho.eu/oauth/v2/token', [
            'code' => $code,
            'client_id' => '1000.1M352HL7V79DSKGV1QMR4YR0C6S45V',
            'client_secret' => '13475ee15bdc44ccc4b1123d63848e10efe5a616ff',
            'redirect_uri' => 'http://192.168.31.122:1002/oauth/callback',
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

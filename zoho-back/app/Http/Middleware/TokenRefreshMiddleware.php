<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenRefreshMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $accessToken = Session::get('access_token');
        $refreshToken = Session::get('refresh_token');

        if (!$accessToken || !$refreshToken) {
            // Handle the case where tokens are missing
            return response()->json(['error' => 'Access token or refresh token missing'], 401);
        }

        // Check if the access token is expired
        $tokenExpiresAt = Session::get('token_expires_at');
        if ($tokenExpiresAt && now()->gt($tokenExpiresAt)) {
            // Access token has expired, refresh it using the refresh token
            $response = Http::asForm()->post('https://accounts.zoho.eu/oauth/v2/token', [
                'refresh_token' => $refreshToken,
                'client_id' => '1000.1M352HL7V79DSKGV1QMR4YR0C6S45V',
                'client_secret' => '13475ee15bdc44ccc4b1123d63848e10efe5a616ff',
                'redirect_uri' => 'http://192.168.31.122:8007/oauth/callback',
                'grant_type' => 'refresh_token',
            ]);

            if ($response->successful()) {
                $newAccessToken = $response['access_token'];
                $newRefreshToken = $response['refresh_token'];
                $newExpiresIn = now()->addSeconds($response['expires_in']);

                // Update session with new tokens and expiry time
                Session::put('access_token', $newAccessToken);
                Session::put('refresh_token', $newRefreshToken);
                Session::put('token_expires_at', $newExpiresIn);
            } else {
                // Handle the case where refresh token request failed
                return response()->json(['error' => 'Failed to refresh access token'], $response->status());
            }
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;

class ZohoTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        if (str_starts_with($request->path(), 'api/') || str_starts_with($request->path(), 'deal/') || str_starts_with($request->path(), 'account') || str_starts_with($request->path(), 'deals')) {
            $accessToken = Session::get('accessToken');
            $isValidToken = $this->validateToken();

            // Redirect if the token is missing or invalid
            if (!$accessToken || !$isValidToken) {
                Log::info('Redirecting to /oauth.', ['GIGG' => $request]);
                Session::forget('accessToken');
                return Redirect::away('/oauth');
            } else {
                // $refreshedToken = $this->refreshToken(Session::get('refreshToken'));
                // Log::info('Token refresfffd', ['refreshedToken' => Session::get('refreshToken')]);
                // Log::info('Token refreshed successfully', ['refreshedToken' => $refreshedToken]);
                // Log::info('Redirecting  Old', ['OLd' => $accessToken, 'NEw' => $refreshedToken]);
                // Session::put('accessToken', $refreshedToken['access_token']);
            }
        }

        return $response;
    }

    private function validateToken()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . Session::get('accessToken'), // Add the Authorization header with the Zoho OAuth token
        ])->get(env('ZOHO_API_URL') . '/crm/v2/settings/modules');
        
        Log::info('Middleware CHECK', ['request' => $response->json(), 'status' => $response->status()]);
        return $response->successful() && $response->status() !== 401;
    }
    
    private function refreshToken($refreshToken)
    {
        $response = Http::asForm()->post(env('ZOHO_API_ACCOUNTS') . '/oauth/v2/token', [
            'client_id' => env('ZOHO_API_KEY'),
            'client_secret' => env('ZOHO_API_SECRET'),
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        ]);

        Log::info('Request to Zoho Token Endpoint', ['request' => $response->json(), 'status' => $response->status()]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}

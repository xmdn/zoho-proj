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

        // Add the Zoho OAuth token to the header
        // $request->headers->set('Authorization', 'Zoho-oauthtoken ' . $accessToken);
        // Log the headers after adding the token
        // Log the entire request including headers
        Log::info('Full Request:');
        Log::info($request->fullUrl());
        Log::info($request->method());
        Log::info($request->all());
        Log::info($request->headers->all());


        return $response;
    }

    private function validateToken()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . Session::get('accessToken'), // Add the Authorization header with the Zoho OAuth token
        ])->get('https://www.zohoapis.eu/crm/v2/settings/modules');
        
        Log::info('Middleware CHECK', ['request' => $response->json(), 'status' => $response->status()]);
        return $response->successful() && $response->status() !== 401;
    }
    
    private function refreshToken($refreshToken)
    {
        $response = Http::asForm()->post('https://accounts.zoho.eu/oauth/v2/token', [
            'client_id' => '1000.1M352HL7V79DSKGV1QMR4YR0C6S45V',
            'client_secret' => '13475ee15bdc44ccc4b1123d63848e10efe5a616ff',
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

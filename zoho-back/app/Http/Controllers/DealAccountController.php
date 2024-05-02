<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Date;

class DealAccountController extends Controller
{
    public function index(Request $request)
    {
        // Fetch all deals using an HTTP request or Eloquent query, depending on your setup
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . Session::get('accessToken'), // Add the Authorization header with the Zoho OAuth token
        ])->get(env('ZOHO_API_URL') . '/crm/v2/Deals'); // Example API endpoint
        $deals = $response->json(); // Assuming the API response is JSON
        
        // Return the deals to the frontend
        return response()->json(['deals' => $deals], 200);
    }
    public function createDeal(Request $request)
    {
        $formData = $request->validate([
            'dealName' => 'required',
            'dealStage' => 'required',
            'dealAccount' => 'required',
        ]);

        // return $formData;

        $zohoData = [
            'data' => [
                [
                    'Deal_Name' => $formData['dealName'],
                    'Stage' => $formData['dealStage'],
                    'Account_Name' => $formData['dealAccount']
                ],
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . Session::get('accessToken'), 
            'Content-Type' => 'application/json',
        ])->post(env('ZOHO_API_URL') . '/crm/v2/Deals', $zohoData);


        return response()->json(['message' => 'Form submitted successfully', 'response' => $response], 200);
    }

    public function updateDeal(Request $request, $id)
    {
        $formData = $request->validate([
            'dealName' => 'required',
            'dealStage' => 'required',
            'dealAccount' => 'required',
        ]);

        $zohoData = [
            'data' => [
                [
                    'Deal_Name' => $formData['dealName'],
                    'Stage' => $formData['dealStage'],
                    'Account_Name' => $formData['dealAccount']
                ],
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . Session::get('accessToken'), 
            'Content-Type' => 'application/json',
        ])->put(env('ZOHO_API_URL') . "/crm/v2/Deals/{$id}", $zohoData);

        // Example: Make API calls to update the deal in Zoho CRM here using $formData and $id

        return response()->json(['message' => 'Deal updated successfully'], 200);
    }

    public function createAcc(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'accountName' => 'required|string',
            'accountWebsite' => 'required|url',
            'accountPhone' => 'required|string',
        ]);

        // Prepare data for Zoho CRM API
        $payload = [
            "data" => [
                [
                    "Account_Name" => $validatedData['accountName'],
                    "Website" => $validatedData['accountWebsite'],
                    "Phone" => $validatedData['accountPhone'],
                ]
            ]
        ];

        // Make an HTTP POST request to create the account record
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . Session::get('accessToken'), // Add the Authorization header with the Zoho OAuth token
        ])->post(env('ZOHO_API_URL') . '/crm/v2/Accounts', $payload);

        // Check if the request was successful
        if ($response->successful()) {
            // Account record created successfully
            return response()->json(['message' => 'Account record created successfully'], 201);
        } else {
            // Error creating account record
            return response()->json(['error' => 'Failed to create account record'], $response->status());
        }
    }

    public function getStages()
    {
        // Step 1: Fetch Layout ID for Deals Module
        $layoutsUrl = env('ZOHO_API_URL') . '/crm/v2/settings/layouts?module=Deals';
        $layoutsResponse = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . Session::get('accessToken'),
        ])->get($layoutsUrl);
        $layoutsData = $layoutsResponse->json();
        
        
        // Check if layouts data contains layouts
        if (!isset($layoutsData['layouts'])) {
            return response()->json(['error' => 'No layouts found for Deals module'], 404);
        }

        // Assuming you want the first layout ID found for the Deals module
        $layoutId = $layoutsData['layouts'][0]['id']; // Assuming the first layout ID is what you need
        
        // Step 2: Use the Obtained Layout ID in the Stages Endpoint
        $url = env('ZOHO_API_URL') . '/crm/v2.1/settings/pipeline?layout_id=' . $layoutId;
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . Session::get('accessToken'), // Add the Authorization header with the Zoho OAuth token
        ])->get($url);
        $data = $response->json();
        
        // return $data;
        $stages = [];
        foreach ($data['pipeline'] as $pipeline) {
            foreach ($pipeline['maps'] as $map) {
                $stages[] = $map['actual_value'];
            }
        }

        return response()->json(['stages' => $stages], 200);
    }

    public function getAccounts()
    {
        // Make an HTTP GET request to fetch accounts from Zoho CRM API
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . Session::get('accessToken'), // Add the Authorization header with the Zoho OAuth token
        ])->get(env('ZOHO_API_URL') . '/crm/v2/Accounts');

        // Check if the request was successful
        if ($response->successful()) {
            $accountsData = $response->json()['data'];

            // Initialize an empty array to store the transformed data
            $accountsList = [];

            // Loop through the accounts data and extract required fields
            foreach ($accountsData as $account) {
                $accountInfo = [
                    'id' => $account['id'],
                    'name' => $account['Account_Name'],
                ];

                // Push the extracted data into the accounts list array
                $accountsList[] = $accountInfo;
            }

            // Return the transformed list of accounts
            return response()->json(['accounts' => $accountsList], 200);
        } else {
            // Error fetching accounts
            return response()->json(['error' => 'Failed to fetch accounts'], $response->status());
        }
    }

    public function getDeals()
    {   
        // Make an HTTP GET request to fetch accounts from Zoho CRM API
        $response = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . Session::get('accessToken'), // Add the Authorization header with the Zoho OAuth token
        ])->get(env('ZOHO_API_URL') . '/crm/v2/Deals');
        
        // Check if the request was successful
        if ($response->successful()) {
            // return $response->json()['data'];
            $dealsData = $response->json()['data'];
            
            // Initialize an empty array to store the transformed data
            $dealsList = [];

            
            // Loop through the accounts data and extract required fields
            foreach ($dealsData as $deal) {
                $formattedDateTime = Date::parse($deal['Created_Time'])->format('d.m.Y (H:i)');
                $dealsInfo = [
                    'id' => $deal['id'],
                    'name' => $deal['Deal_Name'],
                    'stage' => $deal['Stage'],
                    'create_time' => $formattedDateTime,
                    'editable' => $deal['$editable'],
                    'description' => $deal['Description'],
                    'account_name' => !empty($deal['Account_Name']) ? $deal['Account_Name'] : ['name' => 'No account'],
                ];

                // Push the extracted data into the accounts list array
                $dealsList[] = $dealsInfo;
            }

            // Return the transformed list of accounts
            return response()->json(['list_deals' => $dealsList], 200);
        } else {
            // Error fetching accounts
            return response()->json(['error' => 'Failed to fetch accounts'], $response->status());
        }
    }
}

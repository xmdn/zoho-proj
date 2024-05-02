<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\DealAccountController;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        // Handle the single invoke action here
        // You can access request parameters using $request

        return Inertia::render('Home');
    }
    public function loadPage($page)
    {
        
        if (file_exists(resource_path('js/Pages/' . $page . '.vue'))) {
            // dd("'" . $page . "'");
            return Inertia::render($page);
        }

        // If the page doesn't exist, return a 404 response or handle it as needed
        abort(404, 'Page not found');
    }
    public function getDealById($id)
    {
        $dealController = new DealAccountController();
        $response = $dealController->getDeals();
        $accountsAll = $dealController->getAccounts();
        $stagesAll = $dealController->getStages(); 
        // return $stagesAll;
        // Get the JSON data from the response
        $dealsList = $response->getData(true);
        $foundDeal = null;
        foreach ($dealsList['list_deals'] as $deal) {
            if ($deal['id'] == $id) {
                $foundDeal = $deal;
                break;
            }
        }
        return Inertia::render('SingleDeal', ['deal' => $foundDeal, 'accounts' => $accountsAll, 'stages' => $stagesAll]);
    }
}

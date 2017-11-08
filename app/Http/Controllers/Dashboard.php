<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Store as StoreModel;

class Dashboard extends Controller
{
    //This is the function that loads the dashboard page
    public function index(Request $request)
    {
        $data['menu_key'] = "MM_DASHBOARD";
        $data['sub_menu_key'] = "SM_MASTER_DASHBOARD";
        check_access(array($data['menu_key'], $data['sub_menu_key']));

        $data['store'] = StoreModel::select('currency_name', 'currency_code')
        ->where('id', $request->logged_user_store_id)
        ->first();

        return view('dashboard.dashboard', $data);
    }

    public function billing_counter_dashboard(Request $request)
    {
        $data['menu_key'] = "MM_DASHBOARD";
        $data['sub_menu_key'] = "SM_BILLING_COUNTER_DASHBOARD";
        check_access(array($data['menu_key'], $data['sub_menu_key']));

        $data['store'] = StoreModel::select('currency_name', 'currency_code')
        ->where('id', $request->logged_user_store_id)
        ->first();

        return view('dashboard.billing_counter_dashboard', $data);
    }
}

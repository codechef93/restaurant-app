<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\BillingCounter as BillingCounterModel;

use App\Http\Resources\BillingCounterResource;

class BillingCounter extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_BILLING_COUNTERS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('billing_counter.billing_counters', $data);
    }

    //This is the function that loads the add/edit page
    public function add_billing_counter($slack = null){
        //check access
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_BILLING_COUNTERS';
        $data['action_key'] = ($slack == null)?'A_ADD_BILLING_COUNTER':'A_EDIT_BILLING_COUNTER';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('BILLING_COUNTER_STATUS')->active()->sortValueAsc()->get();

        $data['billing_counter_data'] = null;
        if(isset($slack)){
            $billing_counter = BillingCounterModel::where('slack', '=', $slack)->first();
            if (empty($billing_counter)) {
                abort(404);
            }
            
            $billing_counter_data = new BillingCounterResource($billing_counter);
            $data['billing_counter_data'] = $billing_counter_data;
        }

        return view('billing_counter.add_billing_counter', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_BILLING_COUNTERS';
        $data['action_key'] = 'A_DETAIL_BILLING_COUNTER';
        check_access([$data['action_key']]);

        $billing_counter = BillingCounterModel::where('slack', '=', $slack)->first();
        
        if (empty($billing_counter)) {
            abort(404);
        }

        $billing_counter_data = new BillingCounterResource($billing_counter);
        
        $data['billing_counter_data'] = $billing_counter_data;

        return view('billing_counter.billing_counter_detail', $data);
    }
}

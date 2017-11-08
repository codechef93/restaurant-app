<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use App\Models\PaymentMethod as PaymentMethodModel;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\PaymentMethodResource;

class PaymentMethod extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_PAYMENT_METHOD';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('payment_method.payment_methods', $data);
    }

    //This is the function that loads the add/edit page
    public function add_payment_method($slack = null){
        //check access
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_PAYMENT_METHOD';
        $data['action_key'] = ($slack == null)?'A_ADD_PAYMENT_METHOD':'A_EDIT_PAYMENT_METHOD';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('PAYMENT_METHOD_STATUS')->active()->sortValueAsc()->get();

        $data['payment_method_data'] = null;
        if(isset($slack)){
            
            $payment_method = PaymentMethodModel::where('slack', '=', $slack)->first();
            if (empty($payment_method)) {
                abort(404);
            }

            $payment_method_data = new PaymentMethodResource($payment_method);
            $data['payment_method_data'] = $payment_method_data;
        }

        return view('payment_method.add_payment_method', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_PAYMENT_METHOD';
        $data['action_key'] = 'A_DETAIL_PAYMENT_METHOD';
        check_access([$data['action_key']]);

        $payment_method = PaymentMethodModel::where('slack', '=', $slack)->first();
        
        if (empty($payment_method)) {
            abort(404);
        }

        $payment_method_data = new PaymentMethodResource($payment_method);
        
        $data['payment_method_data'] = $payment_method_data;

        return view('payment_method.payment_method_detail', $data);
    }
}

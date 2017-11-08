<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Config;

use App\Models\MasterAccountType as MasterAccountTypeModel;
use App\Models\MasterBillingType as MasterBillingTypeModel;
use App\Models\MasterInvoicePrintType as MasterInvoicePrintTypeModel;
use App\Models\MasterOrderType as MasterOrderTypeModel;
use App\Models\MasterStatus as MasterStatusModel;
use App\Models\MasterTransactionType as MasterTransactionTypeModel;

class Master extends Controller
{
    public function get_billing_master_account_type(){
        try {
            $account_types = MasterAccountTypeModel::select('account_type_constant', 'label', 'description')
            ->active()
            ->get();

            return response()->json($this->generate_response(
                array(
                    "message" => "Account types loaded successfully", 
                    "data"    => $account_types
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function get_billing_type(){
        try {
            $billing_types = MasterBillingTypeModel::select('billing_type_constant', 'label', 'description')
            ->active()
            ->get();

            return response()->json($this->generate_response(
                array(
                    "message" => "Billing types loaded successfully", 
                    "data"    => $billing_types
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function get_master_invoice_print_type(){
        try {
            $print_types = MasterInvoicePrintTypeModel::select('print_type_value', 'print_type_label')
            ->active()
            ->get();

            return response()->json($this->generate_response(
                array(
                    "message" => "Print types loaded successfully", 
                    "data"    => $print_types
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function get_master_order_type(){
        try {
            $order_types = MasterOrderTypeModel::select('order_type_constant', 'label', 'restaurant', 'description')
            ->active()
            ->get();

            return response()->json($this->generate_response(
                array(
                    "message" => "Order types loaded successfully", 
                    "data"    => $order_types
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function get_master_status(Request $request){
        try {

            $status_key = $request->status_key;

            $master_status = MasterStatusModel::select('key', 'value', 'value_constant', 'label')
            ->active()
            ->when($status_key != '', function ($query) use ($status_key) {
                $query->where('key', '=', $status_key);
            })
            ->get();

            return response()->json($this->generate_response(
                array(
                    "message" => "Master status loaded successfully", 
                    "data"    => $master_status
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function get_master_transaction_type(Request $request){
        try {
            $transaction_types = MasterTransactionTypeModel::select('transaction_type_constant', 'label')
            ->active()
            ->get();

            return response()->json($this->generate_response(
                array(
                    "message" => "Transaction types loaded successfully", 
                    "data"    => $transaction_types
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }
    
}

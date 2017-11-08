<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

use App\Http\Resources\OrderResource;

use App\Models\Order as OrderModel;
use App\Models\PaymentMethod as PaymentMethodModel;
use App\Models\Transaction as TransactionModel;
use App\Models\MasterStatus as MasterStatusModel;
use App\Models\MasterTransactionType as MasterTransactionTypeModel;
use App\Models\Customer as CustomerModel;
use App\Models\Account as AccountModel;

use Stripe;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;

use App\Http\Controllers\API\Notification as NotificationAPI;
use App\Http\Controllers\API\Order as OrderAPI;

class PaymentGateway extends Controller
{
    private function update_order_closed_for_payment_gateway($order_slack, $public_order = false, $payment_gateway = ''){
        $order_status_data = MasterStatusModel::select('value')->filterByValueConstant('ORDER_STATUS', 'CLOSED')->first();  
        $payment_status_data = MasterStatusModel::select('value')->filterByValueConstant('ORDER_PAYMENT_STATUS', 'PAYMENT_SUCCESS')->first();

        $order = [
            "status" => $order_status_data->value,
            "payment_status" => $payment_status_data->value
        ];
        if($public_order == true){
            if($payment_gateway != ''){
                
                $order_data = OrderModel::withoutGlobalScopes()->select('store_id')->where('slack', $order_slack)->first();

                $default_business_account = AccountModel::withoutGlobalScopes()->select('id')
                ->where('pos_default', '=', 1)
                ->where('store_id', '=', $order_data->store_id)
                ->active()
                ->first();

                $business_account = AccountModel::withoutGlobalScopes()->select('id')
                ->where('pos_default', '=', 1)
                ->where('store_id', '=', $order_data->store_id)
                ->active()
                ->first();

                $payment_method = PaymentMethodModel::where('payment_constant', '=', $payment_gateway)->first();

                $order['payment_method_id'] = (isset($payment_method->id))?$payment_method->id:0;
                $order['payment_method_slack'] = (isset($payment_method->slack))?$payment_method->slack:'';
                $order['payment_method'] = (isset($payment_method->label))?$payment_method->label:'';

                $order['business_account_id'] = (isset($default_business_account)?$default_business_account->id:(isset($business_account)?$business_account->id:0));
            }
            //unset($order["status"]);
        }
        
        $action_response = OrderModel::withoutGlobalScopes()->where('slack', $order_slack)
        ->update($order);

        $request = request();
        $order_api = new OrderAPI();
        $order_api->update_order_product_inventory($request, $order_slack);
    }

    public function get_stripe_payment_intent(Request $request){

        $order_slack = $request->order_slack;
        $order = OrderModel::withoutGlobalScopes()->select('*')->where('slack', $order_slack)->first();
        $order_data = new OrderResource($order);
        $order_data = collect($order_data)->toArray();

        $order_status_master = MasterStatusModel::select('value')->filterByValueConstant('ORDER_STATUS', 'CLOSED')->first();
        
        if($order_status_master->value == $order->status){
            return false;
        }

        $payment_method = PaymentMethodModel::where('payment_constant', '=', 'STRIPE')->first();
        $secret_key = $payment_method->key_1;
        $publishable_key = $payment_method->key_2;

        \Stripe\Stripe::setApiKey($secret_key);

        //zero decimal currency should not multiply by 100
        if(in_array(strtoupper($order_data['currency_code']), ["BIF", "CLP", "DJF", "GNF", "JPY", "KMF", "KRW", "MGA", "PYG", "RWF", "UGX", "VND", "VUV", "XAF", "XOF", "XPF"])){
            $total_order_amount = $order_data['total_order_amount_rounded'];
        }else{
            $total_order_amount = ($order_data['total_order_amount']*100);
        }
        
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $total_order_amount,
            'currency' => strtolower($order_data['currency_code']),
            'shipping' => [
                'name' => ($order_data['customer_email'] != '')?$order_data['customer_email']:$order_data['customer_phone'],
                'address' => [
                    'line1' => $order_data['store']['address'],
                    'postal_code' => $order_data['store']['pincode'],
                    'city' => '',
                    'state' => '',
                    'country' => ($order_data['store'])?($order_data['store']['country']?strtolower($order_data['store']['country']['code']):'us'):'us',
                ],
            ],
            'description' => 'Appsthing POS Sale ['.$order_data['total_order_amount'].']',
        ]);
        
        $output = [
            'publishableKey' => $publishable_key,
            'clientSecret' => $paymentIntent->client_secret,
        ];
        
        echo json_encode($output);
    }

    public function record_stripe_payment_success(Request $request){

        $order_slack = $request->order_slack;
        $stripe_response = json_decode($request->stripe_response,true);
        $public_order = $request->public_order;

        $transaction_type_data = MasterTransactionTypeModel::filterByTransactionTypeConstant('INCOME')->active()->first();

        DB::beginTransaction();

        $this->update_order_closed_for_payment_gateway($order_slack, $public_order, "STRIPE");
        
        $order_detail = OrderModel::withoutGlobalScopes()->select('*')->where('slack', $order_slack)->first();

        /* record transaction */
        $payment_gateway_response = [
            'id' => $stripe_response['id'],
            'status' => $stripe_response['status']
        ];
        $this->record_payment_gateway_transaction($order_detail, $payment_gateway_response);
        /* record transaction end */

        DB::commit();
        
    }

    public function record_paypal_payment_success($order_slack, $paypal_response, $public_order = false){

        $public_order = (isset($public_order))?$public_order:false;
        $order_slack = $order_slack;
        $paypal_response = $paypal_response;
        
        $transaction_type_data = MasterTransactionTypeModel::filterByTransactionTypeConstant('INCOME')->active()->first();

        DB::beginTransaction();

        $this->update_order_closed_for_payment_gateway($order_slack, $public_order, "PAYPAL");

        $order_detail = OrderModel::withoutGlobalScopes()->select('*')->where('slack', $order_slack)->first();

        /* record transaction */
        $payment_gateway_response = [
            'id' => $paypal_response->id,
            'status' => $paypal_response->status
        ];
        $this->record_payment_gateway_transaction($order_detail, $payment_gateway_response);
        /* record transaction end */

        DB::commit();
        
    }

    public function get_paypal_order_data(Request $request){

        $order_slack = $request->order_slack;
        $order_id = $request->order_id;
        $public_order = $request->public_order;

        $payment_method = PaymentMethodModel::where('payment_constant', '=', 'PAYPAL')->first();
        $client_secret = $payment_method->key_1;
        $client_id = $payment_method->key_2;

        $clientId = $client_id;
        $clientSecret = $client_secret;

        $paypal_env = new SandboxEnvironment($clientId, $clientSecret);
    
        $client = new PayPalHttpClient($paypal_env);
        $response = $client->execute(new OrdersGetRequest($order_id));

        $paypal_response = $response->result;

        $this->record_paypal_payment_success($order_slack, $paypal_response, $public_order);

    }

    public function record_payment_gateway_transaction($order_detail, $payment_gateway_response){


        $transaction_id_exists = TransactionModel::withoutGlobalScopes()->select('id')
        ->where([
            ['payment_method', '=', $order_detail->payment_method],
            ['pg_transaction_id', '=', $payment_gateway_response['id']],
        ])
        ->first();
        if (empty($transaction_id_exists)) {
    
            $transaction_type_data = MasterTransactionTypeModel::select('id')
            ->where('transaction_type_constant', '=', 'INCOME')
            ->first();
            if (empty($transaction_type_data)) {
                throw new Exception("Invalid transaction type selected", 400);
            }

            $customer_data = CustomerModel::select('id', 'name', 'email', 'phone', 'address')
            ->where('id', '=', $order_detail->customer_id)
            ->first();

            $transaction = [
                "slack" => $this->generate_slack("transactions"),
                "store_id" => $order_detail->store_id,
                "transaction_code" => Str::random(6),
                "account_id" => $order_detail->business_account_id,
                "transaction_type" => $transaction_type_data->id,
                "payment_method_id" => $order_detail->payment_method_id,
                "payment_method" => $order_detail->payment_method,
                "bill_to" => 'POS_ORDER',
                "bill_to_id" => $order_detail->id,
                "bill_to_name" => (isset($customer_data->name))?$customer_data->name:'Walkin Customer',
                "bill_to_contact" => $order_detail->customer_phone,
                "bill_to_address" => (isset($customer_data->address))?$customer_data->address:'',
                "currency_code" => $order_detail->currency_code,
                "amount" => $order_detail->total_order_amount,
                "pg_transaction_id" => $payment_gateway_response['id'],
                "pg_transaction_status" => $payment_gateway_response['status'],
                "notes" => '',
                "transaction_date" => date('Y-m-d'),
                "created_by" => (isset(request()->logged_user_id) && request()->logged_user_id != null)?request()->logged_user_id:$order_detail->created_by
            ];
            
            $transaction_id = TransactionModel::withoutGlobalScopes()->create($transaction)->id;
            
            $code_start_config = Config::get('constants.unique_code_start.transaction');
            $code_start = (isset($code_start_config))?$code_start_config:100;
            
            $transaction_code = [
                "transaction_code" => ($code_start+$transaction_id)
            ];
            TransactionModel::withoutGlobalScopes()->where('id', $transaction_id)
            ->update($transaction_code);

            $notification_api = new NotificationAPI();
            $notification_api->send_sms('POS_SALE_BILL_MESSAGE', $order_detail->slack);
        }
    }

    public function record_razorpay_payment_success(Request $request){

        $order_slack = $request->order_slack;
        $razorpay_response = json_decode($request->razorpay_response,true);
        $public_order = $request->public_order;

        $transaction_type_data = MasterTransactionTypeModel::filterByTransactionTypeConstant('INCOME')->active()->first();

        DB::beginTransaction();

        $this->update_order_closed_for_payment_gateway($order_slack, $public_order, "RAZORPAY");

        $order_detail = OrderModel::withoutGlobalScopes()->select('*')->where('slack', $order_slack)->first();
        
        /* record transaction */
        $payment_gateway_response = [
            'id' => $razorpay_response['razorpay_order_id'],
            'status' => $razorpay_response['razorpay_signature']
        ];
        $this->record_payment_gateway_transaction($order_detail, $payment_gateway_response);
        /* record transaction end */

        DB::commit();
        
    }

    public function get_stripe_payment_intent_public(Request $request){
        try{
            $payment_intent = $this->get_stripe_payment_intent($request);
            echo $payment_intent;
        }catch(Exception $e){

        }
    }

    public function record_stripe_payment_success_public(Request $request){
        try{
            $this->record_stripe_payment_success($request);
        }catch(Exception $e){

        }
    }

    public function record_razorpay_payment_success_public(Request $request){
        try{
            $this->record_razorpay_payment_success($request);
        }catch(Exception $e){

        }
    }

    public function get_paypal_order_data_public(Request $request){
        try{
            $this->get_paypal_order_data($request);
        }catch(Exception $e){

        }
    }
}
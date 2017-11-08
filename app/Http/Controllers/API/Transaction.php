<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

use App\Http\Resources\TransactionResource;

use App\Models\Account as AccountModel;
use App\Models\Transaction as TransactionModel;
use App\Models\MasterTransactionType as MasterTransactionTypeModel;
use App\Models\PaymentMethod as PaymentMethodModel;
use App\Models\Store as StoreModel;
use App\Models\Supplier as SupplierModel;
use App\Models\Customer as CustomerModel;
use App\Models\Invoice as InvoiceModel;
use App\Models\User as UserModel;

use App\Http\Resources\Collections\TransactionCollection;

class Transaction extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_TRANSACTION_LISTING';
            if(check_access(array($data['action_key']), true) == false){
                $response = $this->no_access_response_for_listing_table();
                return $response;
            }

            $item_array = array();

            $draw = $request->draw;
            $limit = $request->length;
            $offset = $request->start;
            
            $order_by = $request->order[0]["column"];
            $order_direction = $request->order[0]["dir"];
            $order_by_column =  $request->columns[$order_by]['name'];

            $filter_string = $request->search['value'];
            $filter_columns = array_filter(data_get($request->columns, '*.name'));
            
            $query = TransactionModel::select('transactions.*', 'user_created.fullname')
            ->take($limit)
            ->skip($offset)
            ->masterTransactionTypeJoin()
            ->accountJoin()
            ->createdUser()

            ->when($order_by_column, function ($query, $order_by_column) use ($order_direction) {
                $query->orderBy($order_by_column, $order_direction);
            }, function ($query) {
                $query->orderBy('created_at', 'desc');
            })

            ->when($filter_string, function ($query, $filter_string) use ($filter_columns) {
                $query->where(function ($query) use ($filter_string, $filter_columns){
                    foreach($filter_columns as $filter_column){
                        $query->orWhere($filter_column, 'like', '%'.$filter_string.'%');
                    }
                });
            })

            ->get();

            $transactions = TransactionResource::collection($query);
           
            $total_count = TransactionModel::select("id")->get()->count();

            $item_array = [];
            foreach($transactions as $key => $transaction){
                
                $transaction = $transaction->toArray($request);

                $item_array[$key][] = $transaction['transaction_code'];
                $item_array[$key][] = $transaction['transaction_date'];
                $item_array[$key][] = (isset($transaction['transaction_type_data']['label']))?$transaction['transaction_type_data']['label']:'-';
                $item_array[$key][] = (isset($transaction['account']['label']))?$transaction['account']['label']:'-';
                $item_array[$key][] = $transaction['payment_method'];
                $item_array[$key][] = str_replace('_', ' ', Str::title($transaction['bill_to']));
                $item_array[$key][] = ($transaction['bill_to_name'])?$transaction['bill_to_name']:'-';
                $item_array[$key][] = $transaction['amount'];
                $item_array[$key][] = $transaction['created_at_label'];
                $item_array[$key][] = $transaction['updated_at_label'];
                $item_array[$key][] = (isset($transaction['created_by']['fullname']))?$transaction['created_by']['fullname']:'-';
                $item_array[$key][] = view('transaction.layouts.transaction_actions', ['transaction' => $transaction])->render();
            }

            $response = [
                'draw' => $draw,
                'recordsTotal' => $total_count,
                'recordsFiltered' => $total_count,
                'data' => $item_array
            ];
            
            return response()->json($response);
        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            if(!check_access(['A_ADD_TRANSACTION'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);
            
            if($request->bill_to == 'SUPPLIER'){
                $supplier_data = SupplierModel::select('id', 'name', 'supplier_code', 'email', 'phone', 'address', 'pincode')
                ->where('slack', '=', trim($request->bill_to_slack))
                ->active()
                ->first();
                if (empty($supplier_data)) {
                    throw new Exception("Invalid supplier selected", 400);
                }
    
                $bill_to_id = $supplier_data->id;
                $bill_to_name = $supplier_data->name;
                $bill_to_contact = implode(', ',[$supplier_data->phone, $supplier_data->email]);
                $bill_to_address = $supplier_data->address . ','.$supplier_data->pincode;
    
            }else if($request->bill_to == 'CUSTOMER'){
                $customer_data = CustomerModel::select('id', 'name', 'email', 'address', 'phone')
                ->where('slack', '=', trim($request->bill_to_slack))
                ->active()
                ->first();
                if (empty($customer_data)) {
                    throw new Exception("Invalid customer selected", 400);
                }
    
                $bill_to_id = $customer_data->id;
                $bill_to_name = $customer_data->name;
                $bill_to_contact = implode(', ',[$customer_data->phone, $customer_data->email]);
                $bill_to_address = $customer_data->address;
            }else if($request->bill_to == 'INVOICE'){
                $invoice_data = InvoiceModel::select('id', 'bill_to_id', 'bill_to_name', 'bill_to_email', 'bill_to_contact', 'bill_to_address')
                ->where('slack', '=', trim($request->bill_to_slack))
                ->first();
                if (empty($invoice_data)) {
                    throw new Exception("Invalid invoice selected", 400);
                }
    
                $bill_to_id = $invoice_data->id;
                $bill_to_name = $invoice_data->bill_to_name;
                $bill_to_contact = implode(', ',[$invoice_data->bill_to_contact, $invoice_data->bill_to_email]);
                $bill_to_address = $invoice_data->bill_to_address;
            }else if($request->bill_to == 'STAFF'){
                $user_data = UserModel::select('id', 'fullname', 'email', 'phone')
                ->where('slack', '=', trim($request->bill_to_slack))
                ->active()
                ->first();
                if (empty($user_data)) {
                    throw new Exception("Invalid user selected", 400);
                }
    
                $bill_to_id = $user_data->id;
                $bill_to_name = $user_data->fullname;
                $bill_to_contact = implode(', ',[$user_data->phone, $user_data->email]);
                $bill_to_address = '';
            }

            $account_data = AccountModel::select('id')
            ->where('slack', '=', trim($request->account))
            ->first();
            if (empty($account_data)) {
                throw new Exception("Invalid account selected", 400);
            }

            $transaction_type_data = MasterTransactionTypeModel::select('id')
            ->where('transaction_type_constant', '=', trim($request->transaction_type))
            ->first();
            if (empty($transaction_type_data)) {
                throw new Exception("Invalid transaction type selected", 400);
            }

            $payment_method_data = PaymentMethodModel::select('id', 'label')
            ->where('slack', '=', trim($request->payment_method))
            ->first();
            if (empty($payment_method_data)) {
                throw new Exception("Invalid payment method selected", 400);
            }

            $store_data = StoreModel::select('currency_name', 'currency_code')
            ->where([
                ['stores.id', '=', $request->logged_user_store_id]
            ])
            ->active()
            ->first();
            if (empty($store_data)) {
                throw new Exception("Invalid store selected");
            }

            DB::beginTransaction();
            
            $transaction = [
                "slack" => $this->generate_slack("transactions"),
                "store_id" => $request->logged_user_store_id,
                "transaction_code" => Str::random(6),
                "account_id" => $account_data->id,
                "transaction_type" => $transaction_type_data->id,
                "payment_method_id" => $payment_method_data->id,
                "payment_method" => $payment_method_data->label,
                "bill_to" => $request->bill_to,
                "bill_to_id" => $bill_to_id,
                "bill_to_name" => $bill_to_name,
                "bill_to_contact" => $bill_to_contact,
                "bill_to_address" => $bill_to_address,
                "currency_code" => $store_data->currency_code,
                "amount" => $request->amount,
                "notes" => $request->notes,
                "transaction_date" => $request->transaction_date,
                "created_by" => $request->logged_user_id
            ];
            
            $transaction_id = TransactionModel::create($transaction)->id;

            $code_start_config = Config::get('constants.unique_code_start.transaction');
            $code_start = (isset($code_start_config))?$code_start_config:100;
            
            $transaction_code = [
                "transaction_code" => ($code_start+$transaction_id)
            ];
            TransactionModel::where('id', $transaction_id)
            ->update($transaction_code);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Transaction added successfully", 
                    "data"    => $transaction['slack']
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

    /**
     * Display the specified resource.
     *
     * @param  int  $slack
     * @return \Illuminate\Http\Response
     */
    public function show($slack)
    { 
        try {

            if(!check_access(['A_DETAIL_TRANSACTION'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = TransactionModel::select('*')
            ->where('slack', $slack)
            ->first();

            $item_data = new TransactionResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "Transaction loaded successfully", 
                    "data"    => $item_data
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

    /**
     * list all the specified resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        try {

            if(!check_access(['A_VIEW_TRANSACTION_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $list = new TransactionCollection(TransactionModel::select('*')
            ->orderBy('created_at', 'desc')->paginate());

            return response()->json($this->generate_response(
                array(
                    "message" => "Transactions loaded successfully", 
                    "data"    => $list
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $slack
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slack)
    {
        try{
            
            if(!check_access(['A_DELETE_TRANSACTION'], true)){
                throw new Exception("Invalid request", 400);
            }

            $transaction_detail = TransactionModel::select('id')->where('slack', $slack)->first();
            if (empty($transaction_detail)) {
                throw new Exception("Invalid transaction provided", 400);
            }

            $transaction_id = $transaction_detail->id;

            DB::beginTransaction();

            TransactionModel::where([
                ['id', '=', $transaction_id],
            ])->delete();

            DB::commit();

            $forward_link = route('transactions');

            return response()->json($this->generate_response(
                array(
                    "message" => "Transaction deleted successfully", 
                    "data" => $slack,
                    "link" => $forward_link
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

    public function filter_transactions(Request $request){
        try{

            $keyword = $request->keyword;

            $transaction_list = TransactionModel::select("*")
            ->where('transaction_code', 'like', $keyword.'%')
            ->orWhere('payment_method', 'like', $keyword.'%')
            ->orWhere('bill_to_contact', 'like', $keyword.'%')
            ->orWhere('bill_to_name', 'like', $keyword.'%')
            ->limit(25)
            ->get();
            
            $transactions = TransactionResource::collection($transaction_list);
           
            return response()->json($this->generate_response(
                array(
                    "message" => "Transactions filtered successfully", 
                    "data" => $transactions
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

    public function validate_request($request)
    {
        $validator = Validator::make($request->all(), [
            'bill_to_slack' => $this->get_validation_rules("slack", true),
            'bill_to' => $this->get_validation_rules("string", true),
            'transaction_date' => 'date|required',
            'account' => $this->get_validation_rules("slack", true),
            'transaction_type' => $this->get_validation_rules("string", true),
            'amount' => $this->get_validation_rules("numeric", true),
            'payment_method' => $this->get_validation_rules("slack", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}

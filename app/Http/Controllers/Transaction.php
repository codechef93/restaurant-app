<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction as TransactionModel;
use App\Models\MasterTransactionType as MasterTransactionTypeModel;
use App\Models\Account as AccountModel;
use App\Models\PaymentMethod as PaymentMethodModel;

use App\Http\Resources\TransactionResource;

class Transaction extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_TRANSACTIONS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('transaction.transactions', $data);
    }

    //This is the function that loads the add/edit page
    public function add_transaction($slack = null){
        //check access
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_TRANSACTIONS';
        $data['action_key'] = ($slack == null)?'A_ADD_TRANSACTION':'A_EDIT_TRANSACTION';
        check_access(array($data['action_key']));

        $data['transaction_type'] = MasterTransactionTypeModel::select('transaction_type_constant', 'label')
        ->active()
        ->get();

        $data['accounts'] = AccountModel::select('accounts.slack', 'accounts.label', 'master_account_type.label as account_type_label')
        ->masterAccountTypeJoin()
        ->active()
        ->get();

        $data['payment_methods'] = PaymentMethodModel::select('slack', 'label')
        ->active()
        ->get();
        
        $data['transaction_data'] = null;
        if(isset($slack)){
            
            abort(404);

            $transaction = TransactionModel::where('slack', '=', $slack)->first();
            if (empty($transaction)) {
                abort(404);
            }
            
            $transaction_data = new TransactionResource($transaction);
            $data['transaction_data'] = $transaction_data;
        }

        return view('transaction.add_transaction', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_TRANSACTIONS';
        $data['action_key'] = 'A_DETAIL_TRANSACTION';
        check_access([$data['action_key']]);

        $transaction = TransactionModel::where('slack', '=', $slack)->first();
        
        if (empty($transaction)) {
            abort(404);
        }

        $transaction_data = new TransactionResource($transaction);
        
        $data['transaction_data'] = $transaction_data;

        $data['delete_transaction_access'] = check_access(['A_DELETE_TRANSACTION'] ,true);

        return view('transaction.transaction_detail', $data);
    }
}

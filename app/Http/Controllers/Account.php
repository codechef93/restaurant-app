<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Account as AccountModel;
use App\Models\MasterAccountType as MasterAccountTypeModel;

use App\Http\Resources\AccountResource;

class Account extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_ACCOUNT';
        $data['sub_menu_key'] = 'SM_ACCOUNTS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('account.accounts', $data);
    }

    //This is the function that loads the add/edit page
    public function add_account($slack = null){
        //check access
        $data['menu_key'] = 'MM_ACCOUNT';
        $data['sub_menu_key'] = 'SM_ACCOUNTS';
        $data['action_key'] = ($slack == null)?'A_ADD_ACCOUNT':'A_EDIT_ACCOUNT';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('ACCOUNT_STATUS')->active()->sortValueAsc()->get();

        $data['account_types'] = MasterAccountTypeModel::select('account_type_constant', 'label')->active()->get();

        $data['account_data'] = null;
        if(isset($slack)){
            $account = AccountModel::where('slack', '=', $slack)->first();
            if (empty($account)) {
                abort(404);
            }
            
            $account_data = new AccountResource($account);
            $data['account_data'] = $account_data;
        }

        return view('account.add_account', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_ACCOUNT';
        $data['sub_menu_key'] = 'SM_ACCOUNTS';
        $data['action_key'] = 'A_DETAIL_ACCOUNT';
        check_access([$data['action_key']]);

        $account = AccountModel::where('slack', '=', $slack)->first();
        
        if (empty($account)) {
            abort(404);
        }

        $account_data = new AccountResource($account);
        
        $data['account_data'] = $account_data;

        return view('account.account_detail', $data);
    }
}

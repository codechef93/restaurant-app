<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Target as TargetModel;
use App\Models\Store as StoreModel;

use App\Http\Resources\TargetResource;

class Target extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_ACCOUNT';
        $data['sub_menu_key'] = 'SM_TARGET';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        $data['store'] = StoreModel::select('currency_name', 'currency_code')
        ->where('id', request()->logged_user_store_id)
        ->first();
        
        return view('target.targets', $data);
    }

    //This is the function that loads the add/edit page
    public function add_target($slack = null){
        //check access
        $data['menu_key'] = 'MM_ACCOUNT';
        $data['sub_menu_key'] = 'SM_TARGET';
        $data['action_key'] = ($slack == null)?'A_ADD_TARGET':'A_EDIT_TARGET';
        check_access(array($data['action_key']));

        $data['store'] = StoreModel::select('currency_name', 'currency_code')
        ->where('id', request()->logged_user_store_id)
        ->first();

        $data['target_data'] = null;
        if(isset($slack)){
            $target = TargetModel::where('slack', '=', $slack)->first();
            if (empty($target)) {
                abort(404);
            }
            
            $target_data = new TargetResource($target);
            $data['target_data'] = $target_data;
        }

        return view('target.add_target', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_ACCOUNT';
        $data['sub_menu_key'] = 'SM_TARGET';
        $data['action_key'] = 'A_DETAIL_TARGET';
        check_access([$data['action_key']]);

        $target = TargetModel::where('slack', '=', $slack)->first();
        
        if (empty($target)) {
            abort(404);
        }

        $data['store'] = StoreModel::select('currency_name', 'currency_code')
        ->where('id', request()->logged_user_store_id)
        ->first();

        $data['delete_target_access'] = check_access(['A_DELETE_TARGET'], true);

        $target_data = new TargetResource($target);
        
        $data['target_data'] = $target_data;

        return view('target.target_detail', $data);
    }
}

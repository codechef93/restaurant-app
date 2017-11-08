<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\MasterStatus;
use App\Models\Table as TableModel;
use App\Models\Store as StoreModel;
use App\Models\Role as RoleModel;
use App\Models\User as UserModel;
use App\RestoArea;

use App\Http\Resources\TableResource;
use App\Http\Resources\UserResource;

class Table extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_RESTAURANT';
        $data['sub_menu_key'] = 'SM_RESTAURANT_TABLES';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        $store_data = StoreModel::select('id', 'restaurant_waiter_role_id')
        ->where([
            ['id', '=', $request->logged_user_store_id],
            ['status', '=', 1]
        ])
        ->first();

        $data['store_slack'] = $request->logged_user_store_slack;
        
        $data['items'] = RestoArea::where('restaurant_id', $store_data->id)->get();
        
        $data['parameter_name'] = 'restoarea';
        
        return view('table.tables', $data);
    }

    //This is the function that loads the add/edit page
    public function add_table(Request $request, $slack = null){
        //check access
        $data['menu_key'] = 'MM_RESTAURANT';
        $data['sub_menu_key'] = 'SM_RESTAURANT_TABLES';
        $data['action_key'] = ($slack == null)?'A_ADD_RESTAURANT_TABLE':'A_EDIT_RESTAURANT_TABLE';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('RESTAURANT_TABLE_STATUS')->active()->sortValueAsc()->get();

        $store_data = StoreModel::select('restaurant_waiter_role_id')
        ->where([
            ['id', '=', $request->logged_user_store_id],
            ['status', '=', 1]
        ])
        ->first();

        $data['waiter_list'] = null;
        if(isset($store_data) && $store_data->restaurant_waiter_role_id != ''){
            $waiter_role = RoleModel::select('id')
            ->where('id', '=', $store_data->restaurant_waiter_role_id)
            ->active()
            ->first();

            $store_waiter_role_id = (!empty($waiter_role))?$waiter_role->id:'';

            $user_list = UserModel::select('*')
            ->where('role_id', '=', $store_waiter_role_id)
            ->hideSuperAdminRole()
            ->active()
            ->get();

            $data['waiter_list'] = UserResource::collection($user_list);
        }

        $data['table_data'] = null;
        $data['menu_link'] = null;
        
        $data['restoareas'] = RestoArea::where('restaurant_id', $request->logged_user_store_id)->get();

        if(isset($slack)){
            $table = TableModel::where('slack', '=', $slack)->first();

            if (empty($table)) {
                abort(404);
            }
            
            $table_data = new TableResource($table);

            $data['table_data'] = $table_data;

            $data['menu_link'] = route('our_menu', [$request->logged_user_store_slack, $slack]);
        }

        return view('table.add_table', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_RESTAURANT';
        $data['sub_menu_key'] = 'SM_RESTAURANT_TABLES';
        $data['action_key'] = 'A_DETAIL_RESTAURANT_TABLE';
        check_access([$data['action_key']]);

        $table = TableModel::where('slack', '=', $slack)->first();
        
        if (empty($table)) {
            abort(404);
        }

        $table_data = new TableResource($table);
        
        $data['table_data'] = $table_data;

        return view('table.table_detail', $data);
    }
}

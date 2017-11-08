<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use App\Models\Role as RoleModel;
use App\Models\User as UserModel;
use App\Models\Store as StoreModel;
use App\Models\UserStore as UserStoreModel;

use App\Http\Resources\UserResource;
use App\Http\Resources\StoreResource;

class User extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_USER';
        $data['sub_menu_key'] = 'SM_USERS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('user.users', $data);
    }

    //This is the function that loads the add/edit page
    public function add_user(Request $request, $slack = null){
        //check access
        $data['menu_key'] = 'MM_USER';
        $data['sub_menu_key'] = 'SM_USERS';
        $data['action_key'] = ($slack == null)?'A_ADD_USER':'A_EDIT_USER';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('USER_STATUS')->active()->sortValueAsc()->get();

        $data['roles'] = RoleModel::select('slack', 'label')->resolveSuperAdminRole()->active()->sortLabelAsc()->get();

        $data['stores'] =  StoreModel::select('slack', 'store_code', 'name', 'address')
        ->active()
        ->get();

        $data['is_super_admin'] = $request->is_super_admin;
        
        $data['user_data'] = null;
        if(isset($slack)){
            $user = UserModel::where('users.slack', $slack)
            ->first();

            if (empty($user)) {
                abort(404);
            }
            $user_data = new UserResource($user);
            
            $selected_stores = UserStoreModel::select('stores.slack as store_slack')
            ->where([
                ['user_stores.user_id', '=', $user->id ]
            ])
            ->storeData()
            ->pluck('store_slack')->toArray();

            $data['user_data'] = collect($user_data)->union(collect(['selected_stores' => $selected_stores]));
        }
        return view('user.add_user', $data);
    }

    //This is the function that loads the profile page
    public function profile(Request $request, $slack){
        //check access
        $data['menu_key'] = 'MM_USER';
        $data['sub_menu_key'] = 'SM_USERS';
        
        $user_data = UserModel::select('users.*', 'master_status.label as status_label', 'roles.label as role_label')
        ->where('users.slack', $slack)
        ->statusJoin()
        ->roleJoin()
        ->first();
        
        if (empty($user_data)) {
            abort(404);
        }

        $data['user'] = $user_data;

        $profile_image = config('constants.upload.profile.default');
        if($user_data->profile_image != ''){
            $profile_image = config('constants.upload.profile.view_path').'medium_'.$user_data->profile_image;
        }
        $data['user']['profile_image_path'] = $profile_image;
        
        return view('user.profile.profile', $data);
    }

    //This is the function that loads the edit profile page
    public function edit_profile(Request $request){
        //check access
        $data['menu_key'] = 'MM_USER';
        $data['sub_menu_key'] = 'SM_USERS';
        
        $slack = session('slack');
        $user_data = UserModel::where('slack', $slack)->first();
        if (empty($user_data)) {
            abort(404);
        }
        $data['user'] = $user_data;
        
        $profile_image = config('constants.upload.profile.default');
        if($user_data->profile_image != ''){
            $profile_image = config('constants.upload.profile.view_path').'medium_'.$user_data->profile_image;
        }
        $data['user']['profile_image_path'] = $profile_image;

        return view('user.profile.edit_profile', $data);
    }

    //This is the function that loads the detail page
    public function detail(Request $request, $slack){
        $data['menu_key'] = 'MM_USER';
        $data['sub_menu_key'] = 'SM_USERS';
        $data['action_key'] = 'A_DETAIL_USER';
        check_access([$data['action_key']]);

        $user = UserModel::where('slack', $slack)
        ->first();
        
        if (empty($user)) {
            abort(404);
        }

        $user_data = new UserResource($user);

        $selected_stores = UserStoreModel::where([
            ['user_stores.user_id', '=', $user->id ]
        ])
        ->get()->pluck('store_id')->toArray();
        $store = StoreModel::whereIn('id', $selected_stores)->active()->get();
        $store_data = StoreResource::collection($store);

        $data['user_data'] = collect($user_data)->union(collect(['stores' => $store_data]));

        $data['show_init_password'] = ($request->logged_user_role_id == 1 && $user_data->init_password !='')?true:false;

        return view('user.user_detail', $data);
    }
}

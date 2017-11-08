<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Config;

use App\Models\Role as RoleModel;
use App\Models\User as UserModel;
use App\Models\UserMenu as UserMenuModel;
use App\Models\RoleMenu as RoleMenuModel;

use App\Http\Resources\RoleResource;

use App\Http\Resources\Collections\RoleCollection;

class Role extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_ROLE_LISTING';
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
            
            $query = RoleModel::select('roles.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
            ->take($limit)
            ->skip($offset)
            ->statusJoin()
            ->resolveSuperAdminRole()
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

            $roles = RoleResource::collection($query);
           
            $total_count = RoleModel::select("id")->resolveSuperAdminRole()->get()->count();

            $item_array = [];
            foreach($roles as $key => $role){

                $role = $role->toArray($request);

                $item_array[$key][] = $role['role_code'];
                $item_array[$key][] = $role['label'];
                $item_array[$key][] = (isset($role['status']['label']))?view('common.status', ['status_data' => ['label' => $role['status']['label'], "color" => $role['status']['color']]])->render():'-';
                $item_array[$key][] = $role['created_at_label'];
                $item_array[$key][] = $role['updated_at_label'];
                $item_array[$key][] = (isset($role['created_by']) && isset($role['created_by']['fullname']))?$role['created_by']['fullname']:'-';
                $item_array[$key][] = view('role.layouts.role_actions', array('role' => $role))->render();
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

            if(!check_access(['A_ADD_ROLE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $role_data_exists = RoleModel::select('id')
            ->where('label', '=', trim($request->role_label))
            ->first();
            if (!empty($role_data_exists)) {
                throw new Exception("Role already exists", 400);
            }

            DB::beginTransaction();
            
            $role = [
                "slack" => $this->generate_slack("roles"),
                "role_code" => Str::random(6),
                "label" => Str::title($request->role_label),
                "status" => $request->status,
                'created_by' => $request->logged_user_id
            ];
            
            $role_id = RoleModel::create($role)->id;

            $code_start_config = Config::get('constants.unique_code_start.role');
            $code_start = (isset($code_start_config))?$code_start_config:100;
            
            $role_code = [
                "role_code" => ($code_start+$role_id)
            ];
            RoleModel::where('id', $role_id)
            ->update($role_code);

            $role_menus = explode(",", $request->role_menus);

            if (count($role_menus) >0) {
                $role_menus = array_unique($role_menus);
                foreach ($role_menus as $role_menu) {
                    $menu = [
                        'role_id' => $role_id,
                        'menu_id' => $role_menu,
                        'created_by' => $request->logged_user_id
                    ];
                    RoleMenuModel::create($menu);
                }
            }

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Role created successfully", 
                    "data"    => $role['slack']
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

            if(!check_access(['A_DETAIL_ROLE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = RoleModel::select('*')
            ->where('slack', $slack)
            ->first();

            $item_data = new RoleResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "Role loaded successfully", 
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

            if(!check_access(['A_VIEW_ROLE_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $list = new RoleCollection(RoleModel::select('*')
            ->resolveSuperAdminRole()
            ->orderBy('created_at', 'desc')->paginate());

            return response()->json($this->generate_response(
                array(
                    "message" => "Roles loaded successfully", 
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
    public function update(Request $request, $slack)
    {
        try {

            if(!check_access(['A_EDIT_ROLE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $role_data_exists = RoleModel::select('id')
            ->where([
                ['slack', '!=', $slack],
                ['label', '=', trim($request->role_label)],
            ])
            ->first();
            if ($role_data_exists) {
                throw new Exception("Role already exists", 400);
            }

            DB::beginTransaction();

            $role = [
                "label" => Str::title($request->role_label),
                "status" => $request->status,
                'updated_by' => $request->logged_user_id
            ];

            $action_response = RoleModel::where('slack', $slack)
            ->update($role);

            $role_details = RoleModel::select('*')
            ->where([
                ['slack', '=', $slack]
            ])
            ->first();
            $role_current_menus = RoleMenuModel::where('role_id', '=', $role_details->id)->pluck('menu_id')->toArray();
            (count($role_current_menus) >0 )?sort($role_current_menus):$role_current_menus;

            $role_menus = explode(",", $request->role_menus);
            (count($role_menus) >0 )?sort($role_menus):$role_menus;

            if (count($role_menus) >0 && ($role_current_menus != $role_menus)) {

                RoleMenuModel::where('role_id', $role_details->id)->delete();

                $role_menus = array_unique($role_menus);

                foreach ($role_menus as $role_menu) {
                    $menu = [
                        'role_id' => $role_details->id,
                        'menu_id' => $role_menu,
                        'created_by' => $request->logged_user_id
                    ];
                    RoleMenuModel::create($menu);
                } 
            }
            $this->update_user_roles($request, $role_details->id);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Role updated successfully", 
                    "data"    => $role_details->slack
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function update_user_roles(Request $request, $role_id){
        
        if($role_id == ''){
            return;
        }

        $role_details = RoleModel::select('*')
        ->where([
            ['id', '=', $role_id]
        ])
        ->first();

        $users = UserModel::select('users.id')
        ->where('users.role_id', '=', $role_id)
        ->get();

        $role_menus = RoleMenuModel::where('role_id', '=', $role_id)
        ->pluck('menu_id')
        ->toArray();
        (count($role_menus) >0 )?sort($role_menus):$role_menus;

        foreach($users as $user){
            
            $user_menus = UserMenuModel::where('user_id', $user->id)
            ->pluck('menu_id')
            ->toArray();
            (count($user_menus) >0 )?sort($user_menus):$user_menus;

            if($role_menus != $user_menus && $role_details->status == 1){

                $user_menu_array = [];
                foreach($role_menus as $role_menu){
                    $user_menu_array[] = [
                        'user_id' => $user->id,
                        'menu_id' => $role_menu,
                        'created_by' => $request->logged_user_id,
                        "created_at"=> now(),
                        "updated_at"=> now()
                    ];
                }

                UserMenuModel::where('user_id', $user->id)->delete();
                UserMenuModel::insert($user_menu_array);
            }
            
            if($role_details->status == 0){
                UserMenuModel::where('user_id', $user->id)->delete();
            }
        }
    }

    public function validate_request($request)
    {
        $validator = Validator::make($request->all(), [
            'role_label' => $this->get_validation_rules("name_label", true),
            'status' => $this->get_validation_rules("status", true),
            'role_menus'  => $this->get_validation_rules("role_menus", true)
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}

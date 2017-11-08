<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Role as RoleModel;
use App\Models\RoleMenu as RoleMenuModel;
use App\Models\MasterStatus;

use App\Http\Resources\RoleResource;

class Role extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_USER';
        $data['sub_menu_key'] = 'SM_ROLES';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('role.roles', $data);
    }

    //This is the function that loads the add/edit page
    public function add_role($slack = null){
        //check access
        $data['menu_key'] = 'MM_USER';
        $data['sub_menu_key'] = 'SM_ROLES';
        $data['action_key'] = ($slack == null)?'A_ADD_ROLE':'A_EDIT_ROLE';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('ROLE_STATUS')->active()->sortValueAsc()->get();

        $data['role_data'] = null;
        if(isset($slack)){
            
            $role = RoleModel::where('slack', '=', $slack)->first();
            if (empty($role)) {
                abort(404);
            }

            $role_data = new RoleResource($role);

            $menus = RoleMenuModel::where('role_id', '=', $role->id)->pluck('menu_id')->toArray();

            $data['role_data'] = collect($role_data)->union(collect(['menus' => $menus]));
        }

        $menu_data = $this->get_menus();
        $data['access_menus'] = $menu_data;

        return view('role.add_role', $data);
    }

    private function get_menus(){

        $menu_array = [];
        $menu_tree = [];
        
        $main_menus = DB::table('menus')
        ->select('id', 'label', 'type')
        ->orderBy('sort_order', 'ASC')
        ->where('status', '=', 1)
        ->where('type', '=', 'MAIN_MENU')
        ->get();

        foreach($main_menus as $main_menu){
            
            $menu_array['_'.$main_menu->id] = [
                "menu_key" => $main_menu->id,
                "label" => $main_menu->label,
                "type" => $main_menu->type,
            ];

            $sub_menus = DB::table('menus')
            ->select('id', 'label' , 'type')
            ->orderBy('sort_order', 'ASC')
            ->where('status', '=', 1)
            //->where('type', '=', 'SUB_MENU')
            ->where('parent', '=', $main_menu->id)
            ->get();

            $all_sub_menu_ids = array_pluck($sub_menus, 'id');
            $menu_array['_'.$main_menu->id]['childs'] = $all_sub_menu_ids;

            foreach($sub_menus as $sub_menu){
                
                $menu_array['_'.$main_menu->id]['sub_menu'][$sub_menu->id] = [
                    "menu_key" => $sub_menu->id,
                    "label" => $sub_menu->label,
                    "type" => $sub_menu->type,
                    "sub_items" => [$sub_menu->id]
                ];
                $menu_array['_'.$main_menu->id]['sub_menu'][$sub_menu->id]['siblings'] = $all_sub_menu_ids;

                if($sub_menu->type == 'SUB_MENU'){
                    
                    $action_menus = DB::table('menus')
                    ->select('id', 'label', 'type')
                    ->orderBy('sort_order', 'ASC')
                    ->where('status', '=', 1)
                    ->where('type', '=', 'ACTIONS')
                    ->where('parent', '=', $sub_menu->id)
                    ->get();

                    $all_action_menu_ids = array_pluck($action_menus, 'id');
                    $menu_array['_'.$main_menu->id]['childs'] = array_merge($menu_array['_'.$main_menu->id]['childs'], $all_action_menu_ids);
                    $menu_array['_'.$main_menu->id]['sub_menu'][$sub_menu->id]['childs'] = $all_action_menu_ids;

                    foreach ($action_menus as $action_menu) {
                        $menu_array['_'.$main_menu->id]['sub_menu'][$sub_menu->id]['actions'][] = [
                            "menu_key" => $action_menu->id,
                            "label" => $action_menu->label,
                            "type" => $action_menu->type,
                            "siblings" => $all_action_menu_ids
                        ];
                    }

                }

            }
        }
        return $menu_array;
    }

    //This is the function that loads the detail page
    public function detail(Request $request, $slack){
        $data['menu_key'] = 'MM_USER';
        $data['sub_menu_key'] = 'SM_ROLES';
        $data['action_key'] = 'A_DETAIL_ROLE';
        check_access([$data['action_key']]);

        $role = RoleModel::where('slack', '=', $slack)->first();

        if (empty($role)) {
            abort(404);
        }

        $role_data = new RoleResource($role);

        $menus = RoleMenuModel::where('role_id', '=', $role->id)->pluck('menu_id')->toArray();

        $data['role_data'] = collect($role_data)->union(collect(['menus' => $menus]));

        $menu_data = $this->get_menus();
        $data['access_menus'] = $menu_data;

        return view('role.role_detail', $data);
    }
}

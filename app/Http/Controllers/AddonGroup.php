<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\AddonGroup as AddonGroupModel;
use App\Models\AddonGroupProduct as AddonGroupProductModel;

use App\Http\Resources\AddonGroupResource;

class AddonGroup extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_STOCK';
        $data['sub_menu_key'] = 'SM_ADDON_GROUPS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('addon_group.addon_groups', $data);
    }

    //This is the function that loads the add/edit page
    public function add_addon_group($slack = null){
        //check access
        $data['menu_key'] = 'MM_STOCK';
        $data['sub_menu_key'] = 'SM_ADDON_GROUPS';
        $data['action_key'] = ($slack == null)?'A_ADD_ADDON_GROUP':'A_EDIT_ADDON_GROUP';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('ADDON_GROUP_STATUS')->active()->sortValueAsc()->get();

        $data['addon_group_data'] = null;
        if(isset($slack)){
            $addon_group = AddonGroupModel::where('slack', '=', $slack)->first();
            if (empty($addon_group)) {
                abort(404);
            }
            
            $addon_group_data = new AddonGroupResource($addon_group);
            $data['addon_group_data'] = $addon_group_data;
        }

        return view('addon_group.add_addon_group', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_STOCK';
        $data['sub_menu_key'] = 'SM_ADDON_GROUPS';
        $data['action_key'] = 'A_DETAIL_ADDON_GROUP';
        check_access([$data['action_key']]);

        $addon_group = AddonGroupModel::where('slack', '=', $slack)->first();
        
        if (empty($addon_group)) {
            abort(404);
        }

        $addon_group_data = new AddonGroupResource($addon_group);
        
        $data['addon_group_data'] = $addon_group_data;

        return view('addon_group.addon_group_detail', $data);
    }
}

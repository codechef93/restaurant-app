<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\VariantOption as VariantOptionModel;

use App\Http\Resources\VariantOptionResource;

class VariantOption extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_VARIANT_OPTIONS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('variant_option.variant_options', $data);
    }

    //This is the function that loads the add/edit page
    public function add_variant_option($slack = null){
        //check access
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_VARIANT_OPTIONS';
        $data['action_key'] = ($slack == null)?'A_ADD_VARIANT_OPTION':'A_EDIT_VARIANT_OPTION';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('VARIANT_OPTION_STATUS')->active()->sortValueAsc()->get();

        $data['variant_option_data'] = null;
        if(isset($slack)){
            $variant_option = VariantOptionModel::where('slack', '=', $slack)->first();
            if (empty($variant_option)) {
                abort(404);
            }
            
            $variant_option_data = new VariantOptionResource($variant_option);
            $data['variant_option_data'] = $variant_option_data;
        }

        return view('variant_option.add_variant_option', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_VARIANT_OPTIONS';
        $data['action_key'] = 'A_DETAIL_VARIANT_OPTION';
        check_access([$data['action_key']]);

        $variant_option = VariantOptionModel::where('slack', '=', $slack)->first();
        
        if (empty($variant_option)) {
            abort(404);
        }

        $variant_option_data = new VariantOptionResource($variant_option);
        
        $data['variant_option_data'] = $variant_option_data;

        return view('variant_option.variant_option_detail', $data);
    }
}

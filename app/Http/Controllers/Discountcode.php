<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use App\Models\Discountcode as DiscountcodeModel;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\DiscountcodeResource;

class Discountcode extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_TAX_AND_DISCOUNT';
        $data['sub_menu_key'] = 'SM_DISCOUNTCODES';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('discount_code.discount_codes', $data);
    }

    //This is the function that loads the add/edit page
    public function add_discount_code($slack = null){
        //check access
        $data['menu_key'] = 'MM_TAX_AND_DISCOUNT';
        $data['sub_menu_key'] = 'SM_DISCOUNTCODES';
        $data['action_key'] = ($slack == null)?'A_ADD_DISCOUNTCODE':'A_EDIT_DISCOUNTCODE';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('DISCOUNTCODE_STATUS')->active()->sortValueAsc()->get();

        $data['discount_code_data'] = null;
        if(isset($slack)){
            
            $discount_code = DiscountcodeModel::where('slack', '=', $slack)->first();
            if (empty($discount_code)) {
                abort(404);
            }
            
            $discount_code_data = new DiscountcodeResource($discount_code);
            $data['discount_code_data'] = $discount_code_data;
        }

        return view('discount_code.add_discount_code', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_TAX_AND_DISCOUNT';
        $data['sub_menu_key'] = 'SM_DISCOUNTCODES';
        $data['action_key'] = 'A_DETAIL_DISCOUNTCODE';
        check_access([$data['action_key']]);

        $discount_code = DiscountcodeModel::where('slack', '=', $slack)->first();
        
        if (empty($discount_code)) {
            abort(404);
        }

        $discount_code_data = new DiscountcodeResource($discount_code);
        
        $data['discount_code_data'] = $discount_code_data;

        return view('discount_code.discount_code_detail', $data);
    }
}

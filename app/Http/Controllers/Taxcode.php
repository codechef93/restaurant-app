<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use App\Models\Taxcode as TaxcodeModel;
use App\Models\TaxcodeType as TaxcodeTypeModel;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\TaxcodeResource;

class Taxcode extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_TAX_AND_DISCOUNT';
        $data['sub_menu_key'] = 'SM_TAXCODES';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('tax_code.tax_codes', $data);
    }

    //This is the function that loads the add/edit page
    public function add_tax_code($slack = null){
        //check access
        $data['menu_key'] = 'MM_TAX_AND_DISCOUNT';
        $data['sub_menu_key'] = 'SM_TAXCODES';
        $data['action_key'] = ($slack == null)?'A_ADD_TAXCODE':'A_EDIT_TAXCODE';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('TAX_CODE_STATUS')->active()->sortValueAsc()->get();

        $data['tax_code_data'] = null;
        if(isset($slack)){
            
            $tax_code = TaxcodeModel::where('slack', '=', $slack)->first();
            if (empty($tax_code)) {
                abort(404);
            }

            $tax_code_data = new TaxcodeResource($tax_code);
            $data['tax_code_data'] = $tax_code_data;
        }

        return view('tax_code.add_tax_code', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_TAX_AND_DISCOUNT';
        $data['sub_menu_key'] = 'SM_TAXCODES';
        $data['action_key'] = 'A_DETAIL_TAXCODE';
        check_access([$data['action_key']]);

        $tax_code = TaxcodeModel::where('slack', '=', $slack)->first();
        
        if (empty($tax_code)) {
            abort(404);
        }

        $tax_code_data = new TaxcodeResource($tax_code);
        
        $data['tax_code_data'] = $tax_code_data;

        return view('tax_code.tax_code_detail', $data);
    }
}

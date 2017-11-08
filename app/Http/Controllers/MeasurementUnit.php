<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\MeasurementUnit as MeasurementUnitModel;

use App\Http\Resources\MeasurementUnitResource;

class MeasurementUnit extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_MEASUREMENT_UNIT';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('measurement_unit.measurement_units', $data);
    }

    //This is the function that loads the add/edit page
    public function add_measurement_unit($slack = null){
        //check access
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_MEASUREMENT_UNIT';
        $data['action_key'] = ($slack == null)?'A_ADD_MEASUREMENT_UNIT':'A_EDIT_MEASUREMENT_UNIT';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('MEASUREMENT_UNIT_STATUS')->active()->sortValueAsc()->get();

        $data['measurement_unit_data'] = null;
        if(isset($slack)){
            $measurement_unit = MeasurementUnitModel::where('slack', '=', $slack)->first();
            if (empty($measurement_unit)) {
                abort(404);
            }
            
            $measurement_unit_data = new MeasurementUnitResource($measurement_unit);
            $data['measurement_unit_data'] = $measurement_unit_data;
        }

        return view('measurement_unit.add_measurement_unit', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_MEASUREMENT_UNIT';
        $data['action_key'] = 'A_DETAIL_MEASUREMENT_UNIT';
        check_access([$data['action_key']]);

        $measurement_unit = MeasurementUnitModel::where('slack', '=', $slack)->first();
        
        if (empty($measurement_unit)) {
            abort(404);
        }

        $measurement_unit_data = new MeasurementUnitResource($measurement_unit);
        
        $data['measurement_unit_data'] = $measurement_unit_data;

        return view('measurement_unit.measurement_unit_detail', $data);
    }
}

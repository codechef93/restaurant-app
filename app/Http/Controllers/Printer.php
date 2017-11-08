<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Printer as PrinterModel;

use App\Http\Resources\PrinterResource;

class Printer extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_PRINTERS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('printer.printers', $data);
    }

    //This is the function that loads the add/edit page
    public function add_printer($slack = null){
        //check access
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_PRINTERS';
        $data['action_key'] = ($slack == null)?'A_ADD_PRINTER':'A_EDIT_PRINTER';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('PRINTER_STATUS')->active()->sortValueAsc()->get();

        $data['printer_data'] = null;
        if(isset($slack)){
            $printer = PrinterModel::where('slack', '=', $slack)->first();
            if (empty($printer)) {
                abort(404);
            }
            
            $printer_data = new PrinterResource($printer);
            $data['printer_data'] = $printer_data;
        }

        return view('printer.add_printer', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_PRINTERS';
        $data['action_key'] = 'A_DETAIL_PRINTER';
        check_access([$data['action_key']]);

        $printer = PrinterModel::where('slack', '=', $slack)->first();
        
        if (empty($printer)) {
            abort(404);
        }

        $printer_data = new PrinterResource($printer);
        
        $data['printer_data'] = $printer_data;

        return view('printer.printer_detail', $data);
    }
}

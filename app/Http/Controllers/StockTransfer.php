<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\StockTransfer as StockTransferModel;
use App\Models\Store as StoreModel;
use App\Models\MasterStatus as MasterStatusModel;

use App\Http\Resources\StoreResource;
use App\Http\Resources\StockTransferResource;

class StockTransfer extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_STOCK';
        $data['sub_menu_key'] = 'SM_STOCK_TRANSFER';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('stock_transfer.stock_transfers', $data);
    }

    //This is the function that loads the add/edit page
    public function add_stock_transfer($slack = null){
        
        //check access
        $data['menu_key'] = 'MM_STOCK';
        $data['sub_menu_key'] = 'SM_STOCK_TRANSFER';
        $data['action_key'] = ($slack == null)?'A_ADD_STOCK_TRANSFER':'A_EDIT_STOCK_TRANSFER';
        check_access(array($data['action_key']));

        $current_selected_store = request()->logged_user_store_id;

        $data['edit_stock_transfer_access'] = true;
        $data['stock_transfer_data'] = null;
        if(isset($slack)){
            $stock_transfer = StockTransferModel::where('slack', '=', $slack)->resolveStore($current_selected_store)->first();
            if (empty($stock_transfer)) {
                abort(404);
            }
            
            $stock_transfer_data = new StockTransferResource($stock_transfer);
            $data['stock_transfer_data'] = $stock_transfer_data;

            $stock_transfer_status = MasterStatusModel::select('value_constant')->filterByValue('STOCK_TRANSFER_STATUS', $stock_transfer->status)->active()->first();

            $data['edit_stock_transfer_access'] = ($stock_transfer->from_store_id == $current_selected_store && $stock_transfer_status->value_constant == 'PENDING')?true:false;
        }

        $current_store = StoreModel::select('*')
        ->where('id', request()->logged_user_store_id)
        ->active()
        ->first();

        $current_store_data = new StoreResource($current_store);
        $data['current_store'] = $current_store_data;

        $to_stores = StoreModel::select('slack', 'store_code', 'name')
        ->where([
            ['id', '!=', request()->logged_user_store_id]
        ])
        ->active()
        ->get();
        $data['to_stores'] = $to_stores;

        return view('stock_transfer.add_stock_transfer', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_STOCK';
        $data['sub_menu_key'] = 'SM_STOCK_TRANSFER';
        $data['action_key'] = 'A_DETAIL_STOCK_TRANSFER';
        check_access([$data['action_key']]);

        $current_selected_store = request()->logged_user_store_id;

        $stock_transfer = StockTransferModel::where('slack', '=', $slack)->resolveStore($current_selected_store)->first();
        
        if (empty($stock_transfer)) {
            abort(404);
        }

        $stock_data = new StockTransferResource($stock_transfer);
        
        $data['stock_transfer_data'] = $stock_data;

        $stock_transfer_status = MasterStatusModel::select('value_constant')->filterByValue('STOCK_TRANSFER_STATUS', $stock_data->status)->active()->first();

        $data['delete_stock_transfer_access'] = ($stock_data->from_store_id == $current_selected_store && $stock_transfer_status->value_constant == 'PENDING' && check_access(['A_DELETE_STOCK_TRANSFER'] ,true));

        $data['stock_transfer_statuses'] = [];
        if(check_access(['A_EDIT_STATUS_STOCK_TRANSFER'] ,true)){
            $data['stock_transfer_statuses'] = MasterStatusModel::select('label','value_constant')->where([
                ['value_constant', '!=', strtoupper('PENDING')],
                ['key', '=', 'STOCK_TRANSFER_STATUS']
            ])->active()->orderBy('value', 'asc')->get();
        }

        return view('stock_transfer.stock_transfer_detail', $data);
    }

    //This is the function that loads the verify page
    public function verify_stock_transfer($slack){
        
        //check access
        $data['menu_key'] = 'MM_STOCK';
        $data['sub_menu_key'] = 'SM_STOCK_TRANSFER';
        $data['action_key'] = 'A_VERIFY_STOCK_TRANSFER';
        check_access(array($data['action_key']));

        $current_selected_store = request()->logged_user_store_id;

        $data['stock_transfer_data'] = null;
        if(isset($slack)){
            $stock_transfer = StockTransferModel::where('slack', '=', $slack)->resolveStore($current_selected_store)->first();
            if (empty($stock_transfer)) {
                abort(404);
            }

            if($current_selected_store != $stock_transfer->to_store_id){
                abort(404);
            }
            
            $stock_transfer_data = new StockTransferResource($stock_transfer);
            $data['stock_transfer_data'] = $stock_transfer_data;
        }

        return view('stock_transfer.verify_stock_transfer', $data);
    }
}

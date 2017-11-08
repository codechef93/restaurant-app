<?php

namespace Modules\Floorplan\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\RestoArea;
use App\Models\Table;
use App\Models\Store as StoreModel;

class Main extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    private function generate_slack($table)
    {
        do{
            $slack = str_random(25);
            $exist = DB::table($table)->where("slack", $slack)->first();
        }while($exist);
        return $slack;
    }

    public function edit(Request $request, RestoArea $restoarea)
    {
        $data['menu_key'] = 'MM_RESTAURANT';
        $data['sub_menu_key'] = 'SM_RESTAURANT_WAITER';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        $is_waiter = false;

        $store_data = StoreModel::select('id', 'restaurant_waiter_role_id')
        ->where([
            ['id', '=', $request->logged_user_store_id],
            ['status', '=', 1]
        ])
        ->first();

        $data['users'] = [];
        if (!empty($store_data)) {
            if($request->logged_user_role_id != $store_data->restaurant_waiter_role_id){
                
                $is_waiter = false;

                if ($store_data->restaurant_waiter_role_id != ''){
                    $waiter_role_id = RoleModel::select('id')
                    ->where('id', '=', $store_data->restaurant_waiter_role_id)
                    ->active()
                    ->first();
                    
                    $user_list = UserModel::select('*', 'user_stores.id as user_store_access')
                    ->hideSuperAdminRole()
                    ->userStoreAccessData()
                    ->active()
                    ->where('role_id', '=', $waiter_role_id->id)
                    ->where('user_stores.store_id', $store_data->id)
                    ->whereNotNull('user_stores.id')
                    ->groupBy('users.id')
                    ->get();
                    
                    $users = UserResource::collection($user_list);
                    $data['users'] = $users;
                }     
            }else{
                $is_waiter = true;
            }
        }

        $data['is_waiter'] = $is_waiter;

        $data['store_slack'] = $request->logged_user_store_slack;

        return view('floorplan::edit',['restoarea'=>$restoarea,'title'=>__('Floor manager for ').$restoarea->name], $data);        
    }

    public function saveFloorPlan(Request $request,RestoArea $restoarea){
        foreach ($request->items as $key => $item) {

            if(isset($item['table_id'])){
                $table=Table::findOrFail($item['table_id']);
            }else{
                $table=Table::create([
                    "slack" => $this->generate_slack("restaurant_tables"),
                    "store_id" => $request->logged_user_store_id,
                    "table_number" => "",
                    "no_of_occupants" => 4,
                    "status" => 1,
                    "restoarea_id" => $restoarea->id,
                    "created_by" => $request->logged_user_id
                ]);
                $table->save();
            }

            if(isset($item['table_number'])){
                $table->table_number=$item['table_number'];
            }
            if(isset($item['size'])){
                $table->no_of_occupants=$item['no_of_occupants'];
            }
            if(isset($item['w'])){
                $table->w=$item['w'];
            }
            if(isset($item['h'])){
                $table->h=$item['h'];
            }
            if(isset($item['x'])){
                $table->x=$item['x'];
            }
            if(isset($item['y'])){
                $table->y=$item['y'];
            }
            if(isset($item['rounded'])){
                $table->rounded=$item['rounded'];
            }
            $table->update();

            //Or remove
            if(isset($item['deleted'])){
                $table->delete();
            }
        }
        return response()->json([
            'data' => [],
            'status' => true,
            'errMsg' => '',
        ]);
    }
}

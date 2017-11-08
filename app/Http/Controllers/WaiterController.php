<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Store as StoreModel;
use App\RestoArea;
use App\Models\Store;

class WaiterController extends Controller
{
    private $provider = RestoArea::class;
    private $parameter_name = 'restoarea';

    private function getFields()
    {
        return [
            ['ftype'=>'input', 'name'=>'Name', 'id'=>'name', 'placeholder'=> ('Enter area name'), 'required'=>true],
        ];
    }
    
    //
    public function create(Request $request)
    {
        $data['menu_key'] = 'MM_RESTAURANT';
        $data['sub_menu_key'] = 'SM_RESTAURANT_TABLES';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        $data['store_slack'] = $request->logged_user_store_slack;

        return view('table.waiter.create', [ 'fields'=>$this->getFields(), ], $data);
    }

    public function store(Request $request)
    {
        $restaurant_id = Store::where('slack', $request->store_slack)->first()->id;
        $item = $this->provider::create([
            'name'=>$request->name,
            'restaurant_id'=>$restaurant_id,
        ]);
        $item->save();

        return redirect()->route('tables')->withStatus(('Area has been added'));
    }

    public function edit(Request $request, $id)
    {
        $data['menu_key'] = 'MM_RESTAURANT';
        $data['sub_menu_key'] = 'SM_RESTAURANT_TABLES';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        // $is_waiter = false;

        // $store_data = StoreModel::select('id', 'restaurant_waiter_role_id')
        // ->where([
        //     ['id', '=', $request->logged_user_store_id],
        //     ['status', '=', 1]
        // ])
        // ->first();

        // $data['users'] = [];
        // if (!empty($store_data)) {
        //     if($request->logged_user_role_id != $store_data->restaurant_waiter_role_id){
                
        //         $is_waiter = false;

        //         if ($store_data->restaurant_waiter_role_id != ''){
        //             $waiter_role_id = RoleModel::select('id')
        //             ->where('id', '=', $store_data->restaurant_waiter_role_id)
        //             ->active()
        //             ->first();
                    
        //             $user_list = UserModel::select('*', 'user_stores.id as user_store_access')
        //             ->hideSuperAdminRole()
        //             ->userStoreAccessData()
        //             ->active()
        //             ->where('role_id', '=', $waiter_role_id->id)
        //             ->where('user_stores.store_id', $store_data->id)
        //             ->whereNotNull('user_stores.id')
        //             ->groupBy('users.id')
        //             ->get();
                    
        //             $users = UserResource::collection($user_list);
        //             $data['users'] = $users;
        //         }     
        //     }else{
        //         $is_waiter = true;
        //     }
        // }

        // $data['is_waiter'] = $is_waiter;

        $data['store_slack'] = $request->logged_user_store_slack;

        $data['restoarea'] = $this->provider::findOrFail($id);

        $fields = $this->getFields();
        $fields[0]['value'] = $data['restoarea']->name;

        $parameter = [];
        $parameter[$this->parameter_name] = $id;

        return view('table.waiter.edit', [ 'fields'=> $fields, ], $data);
    }

    public function update(Request $request, $id)
    {
        $item = $this->provider::findOrFail($id);
        $item->name = $request->name;
        $item->update();

        return redirect()->route('tables')->withStatus(('Area has been updated'));
    }

    public function delete($id)
    {
        $item = $this->provider::findOrFail($id);
        if (empty($item)) {
            return redirect()->route('tables')->withStatus(('Area has items associated. Please remove items associated before deleting this area'));
        } else {
            $item->delete();

            return redirect()->route('tables')->withStatus(('Area has been removed'));
        }
    }
}

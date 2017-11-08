<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;

use App\Models\MasterStatus;
use App\Models\Store as StoreModel;
use App\Models\Account as AccountModel;

class account_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $base_controller = new Controller;

        MasterStatus::firstOrCreate(
            [
                'key' => 'ACCOUNT_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'ACCOUNT_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        //add basic business account to each store as default
        $active_stores = StoreModel::select('id')
        ->active()
        ->get();

        if(count($active_stores)>0){
            foreach ($active_stores as $active_store) {
                $account_exists = AccountModel::select('id')
                ->where('store_id', '=', trim($active_store->id))
                ->first();
                if (empty($account_data_exists)) {
                    
                    $account = [
                        "slack" => $base_controller->generate_slack("accounts"),
                        "store_id" => $active_store->id,
                        "account_code" => Str::random(6),
                        "account_type" => 1,
                        "label" => 'Default Sales Account',
                        "description" => 'Default Sales Account',
                        "initial_balance" => 0,
                        "pos_default" => 1,
                        "status" => 1,
                        "created_by" => 1
                    ];
                    
                    $account_id = AccountModel::create($account)->id;
                    
                    $code_start_config = Config::get('constants.unique_code_start.account');
                    $code_start = (isset($code_start_config))?$code_start_config:100;
                    
                    $account_code = [
                        "account_code" => ($code_start+$account_id)
                    ];

                    AccountModel::withoutGlobalScopes()->where('id', $account_id)
                    ->update($account_code);
                }
            }
        }
    }
}

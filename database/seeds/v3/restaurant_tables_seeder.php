<?php

use Illuminate\Database\Seeder;

use App\Models\MasterStatus;
use App\Models\MasterOrderType as MasterOrderTypeModel;

class restaurant_tables_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Status seeder
        MasterStatus::firstOrCreate(
            [
                'key' => 'RESTAURANT_TABLE_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'RESTAURANT_TABLE_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();
    }
}

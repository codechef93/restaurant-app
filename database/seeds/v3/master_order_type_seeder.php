<?php

use Illuminate\Database\Seeder;

use App\Models\MasterStatus;
use App\Models\MasterOrderType as MasterOrderTypeModel;

class master_order_type_seeder extends Seeder
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
                'key' => 'MASTER_ORDER_TYPE_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'MASTER_ORDER_TYPE_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        MasterOrderTypeModel::firstOrCreate(
            [
                "order_type_constant" => 'DINEIN',
                "label" => 'Dine In',
                "description" => '',
                "restaurant" => 1,
                "icon" => 'fas fa-utensil-spoon',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();
        
        MasterOrderTypeModel::firstOrCreate(
            [
                "order_type_constant" => 'TAKEWAY',
                "label" => 'Take Away',
                "description" => '',
                "restaurant" => 1,
                "icon" => 'fas fa-box-open',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();

        MasterOrderTypeModel::firstOrCreate(
            [
                "order_type_constant" => 'DELIVERY',
                "label" => 'Delivery',
                "description" => '',
                "restaurant" => 1,
                "icon" => 'fas fa-biking',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();
    }
}

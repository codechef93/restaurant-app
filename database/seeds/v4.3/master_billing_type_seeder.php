<?php

use Illuminate\Database\Seeder;
use App\Models\MasterBillingType as MasterBillingTypeModel;
use App\Models\MasterStatus;

class master_billing_type_seeder extends Seeder
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
                'key' => 'MASTER_BILLING_TYPE_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'MASTER_BILLING_TYPE_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        MasterBillingTypeModel::firstOrCreate(
            [
                "billing_type_constant" => 'FINE_DINE',
                "label" => 'Fine Dine',
                "description" => '',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();

        MasterBillingTypeModel::firstOrCreate(
            [
                "billing_type_constant" => 'QUICK_BILL',
                "label" => 'Quick Bill',
                "description" => '',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();
    }
}

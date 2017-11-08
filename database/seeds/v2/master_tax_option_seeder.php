<?php

use Illuminate\Database\Seeder;
use App\Models\MasterStatus;
use App\Models\MasterTaxOption as MasterTaxOptionModel;

class master_tax_option_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MasterStatus::firstOrCreate(
            [
                'key' => 'MASTER_TAX_OPTION_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'MASTER_TAX_OPTION_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();
        
        MasterTaxOptionModel::firstOrCreate(
            [
                "tax_option_constant" => 'DEFAULT_TAX',
                "label" => 'DEFAULT TAX',
                "component_count" => '1',
                "component_1" => 'TAX',
                "component_2" => '',
                "component_3" => '',
                "description" => '',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();

        MasterTaxOptionModel::firstOrCreate(
            [
                "tax_option_constant" => 'CGST_SGST',
                "label" => 'CGST + SGST',
                "component_count" => '2',
                "component_1" => 'CGST',
                "component_2" => 'SGST',
                "component_3" => '',
                "description" => '',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();

        MasterTaxOptionModel::firstOrCreate(
            [
                "tax_option_constant" => 'IGST',
                "label" => 'IGST',
                "component_count" => '1',
                "component_1" => 'IGST',
                "component_2" => '',
                "component_3" => '',
                "description" => '',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();
    }
}

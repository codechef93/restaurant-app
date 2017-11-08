<?php

use Illuminate\Database\Seeder;

use App\Models\MasterStatus;
use App\Models\MasterAccountType as MasterAccountTypeModel;

class master_account_type_seeder extends Seeder
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
                'key' => 'MASTER_ACCOUNT_TYPE_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'MASTER_ACCOUNT_TYPE_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        MasterAccountTypeModel::firstOrCreate(
            [
                "account_type_constant" => 'BASIC',
                "label" => 'Basic (Default)',
                "description" => '',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();
        
        MasterAccountTypeModel::firstOrCreate(
            [
                "account_type_constant" => 'ASSET',
                "label" => 'Asset',
                "description" => '',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();

        MasterAccountTypeModel::firstOrCreate(
            [
                "account_type_constant" => 'LIABILITY',
                "label" => 'Liability',
                "description" => '',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();

        MasterAccountTypeModel::firstOrCreate(
            [
                "account_type_constant" => 'EQUITY',
                "label" => 'Equity',
                "description" => '',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();

        MasterAccountTypeModel::firstOrCreate(
            [
                "account_type_constant" => 'REVENUE',
                "label" => 'Revenue',
                "description" => '',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();

        MasterAccountTypeModel::firstOrCreate(
            [
                "account_type_constant" => 'EXPENSE',
                "label" => 'Expense',
                "description" => '',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();
    }
}

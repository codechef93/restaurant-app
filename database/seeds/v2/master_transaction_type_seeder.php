<?php

use Illuminate\Database\Seeder;

use App\Models\MasterStatus;
use App\Models\MasterTransactionType AS MasterTransactionTypeModel;

class master_transaction_type_seeder extends Seeder
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
                'key' => 'MASTER_TRANSACTION_TYPE_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'MASTER_TRANSACTION_TYPE_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        MasterTransactionTypeModel::firstOrCreate(
            [
                "transaction_type_constant" => 'INCOME',
                "label" => 'Income/Credit',
                "description" => '',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();

        MasterTransactionTypeModel::firstOrCreate(
            [
                "transaction_type_constant" => 'EXPENSE',
                "label" => 'Expense/Debit',
                "description" => '',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();
    }
}

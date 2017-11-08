<?php

use App\Models\MasterStatus;
use Illuminate\Database\Seeder;

class master_status_table_seeder extends Seeder
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
                'key' => 'USER_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'USER_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'CUSTOMER_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'CUSTOMER_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'ROLE_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'ROLE_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'CATEGORY_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'CATEGORY_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'PRODUCT_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'PRODUCT_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'SUPPLIER_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'SUPPLIER_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'TAX_CODE_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'TAX_CODE_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();


        MasterStatus::firstOrCreate(
            [
                'key' => 'ORDER_STATUS',
                'value' => '0',
                'value_constant' => 'DELETED',
                'label' => 'Deleted',
                'color' => 'label red-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'ORDER_STATUS',
                'value' => '1',
                'value_constant' => 'CLOSED',
                'label' => 'Closed',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'ORDER_STATUS',
                'value' => '2',
                'value_constant' => 'HOLD',
                'label' => 'Hold',
                'color' => 'label red-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'ORDER_PRODUCT_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'ORDER_PRODUCT_STATUS',
                'value' => '2',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'STORE_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'STORE_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'DISCOUNTCODE_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'DISCOUNTCODE_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'PAYMENT_METHOD_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'PAYMENT_METHOD_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'PURCHASE_ORDER_STATUS',
                'value' => '1',
                'value_constant' => 'CREATED',
                'label' => 'Created',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'PURCHASE_ORDER_STATUS',
                'value' => '2',
                'value_constant' => 'APPROVED',
                'label' => 'Approved',
                'color' => 'label blue-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'PURCHASE_ORDER_STATUS',
                'value' => '3',
                'value_constant' => 'RELEASED_TO_SUPPLIER',
                'label' => 'Released To Supplier',
                'color' => 'label orange-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'PURCHASE_ORDER_STATUS',
                'value' => '4',
                'value_constant' => 'CLOSED',
                'label' => 'Closed',
                'color' => 'label grey-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'PURCHASE_ORDER_STATUS',
                'value' => '0',
                'value_constant' => 'CANCELLED',
                'label' => 'Cancelled',
                'color' => 'label red-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'PURCHASE_ORDER_PRODUCT_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'PURCHASE_ORDER_PRODUCT_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'MAIL_SETTING_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'MAIL_SETTING_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'MASTER_DATE_FORMAT_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'MASTER_DATE_FORMAT_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'MASTER_INVOICE_PRINT_TYPE_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'MASTER_INVOICE_PRINT_TYPE_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

    }
}

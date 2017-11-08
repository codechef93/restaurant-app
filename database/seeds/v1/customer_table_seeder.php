<?php

use Illuminate\Database\Seeder;

use App\Http\Controllers\Controller;
use App\Models\Customer as CustomerModel;

class customer_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $base_controller = new Controller;

        CustomerModel::updateOrCreate(
            ['email' => 'customer@alltool.com'],
            [
                'slack' => $base_controller->generate_slack("customers"),
                'customer_type' => 'DEFAULT',
                'name' => 'Customer',
                'email' => 'customer@alltool.com',
                'phone' => '0000000000',
                'status' => 1
            ]
        )->save();
    }
}

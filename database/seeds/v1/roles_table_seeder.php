<?php

use Illuminate\Database\Seeder;
use App\Models\Role as RoleModel;
use App\Http\Controllers\Controller;

class roles_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $base_controller = new Controller;

        RoleModel::updateOrCreate(
            ['id' => 1],
            [
                'slack' => $base_controller->generate_slack("roles"),
                'role_code' => 'SA',
                'label' => 'Super Admin', 
                'status' => 1,
                'created_by' => 1
            ]
        )->save();
    }
}

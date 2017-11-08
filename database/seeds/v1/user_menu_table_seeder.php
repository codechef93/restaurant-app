<?php

use Illuminate\Database\Seeder;
use App\Models\UserMenu as UserMenuModel;
use App\Models\Menu as MenuModel;

class user_menu_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menus = MenuModel::active()->get();
        foreach ($menus as $menu) {
            UserMenuModel::firstOrCreate(
                [
                    'user_id' => 1,
                    'menu_id' => $menu->id,
                    'created_by' => 1
                ]
            )->save();
        }
    }
}

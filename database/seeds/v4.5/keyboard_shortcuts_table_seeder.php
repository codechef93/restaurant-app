<?php

use Illuminate\Database\Seeder;

use App\Http\Controllers\Controller;
use App\Models\MasterStatus;
use App\Models\KeyboardShortcut as KeyboardShortcutModel;

class keyboard_shortcuts_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $base_controller = new Controller;
        
        KeyboardShortcutModel::create(
            [
                'keyboard_constant' => 'CLOSE_ORDER',
                'keyboard_shortcut' => "ctrl,shift,m", 
                'keyboard_shortcut_label' => 'Ctrl+Shift+m',
                'description' => 'Close Order',
                'status' => 1,
                'created_by'=> 1
            ]
        )->id;

        KeyboardShortcutModel::create(
            [
                'keyboard_constant' => 'HOLD_ORDER',
                'keyboard_shortcut' => "ctrl,shift,n", 
                'keyboard_shortcut_label' => 'Ctrl+Shift+n',
                'description' => 'Hold Order',
                'status' => 1,
                'created_by'=> 1
            ]
        )->id;

        KeyboardShortcutModel::create(
            [
                'keyboard_constant' => 'SEND_TO_KITCHEN',
                'keyboard_shortcut' => "ctrl,shift,b", 
                'keyboard_shortcut_label' => 'Ctrl+Shift+b',
                'description' => 'Send to Kitchen',
                'status' => 1,
                'created_by'=> 1
            ]
        )->id;

        KeyboardShortcutModel::create(
            [
                'keyboard_constant' => 'SKIP_CUSTOMER',
                'keyboard_shortcut' => "shift,z", 
                'keyboard_shortcut_label' => 'Shift+z',
                'description' => 'Skip customer selection',
                'status' => 1,
                'created_by'=> 1
            ]
        )->id;

        KeyboardShortcutModel::create(
            [
                'keyboard_constant' => 'PROCEED_CUSTOMER',
                'keyboard_shortcut' => "shift,x", 
                'keyboard_shortcut_label' => 'Shift+x',
                'description' => 'Proceed customer selection',
                'status' => 1,
                'created_by'=> 1
            ]
        )->id;

        KeyboardShortcutModel::create(
            [
                'keyboard_constant' => 'ARROW_LEFT',
                'keyboard_shortcut' => "arrowleft", 
                'keyboard_shortcut_label' => 'Arrow Left',
                'description' => 'Move left through POS products',
                'status' => 1,
                'created_by'=> 1
            ]
        )->id;

        KeyboardShortcutModel::create(
            [
                'keyboard_constant' => 'ARROW_RIGHT',
                'keyboard_shortcut' => "arrowright", 
                'keyboard_shortcut_label' => 'Arrow Right',
                'description' => 'Move right through POS products',
                'status' => 1,
                'created_by'=> 1
            ]
        )->id;

        KeyboardShortcutModel::create(
            [
                'keyboard_constant' => 'CHOOSE_PRODUCT',
                'keyboard_shortcut' => "ctrl", 
                'keyboard_shortcut_label' => 'Control',
                'description' => 'Choose POS product',
                'status' => 1,
                'created_by'=> 1
            ]
        )->id;

        KeyboardShortcutModel::create(
            [
                'keyboard_constant' => 'SCROLL_PAYMENT_METHODS',
                'keyboard_shortcut' => "shift,p", 
                'keyboard_shortcut_label' => 'Shift+p',
                'description' => 'Scroll through payment methods',
                'status' => 1,
                'created_by'=> 1
            ]
        )->id;

        KeyboardShortcutModel::create(
            [
                'keyboard_constant' => 'CHOOSE_PAYMENT_METHOD',
                'keyboard_shortcut' => "shift,l", 
                'keyboard_shortcut_label' => 'Shift+l',
                'description' => 'Choose payment method',
                'status' => 1,
                'created_by'=> 1
            ]
        )->id;

        KeyboardShortcutModel::create(
            [
                'keyboard_constant' => 'SCROLL_BUSINESS_ACCOUNTS',
                'keyboard_shortcut' => "shift,o", 
                'keyboard_shortcut_label' => 'Shift+o',
                'description' => 'Scroll through business accounts',
                'status' => 1,
                'created_by'=> 1
            ]
        )->id;

        KeyboardShortcutModel::create(
            [
                'keyboard_constant' => 'CHOOSE_BUSINESS_ACCOUNT',
                'keyboard_shortcut' => "shift,k", 
                'keyboard_shortcut_label' => 'Shift+k',
                'description' => 'Choose business accounts',
                'status' => 1,
                'created_by'=> 1
            ]
        )->id;

        KeyboardShortcutModel::create(
            [
                'keyboard_constant' => 'SCROLL_ORDER_TYPES',
                'keyboard_shortcut' => "shift,i", 
                'keyboard_shortcut_label' => 'Shift+i',
                'description' => 'Scroll through order types',
                'status' => 1,
                'created_by'=> 1
            ]
        )->id;

        KeyboardShortcutModel::create(
            [
                'keyboard_constant' => 'CHOOSE_ORDER_TYPE',
                'keyboard_shortcut' => "shift,j", 
                'keyboard_shortcut_label' => 'Shift+j',
                'description' => 'Choose order type',
                'status' => 1,
                'created_by'=> 1
            ]
        )->id;

        KeyboardShortcutModel::create(
            [
                'keyboard_constant' => 'SCROLL_RESTAURANT_TABLES',
                'keyboard_shortcut' => "shift,u", 
                'keyboard_shortcut_label' => 'Shift+u',
                'description' => 'Scroll through restaurant tables',
                'status' => 1,
                'created_by'=> 1
            ]
        )->id;

        KeyboardShortcutModel::create(
            [
                'keyboard_constant' => 'CHOOSE_RESTAURANT_TABLE',
                'keyboard_shortcut' => "shift,h", 
                'keyboard_shortcut_label' => 'Shift+h',
                'description' => 'Choose restaurant table',
                'status' => 1,
                'created_by'=> 1
            ]
        )->id;

        KeyboardShortcutModel::create(
            [
                'keyboard_constant' => 'CONTINUE',
                'keyboard_shortcut' => "shift,m", 
                'keyboard_shortcut_label' => 'Shift+m',
                'description' => 'Continue',
                'status' => 1,
                'created_by'=> 1
            ]
        )->id;

        KeyboardShortcutModel::create(
            [
                'keyboard_constant' => 'CANCEL',
                'keyboard_shortcut' => "shift,n", 
                'keyboard_shortcut_label' => 'Shift+n',
                'description' => 'Cancel',
                'status' => 1,
                'created_by'=> 1
            ]
        )->id;

        //Status seeder
        MasterStatus::firstOrCreate(
            [
                'key' => 'KEYBOARD_SHORTCUT_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'KEYBOARD_SHORTCUT_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();
    }
}

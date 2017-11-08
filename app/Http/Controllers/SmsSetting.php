<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\App;

use App\Models\SettingSms as SettingSmsModel;
use App\Models\MasterStatus;

use App\Http\Resources\SettingSmsResource;

class SmsSetting extends Controller
{
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_SMS_SETTING';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('setting.sms.sms_settings', $data);
    }

    //This is the function that loads the add/edit page
    public function add_sms_setting($slack){
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_SMS_SETTING';
        $data['action_key'] = 'A_EDIT_SMS_SETTING';
        check_access([$data['action_key']]);

        $sms_setting = SettingSmsModel::select('*')
        
        ->when($slack, function ($query, $slack) {
            $query->where('slack', $slack);
        })

        ->first();
        
        $sms_setting_data = collect();
        if(!empty($sms_setting)){
            $sms_setting_data = new SettingSmsResource($sms_setting);
        }
        $data['setting_data'] = $sms_setting_data;

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('SMS_SETTING_STATUS')->active()->sortValueAsc()->get();

        return view('setting.sms.edit_sms_setting', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_SMS_SETTING';
        $data['action_key'] = 'A_DETAIL_SMS_SETTING';
        check_access([$data['action_key']]);

        $sms_setting = SettingSmsModel::where('slack', '=', $slack)->first();
        
        if (empty($sms_setting)) {
            abort(404);
        }

        $sms_setting_data = new SettingSmsResource($sms_setting);
        
        $data['sms_setting_data'] = $sms_setting_data;

        return view('setting.sms.sms_setting_detail', $data);
    }
}

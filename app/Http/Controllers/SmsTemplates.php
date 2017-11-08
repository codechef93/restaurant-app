<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\App;

use App\Models\SmsTemplate as SmsTemplateModel;
use App\Models\MasterStatus;

use App\Http\Resources\SmsTemplateResource;

class SmsTemplates extends Controller
{
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_SMS_TEMPLATE';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('setting.sms_templates.sms_templates', $data);
    }

    //This is the function that loads the add/edit page
    public function add_sms_template($slack){
        //check access
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_SMS_TEMPLATE';
        $data['action_key'] = 'A_EDIT_SMS_TEMPLATE';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('SMS_TEMPLATE_STATUS')->active()->sortValueAsc()->get();

        $data['sms_template_data'] = null;
        if(isset($slack)){
            $sms_template = SmsTemplateModel::where('slack', '=', $slack)->first();
            if (empty($sms_template)) {
                abort(404);
            }
            
            $sms_template_data = new SmsTemplateResource($sms_template);
            $data['sms_template_data'] = $sms_template_data;
        }

        return view('setting.sms_templates.add_sms_template', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_SMS_TEMPLATE';
        $data['action_key'] = 'A_DETAIL_SMS_TEMPLATE';
        check_access([$data['action_key']]);

        $sms_template = SmsTemplateModel::where('slack', '=', $slack)->first();
        
        if (empty($sms_template)) {
            abort(404);
        }

        $sms_template_data = new SmsTemplateResource($sms_template);
        
        $data['sms_template_data'] = $sms_template_data;

        return view('setting.sms_templates.template_detail', $data);
    }
}

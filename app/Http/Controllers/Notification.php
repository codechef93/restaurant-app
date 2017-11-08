<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Role as RoleModel;
use App\Models\Notification as NotificationModel;

use App\Http\Resources\NotificationResource;

class Notification extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_NOTIFICATION';
        $data['sub_menu_key'] = 'SM_NOTIFICATIONS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('notification.notifications', $data);
    }

    //This is the function that loads the add/edit page
    public function add_notification($slack = null){
        //check access
        $data['menu_key'] = 'MM_NOTIFICATION';
        $data['sub_menu_key'] = 'SM_NOTIFICATIONS';
        $data['action_key'] = ($slack == null)?'A_ADD_NOTIFICATION':'A_EDIT_NOTIFICATION';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('NOTIFICATION_STATUS')->active()->sortValueAsc()->get();

        $data['roles'] = RoleModel::select('slack', 'label')->resolveSuperAdminRole()->active()->sortLabelAsc()->get();

        $data['notification_data'] = null;
        if(isset($slack)){
            $notification = NotificationModel::where('slack', '=', $slack)->first();
            if (empty($notification)) {
                abort(404);
            }
            
            $notification_data = new NotificationResource($notification);
            $data['notification_data'] = $notification_data;
        }

        return view('notification.add_notification', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_NOTIFICATION';
        $data['sub_menu_key'] = 'SM_NOTIFICATIONS';
        $data['action_key'] = 'A_DETAIL_NOTIFICATION';
        //check_access([$data['action_key']]);

        $notification = NotificationModel::where('slack', '=', $slack)->first();
        
        if (empty($notification)) {
            abort(404);
        }

        $notification_data = new NotificationResource($notification);
        
        $data['notification_data'] = $notification_data;

        $data['delete_notification_access'] = check_access(['A_DELETE_NOTIFICATION'] ,true);

        if($notification->user_id == request()->logged_user_id){
            $notification_data = [];
            $notification_data['read'] = 1;
            $notification_data['updated_at'] = now();
            $notification_data['updated_by'] = request()->logged_user_id;

            $action_response = NotificationModel::where('slack', $slack)
            ->update($notification_data);
        }

        return view('notification.notification_detail', $data);
    }
}

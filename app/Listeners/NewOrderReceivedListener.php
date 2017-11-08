<?php

namespace App\Listeners;

use App\Events\NewOrderReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;

use App\Models\User as UserModel;
use App\Models\Store as StoreModel;
use App\Models\Notification as NotificationModel;

class NewOrderReceivedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewOrderReceived  $event
     * @return void
     */
    public function handle(NewOrderReceived $event)
    {
        //Add chef and waiter notification
        
        $base_controller = new Controller;

        $order = $event->order;
        $store_slack = (isset($event->store_slack))?$event->store_slack:'';
        $waiter_slack = (isset($order['waiter_slack']))?$order['waiter_slack']:'';

        $notification_text = 'New order received #'.$order['order_number']. ' ['.$order['order_type'].']';

        $store_data = StoreModel::select('id', 'restaurant_chef_role_id')
        ->where([
            ['slack', '=', $store_slack],
        ])
        ->first()->makeVisible(['id']);
        if(empty($store_data)){
            return;
        }

        if(isset($store_data->restaurant_chef_role_id) && $store_data->restaurant_chef_role_id != ''){

            $chef_user_ids = UserModel::where([
                ['role_id', '=', $store_data->restaurant_chef_role_id],
                ['store_id', '=', $store_data->id],
            ])->hideSuperAdminRole()->active()->pluck('id')->toArray();

            if(count($chef_user_ids)>0){
                foreach($chef_user_ids as $chef_user_id){
                    $notification = [
                        "slack" => $base_controller->generate_slack("notifications"),
                        "user_id" => $chef_user_id,
                        "store_id" => $store_data->id,
                        "notification_text" => $notification_text,
                        "created_by" => request()->logged_user_id
                    ];
                    $notification_id = NotificationModel::create($notification)->id;
                }
            }
        }

        if(!empty($waiter_slack)){

            $waiter_user = UserModel::where([
                ['slack', '=', $waiter_slack],
            ])->hideSuperAdminRole()->active()->first()->makeVisible(['id']);

            if(!empty($waiter_user)){
                $notification = [
                    "slack" => $base_controller->generate_slack("notifications"),
                    "user_id" => $waiter_user->id,
                    "store_id" => $store_data->id,
                    "notification_text" => $notification_text,
                    "created_by" => request()->logged_user_id
                ];
                $notification_id = NotificationModel::create($notification)->id;
            }
        }
    }
}

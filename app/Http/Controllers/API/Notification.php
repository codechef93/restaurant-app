<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

use App\Models\Notification as NotificationModel;
use App\Models\User as UserModel;
use App\Models\Role as RoleModel;
use App\Models\SmsTemplate as SmsTemplateModel;
use App\Models\Order as OrderModel;
use App\Models\SettingSms as SettingSmsModel;

use App\Http\Resources\NotificationResource;
use App\Http\Resources\Collections\NotificationCollection;
use App\Http\Resources\OrderResource;

use Twilio\Rest\Client;

use Pusher\Pusher;

class Notification extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_NOTIFICATION_LISTING';
            if(check_access(array($data['action_key']), true) == false){
                $response = $this->no_access_response_for_listing_table();
                return $response;
            }

            $item_array = array();

            $draw = $request->draw;
            $limit = $request->length;
            $offset = $request->start;
            
            $order_by = $request->order[0]["column"];
            $order_direction = $request->order[0]["dir"];
            $order_by_column =  $request->columns[$order_by]['name'];

            $filter_string = $request->search['value'];
            $filter_columns = array_filter(data_get($request->columns, '*.name'));
            
            $query = NotificationModel::select('notifications.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
            ->take($limit)
            ->skip($offset)
            ->notifiedUser()
            ->statusJoin()
            ->createdUser()
            ->chooseUser($request->logged_user_id)

            ->when($order_by_column, function ($query, $order_by_column) use ($order_direction) {
                $query->orderBy($order_by_column, $order_direction);
            }, function ($query) {
                $query->orderBy('created_at', 'desc');
            })

            ->when($filter_string, function ($query, $filter_string) use ($filter_columns) {
                $query->where(function ($query) use ($filter_string, $filter_columns){
                    foreach($filter_columns as $filter_column){
                        $query->orWhere($filter_column, 'like', '%'.$filter_string.'%');
                    }
                });
            })

            ->get();

            $notifications = NotificationResource::collection($query);
           
            $total_count = NotificationModel::select("id")->chooseUser($request->logged_user_id)->get()->count();

            $item_array = [];
            foreach($notifications as $key => $notification){
                
                $notification = $notification->toArray($request);

                $receipt_indicator = '<i class="fas fa-arrow-up mr-1"></i>';
                if($notification['user']['slack'] == $request->logged_user_slack ){
                    $receipt_indicator = '<i class="fas fa-arrow-down mr-1"></i>';
                }

                $item_array[$key][] = Str::limit($notification['notification_text'], 50);
                $item_array[$key][] = $receipt_indicator.((isset($notification['user']['fullname']))?$notification['user']['fullname'] ." (".$notification['user']['user_code'].")":'');
                $item_array[$key][] = (isset($notification['status']['label']))?view('common.status', ['status_data' => ['label' => $notification['status']['label'], "color" => $notification['status']['color']]])->render():'-';
                $item_array[$key][] = $notification['created_at_label'];
                $item_array[$key][] = $notification['updated_at_label'];
                $item_array[$key][] = (isset($notification['created_by']['fullname']))?$notification['created_by']['fullname']:'-';
                $item_array[$key][] = view('notification.layouts.notification_actions', ['notification' => $notification])->render();
            }

            $response = [
                'draw' => $draw,
                'recordsTotal' => $total_count,
                'recordsFiltered' => $total_count,
                'data' => $item_array
            ];
            
            return response()->json($response);
        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            if(!check_access(['A_ADD_NOTIFICATION'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $users = json_decode($request->users, true);

            DB::beginTransaction();

            if($request->role == '' && count($users) == 0){
                throw new Exception("Please choose either role or user", 400);
            }

            if(count($users) == 0){
                $role_data = RoleModel::select('id')->where('slack', '=', $request->role)->resolveSuperAdminRole()->active()->first();
                if (!$role_data) {
                    throw new Exception("Invalid role selected", 400);
                }
                
                $users = UserModel::where('role_id', $role_data->id)->active()->get();
            }
            
            foreach($users as $user){

                $user_data = UserModel::where('slack', $user['slack'])->first();

                $notification = [
                    "slack" => $this->generate_slack("notifications"),
                    "user_id" => $user_data->id,
                    "notification_text" => $request->notification_text,
                    "created_by" => $request->logged_user_id
                ];
                
                $notification_id = NotificationModel::create($notification)->id;
            }

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Notification created successfully", 
                    "data"    => $notification['slack']
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $slack
     * @return \Illuminate\Http\Response
     */
    public function show($slack)
    { 
        try {

            if(!check_access(['A_DETAIL_NOTIFICATION'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = NotificationModel::select('*')
            ->where('slack', $slack)
            ->first();

            $item_data = new NotificationResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "Stock Transfer loaded successfully", 
                    "data"    => $item_data
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }  
    }

    /**
     * list all the specified resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        try {

            if(!check_access(['A_VIEW_NOTIFICATION_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $list = new NotificationCollection(NotificationModel::select('*')
            ->orderBy('created_at', 'desc')->active()->paginate());

            return response()->json($this->generate_response(
                array(
                    "message" => "Notification loaded successfully", 
                    "data"    => $list
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $slack
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slack)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slack)
    {
        try{

            if(!check_access(['A_DELETE_NOTIFICATION'], true)){
                throw new Exception("Invalid request", 400);
            }

            $notification_detail = NotificationModel::select('id')->where('slack', $slack)->first();
            if (empty($notification_detail)) {
                throw new Exception("Invalid Notification selected", 400);
            }
            $notification_id = $notification_detail->id;

            DB::beginTransaction();

            NotificationModel::where('id', $notification_id)->delete();

            DB::commit();

            $forward_link = route('notifications');

            return response()->json($this->generate_response(
                array(
                    "message" => "Notification deleted successfully", 
                    "data" => $slack,
                    "link" => $forward_link
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function load_notification(Request $request)
    {
        try{

            DB::beginTransaction();

            $page = $request->page;

            $notifications =  new NotificationCollection(NotificationModel::select('*')->where('user_id', $request->logged_user_id)->active()
            ->orderBy('created_at', 'desc')->paginate(10));

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Notifications loaded successfully", 
                    "data" => $notifications,
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function validate_request($request)
    {
        $validator = Validator::make($request->all(), [
            'notification_text' => $this->get_validation_rules("text", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }

    public function send_sms($template_key, $operation_id){
        try {
            $message = '';
            $recipient_number = '';

            switch($template_key){
                case 'POS_SALE_BILL_MESSAGE':
                    
                    $sms_template_data = SmsTemplateModel::select('message', 'flow_id')
                    ->where([
                        ['template_key', '=', $template_key]
                    ])
                    ->active()
                    ->first();

                    if(!empty($sms_template_data)){
                        $order = OrderModel::withoutGlobalScopes()->where('slack', $operation_id)->first();
                        $order_data = json_decode(json_encode(new OrderResource($order), true));

                        $recipient_number = $order_data->customer_phone;

                        $order_variables = [
                            '{order_number}'    => $order_data->order_number,
                            '{order_amount}'    => $order_data->total_order_amount,
                            '{currency_code}'   => $order_data->currency_code,
                            '{payment_method}'  => $order_data->payment_method,
                            '{customer_name}'   => $order_data->customer->name,
                            '{customer_email}'  => $order_data->customer_email,
                            '{customer_phone}'  => $order_data->customer_phone,
                            '{order_date}'      => $order_data->created_at_label,
                            '{public_order_link}' => route('order_public', ['slack' => $order_data->slack]),
                        ];

                        $message = strtr($sms_template_data['message'], $order_variables);
                        $variables = $order_variables;
                        $flow_id = (isset($sms_template_data->flow_id))?$sms_template_data->flow_id:'';
                    }

                break;
            }

            if($message != '' && $recipient_number != '' && $recipient_number != '0000000000'){
                $sms_setting_data = SettingSmsModel::select('*')
                ->active()
                ->first();

                if(!empty($sms_setting_data)){
                    
                    switch($sms_setting_data->gateway_type){
                        case 'TWILIO':

                            $account_sid = $sms_setting_data->account_id;
                            $auth_token = $sms_setting_data->token;
                            $twilio_number = $sms_setting_data->twilio_number;

                            $client = new Client($account_sid, $auth_token);
                            $message = $client->messages->create(
                                $recipient_number,
                                array(
                                    'from' => $twilio_number,
                                    'body' => $message
                                )
                            );
                        break;
                        case 'MSG91':
                            
                            $auth_key = $sms_setting_data->auth_key;
                            $sender_id = $sms_setting_data->sender_id;

                            $new_variables = [];
                            if(!empty($variables) && count($variables)>0){   
                                foreach($variables as $key => $variable){
                                    $new_variables[preg_replace("/{|}/", "", $key)] = $variable;
                                }
                            }

                            $extras = [
                                'variables' => $new_variables,
                                'flow_id' =>  $flow_id
                            ];

                            try{
                                $this->send_sms_msg91($auth_key, $sender_id, $recipient_number, $message, $extras);
                            }catch(Exception $e){
                                //throw new Exception("SMS not sent. ".$e->getMessage(), 400);
                            }
                            
                        break;
                        case 'TEXTLOCAL':

                            $auth_key = $sms_setting_data->auth_key;
                            $sender_id = $sms_setting_data->sender_id;

                            $Textlocal = new Textlocal(false, false, $auth_key);
 
                            $numbers = array($recipient_number);
                            $sender = ($sender_id != '')?$sender_id:'TXTLCL';
                            $message = $message;
                            
                            try{
                                $response = $Textlocal->sendSms($numbers, $message, $sender);
                            }catch(Exception $e){

                            }

                            if(isset($response) && $response->status != 'success'){
                                //throw new Exception("SMS not sent", 400);
                            }
                        break;
                    }

                    Log::info($template_key .'-'. $operation_id);
                    Log::info($message);
                }
            }else{
                if($message == ''){
                    throw new Exception("SMS not sent : Template is inactive!", 400);
                }
                if($recipient_number == ''){
                    throw new Exception("SMS not sent : Recipient number is not available!", 400);
                }
                if($recipient_number == '0000000000'){
                    throw new Exception("SMS not sent : Can't send SMS to walkin customer!", 400);
                }
                throw new Exception("SMS not sent", 400);
            }

            return response()->json($this->generate_response(
                array(
                    "message" => "SMS sent successfully",
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            Log::info($template_key .'-'. $operation_id);
            Log::info($e->getMessage());
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function send_sms_msg91($auth_key, $sender_id, $recipient_number, $message, $extras = []){
       
        //Prepare you post parameters
        $post_data = array(
            //'authkey' => $auth_key,
            'flow_id' => $extras['flow_id'],
            'mobiles' => $recipient_number,
            'message' => urlencode($message),
            'sender' => $sender_id,
        );

        if(!empty($extras['variables'])){
            $post_data = array_merge($post_data, $extras['variables']); 
        }

        //API URL
        $url="https://api.msg91.com/api/v5/flow/";

        // init the resource
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($post_data),
            //,CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => [
            "authkey: $auth_key",
            "content-type: application/JSON"
            ],
        ));

        //Ignore SSL certificate verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


        //get response
        $output = curl_exec($ch);
        
        //Print error if any
        if(curl_errno($ch))
        {
            throw new Exception('error:' . curl_error($ch));
        }

        curl_close($ch);
    }

    public function authorize_pusher_client_connection(Request $request){
        try {
            $token = $request->query("access_token");
            
            if (!isset($token) || is_null($token)) {
                header('', true, 403);
                echo "Forbidden";
                exit;
            }

            $channel_name = $request->input('channel_name');
            $socket_id = $request->input('socket_id');

            if($channel_name == '' || $socket_id == ''){
                header('', true, 403);
                echo "Forbidden";
                exit;
            }

            $token_decode = (new Controller())->jwt_decode($token, env('JWT_KEY', config('aconfig.jwt_key')), ['HS256']);
            $decoded_data = $token_decode->sub;
            $user_id = $decoded_data->user_id;
            $user_slack = $decoded_data->user_slack;
            
            $user_exists = UserModel::select("users.*")
            ->join('user_access_tokens', 'user_access_tokens.user_id', '=', 'users.id')
            ->where(['users.id' => $user_id, "users.slack" => $user_slack , "user_access_tokens.user_id" => $user_id, "user_access_tokens.access_token" => $token])
            ->active()
            ->first();

            if (!empty($user_exists)) {
                $pusher = new Pusher(config('broadcasting.connections.pusher.key'), config('broadcasting.connections.pusher.secret'), config('broadcasting.connections.pusher.app_id'));
                $auth = $pusher->socket_auth($channel_name, $socket_id);
                return $auth;
            }else {
                header('', true, 403);
                echo "Forbidden";
                exit;
            }
        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function mark_as_read(Request $request)
    { 
        try {

            $logged_in_user_id = $request->logged_user_id;

            if($logged_in_user_id == ''){
                return;
            }

            NotificationModel::where('user_id', $logged_in_user_id)
            ->update(['read' => 1]);

            return response()->json($this->generate_response(
                array(
                    "message" => "Notifications marked as read successfully", 
                    "data"    => ''
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }  
    }

    public function remove_all_notifications(Request $request)
    { 
        try {

            $logged_in_user_id = $request->logged_user_id;

            if($logged_in_user_id == ''){
                return;
            }

            NotificationModel::where('user_id', $logged_in_user_id)
            ->update(['status' => 0]);

            return response()->json($this->generate_response(
                array(
                    "message" => "Notifications deleted successfully", 
                    "data"    => ''
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }  
    }
}

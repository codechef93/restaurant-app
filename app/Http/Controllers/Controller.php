<?php

namespace App\Http\Controllers;

use Session;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // jwt encode class
    public function jwt_encode($encode_data)
    {
        $payload = [
            'iss' => "jwt_token", // Issuer of the token
            'sub' => $encode_data, // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + 60*60*24 // Expiration time in sec, 24 hours
        ];

        // As you can see we are passing `JWT_KEY` as the second parameter that will
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_KEY', config('app.jwt_key')));
    }

    // decode jwt token
    public function jwt_decode($token = "")
    {
        return JWT::decode($token, env('JWT_KEY', config('app.jwt_key')), ['HS256']);
    }

    /**
     * Function that will set user session
    */
    public function set_user_session($user, $token)
    {
        session()->put('fullname', $user->fullname);
        session()->put('firstname', $user->firstname);
        session()->put('profile_image', $user->profile_image);
        session()->put('slack', $user->slack);
        session()->put('user_id', $user->id);
        session()->put('role', $user->role_id);    
        session()->put('initial_link', $user->initial_link);     
        session()->put('access_token', $token);
        //Session::save();
    }

    public function check_user_session($user, $token)
    {
        $user_slack = session('slack');
        if($user_slack != ""){
            return true;
        }else{
            return false;
        }
    }

    function generate_slack($table)
    {
        do{
            $slack = str_random(25);
            $exist = DB::table($table)->where("slack", $slack)->first();
        }while($exist);
        return $slack;
    }

    function generate_response($response_array, $type = "")
    {
        switch($type){
            case "SUCCESS":
                $status_code = 200;
            break;
            case "NOT_AUTHORIZED":
                $status_code = 401;
            break;
            case "NO_ACCESS":
                $status_code = 403;
            break;
            case "BAD_REQUEST":
                $status_code = 400;
            break;
            default:
                $status_code = 200;
            break;
        }
        $response = array(
            'status' => true,
            'msg'    => (isset($response_array['message']))?$response_array['message']:"",
            'data'   => (isset($response_array['data']))?$response_array['data']:"",
            'status_code' => (isset($response_array['status_code']))?$response_array['status_code']:$status_code
        );
        if(isset($response_array['link'])){
            $response = array_merge($response, array("link" => $response_array['link']));
        }
        if(isset($response_array['new_tab'])){
            $response = array_merge($response, array("new_tab" => $response_array['new_tab']));
        }
        if(isset($response_array['orders_link'])){
            $response = array_merge($response, array("orders_link" => $response_array['orders_link']));
        }
        return $response;
    }

    public function no_access_response_for_listing_table(){
        $response = [
            'draw' => 0,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'access' => false
        ];
        return response()->json($response);
    }

    public function get_validation_rules($field, $required = false)
    {
        $rule = "";
        switch($field){
            case 'email' : $rule = "email|max:150|"; break;
            case 'password' : $rule = "alpha_dash|min:6|max:100|"; break;
            case 'fullname' : $rule = "alpha_spaces|max:100|"; break;
            case 'phone' : $rule = "regex:/^[0-9-+()]*$/i|max:15|"; break;
            case 'new_password' : $rule = "alpha_dash|min:6|max:100|confirmed|"; break;
            case 'status' : $rule = "numeric|"; break;
            case 'name_label' : $rule = "nullable|max:250|"; break;
            case 'role_menus' : $rule = 'string|'; break;
            case 'pincode' : $rule = "alpha_num|max:15|"; break;
            case 'text' : $rule = "max:65535|"; break;
            case 'string' : $rule = 'string|'; break;
            case 'numeric' : $rule = "numeric|"; break;
            case 'slack' : $rule = "alpha_num|"; break;
            case 'order_status' : $rule = "in:CLOSE,HOLD,IN_KITCHEN,CUSTOMER_ORDER|"; break;
            case 'codes' : $rule = "alpha_dash|"; break;
            case 'filled' : $rule = "filled|"; break;
            case 'product_image' : $rule = "mimes:jpeg,jpg,png,webp|max:1500"; break;
            case 'company_logo' : $rule = "mimes:jpeg,jpg,png|max:150|"; break;
            case 'invoice_print_logo' : $rule = "mimes:jpeg,jpg,png|max:150|dimensions:width=200,height=100|"; break;
            case 'navbar_logo' : $rule = "mimes:jpeg,jpg,png|max:50|dimensions:width=30,height=30|"; break;
            case 'favicon' : $rule = "mimes:jpeg,jpg,png|max:10|dimensions:width=30,height=30|"; break;
        }

        if($required == true){
            $rule = implode('|', array('required', $rule));
        }else{
            $rule = implode('|', array('nullable', 'sometimes', $rule));
        }
        return $rule;
    }
}

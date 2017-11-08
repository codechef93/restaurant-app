<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\InvalidLinkException;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

use App\Models\User as UserModel;
use App\Models\UserToken as UserTokenModel;

class Entry extends Controller
{
    //This is the function that loads the sign in page
    public function sign_in(Request $request)
    {   
        if(session()->get("access_token") != ""){
            return redirect(session()->get("initial_link")); 
        }
        
        $data['message'] = (session('message'))?session('message'):'';
        $data['is_demo'] = (App::environment('demo'))?true:false;
        $data['preview_mode'] = ($request->query('preview_mode') == 'true')?true:false;
        $data['company_logo'] = config('app.company_logo');
        return view('entry.sign_in', $data);
    }

    //This is the function that calls when user logs out
    public function logout()
    {
        $session_id = session()->getId();
        UserTokenModel::where('session_id', $session_id)->delete();
        session()->flush();
        return redirect('/');
    }

    //This is the function that loads the forgot password page
    public function forgot_password(){
        $data['company_logo'] = config('app.company_logo');
        return view('entry.forgot_password', $data);
    }

    //This is the function that loads the reset password page
    public function reset_password($user_slack, $forgot_password_token){
        
        $user_data = UserModel::select('slack', 'password_reset_max_tries', 'password_reset_last_tried_on')
        ->where([
            ['slack', '=', $user_slack],
            ['password_reset_token', '=', $forgot_password_token]
        ])->first();

        if (!$user_data) {
            throw new InvalidLinkException("Invalid Link");
        }

        $data['user_slack'] = $user_slack;
        $data['password_reset_token'] = $forgot_password_token;
        $data['company_logo'] = config('app.company_logo');

        return view('entry.reset_password', $data);
    }

    //This is the function that loads the password hash when an admin locks out
    public function generate_lockout_password($password_string = null){
        $password_string = ($password_string != "")?$password_string:Str::random(10);
        $hashed_password = Hash::make($password_string);

        $data['hashed_password'] = $hashed_password;
        $data['password'] = $password_string;
        $data['company_logo'] = config('app.company_logo');

        return view('entry.lockout_password', $data);
    }
}
<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

use App\Models\Otp as OtpModel;
use App\Models\SettingEmail as SettingEmailModel;

use App\Mail\DigitalMenuOtp as DigitalMenuOtpMail;

class Otp extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
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

            $current_date = date('Y-m-d');

            $this->validate_request($request);

            $event_type = $request->event_type;

            $otp = rand(10000,999999);

            switch($event_type){
                case 'QR_CUSTOMER_ORDER':

                    $otp_exists = OtpModel::select('id', 'otp', 'generate_counter')
                    ->where('event_type', '=', trim($event_type))
                    ->where('email', '=', trim($request->email))
                    ->where('created_at', 'like', $current_date.'%')
                    ->first();
                    if (!empty($otp_exists)) {
                        if($otp_exists->generate_counter >= 5){
                            throw new Exception("Maximum OTP limit reached", 400);
                        }
                    }

                    $email_setting = SettingEmailModel::select('*')->active()->first();

                    if (!$email_setting) {
                        throw new Exception("Email not configured.");
                    } 

                    Mail::to(trim($request->email))->send(new DigitalMenuOtpMail(['otp' => $otp]));
                break;
            }

            DB::beginTransaction();
            
            if (!empty($otp_exists)) {
                if($otp_exists->id){
                    $otp = [
                        "otp" => $otp,
                        "generate_counter" => $otp_exists->generate_counter+1
                    ];
                    OtpModel::where('id', $otp_exists->id)
                    ->update($otp);
                }
            }else{
                $otp = [
                    "event_type" => trim($event_type),
                    "user_id" => isset($request->user_id)?$request->user_id:NULL,
                    "customer_id" => isset($request->customer_id)?$request->customer_id:NULL,
                    "email" => isset($request->email)?$request->email:NULL,
                    "phone" => isset($request->phone)?$request->phone:NULL,
                    "otp" => $otp,
                    "generate_counter" => 1
                ];
                $otp_id = OtpModel::create($otp)->id;
            }

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "OTP generated successfully"
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
        //  
    }

    /**
     * list all the specified resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function validate_otp($request)
    {
        try {
            $this->validate_request($request);
            $event_type = $request->event_type;
            $otp = $request->otp;

            $otp_valid = false;

            switch($event_type){
                case 'QR_CUSTOMER_ORDER':
                    $email = $request->email;

                    $otp_exists = OtpModel::select('id', 'otp', 'generate_counter')
                    ->where('event_type', '=', trim($event_type))
                    ->where('email', '=', trim($email))
                    ->where('otp', '=', trim($otp))
                    ->first();

                    if (!empty($otp_exists)) {

                        OtpModel::where([
                            ['event_type', '=', trim($event_type)],
                            ['email', '=', trim($email)]
                        ])
                        ->delete();

                        $otp_valid = true;
                    }
                break;
            }

            if($otp_valid == true){
                return response()->json($this->generate_response(
                    array(
                        "message" => "OTP validated successfully"
                    ), 'SUCCESS'
                ));
            }else{
                throw new Exception("Invalid OTP provided", 400);
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

    public function validate_request($request)
    {
        $validate_fields = [
            'event_type' => 'required',
        ];

        $event_type = $request->event_type;
        switch($event_type){
            case 'QR_CUSTOMER_ORDER':
                $validate_fields = array_merge($validate_fields, ['email' => $this->get_validation_rules("email", true)]);
            break;
        }
        $validator = Validator::make($request->all(), $validate_fields);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}

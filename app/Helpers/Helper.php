<?php

use App\Models\Menu as MenuModel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Models\User as UserModel;
use Illuminate\Support\Facades\Route;

if (! function_exists('check_access')) {
    function check_access($menu_array, $return = false){
        $access_check_array = [];

        $logged_user_role_id = request()->logged_user_role_id;

        //For super admin by default allow all the actions and menus
        if ($logged_user_role_id != 1) {
            if($menu_array == 'A_DELETE_ORDER')
                $access_check_array[] = false;
            else{
                $menu_ids = MenuModel::select('id')->active()->whereIn('menu_key', $menu_array)->get()->pluck('id');
                if (count($menu_ids) != count($menu_array)) {
                    $access_check_array[] = false;
                }
            
                $logged_user_menus_access = request()->logged_user_menus;
                if (count($menu_ids) > 0) {
                    foreach ($menu_ids as $menu_array_item) {
                        if (is_array($logged_user_menus_access) && !in_array($menu_array_item, $logged_user_menus_access) ) {
                            $access_check_array[] = false;
                        } else {
                            $access_check_array[] = true;
                        }
                    }
                } else {
                    $access_check_array[] = false;
                }
            }            
        }else{
            $access_check_array[] = true;
        }

        $access_check_result = (in_array(false, $access_check_array))?false:true;
        
        if($return == false){
            ($access_check_result == false)?abort(404):$access_check_result;
        }else{
            return $access_check_result;
        }
    }
}

if (! function_exists('get_profile_photo')) {
    function get_profile_photo($photo){
        $view_dir = Config::get('constants.upload.profile.view_path');
        $default_profile_image = Config::get('constants.upload.profile.default');
        $image_link = ($photo != null && $photo != '')?asset($view_dir.'small_'.$photo):asset($default_profile_image);
        return $image_link;
    }
}

if (! function_exists('number_format_short')) {
    function number_format_short( $n, $precision = 1 ) {
        if ($n < 900) {
            // 0 - 900
            $n_format = number_format($n, $precision);
            $suffix = '';
        } else if ($n < 900000) {
            // 0.9k-850k
            $n_format = number_format($n / 1000, $precision);
            $suffix = 'K';
        } else if ($n < 900000000) {
            // 0.9m-850m
            $n_format = number_format($n / 1000000, $precision);
            $suffix = 'M';
        } else if ($n < 900000000000) {
            // 0.9b-850b
            $n_format = number_format($n / 1000000000, $precision);
            $suffix = 'B';
        } else {
            // 0.9t+
            $n_format = number_format($n / 1000000000000, $precision);
            $suffix = 'T';
        }
        // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
        // Intentionally does not affect partials, eg "1.50" -> "1.50"
        if ( $precision > 0 ) {
            $dotzero = '.' . str_repeat( '0', $precision );
            $n_format = str_replace( $dotzero, '', $n_format );
        }
        return $n_format . $suffix;
    }
}

if (! function_exists('skip_zero_array_filter')) {
    function skip_zero_array_filter($var){
        return ($var !== NULL && $var !== FALSE && $var !== '');
    }
}

if (! function_exists('generate_date_range')) {
    function generate_date_range($start, $end, $step = '+1 day', $format = 'Y-m-d'){
        $dates = [];

        $current = strtotime( $start );
        $last = strtotime( $end );

        while( $current <= $last ) {

            $dates[] = date( $format, $current );
            $current = strtotime( $step, $current );
        }

        return $dates;
    }
}

if (! function_exists('calculate_tax')) {
    function calculate_tax($item_total, $tax_percentage){
        $tax_amount = ($tax_percentage/100)*$item_total;
        return $tax_amount;
    }
}
?>
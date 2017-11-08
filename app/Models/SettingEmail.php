<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SettingEmail extends Model
{
    protected $table = 'setting_mail';
    protected $hidden = ['id'];
    protected $fillable = ['slack', 'type', 'driver', 'host', 'port', 'encryption', 'username', 'password', 'from_email', 'from_email_name', 'status', 'created_by', 'updated_by'];

    public function scopeActive($query){
        return $query->where('status', 1);
    }

    public function scopeStatusJoin($query){
        return $query->leftJoin('master_status', function ($join) {
            $join->on('master_status.value', '=', 'category.status');
            $join->where('master_status.key', '=', 'MAIL_SETTING_STATUS');
        });
    }

    /* For view files */

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }
    
    public function status_data(){
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'MAIL_SETTING_STATUS');
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $hidden = ['id'];
    protected $fillable = ['slack', 'user_id', 'store_id', 'notification_text', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
    }

    public function scopeActive($query){
        return $query->where('notifications.status', 1);
    }

    public function scopeChooseUser($query, $logged_user_id){
        return $query->where(function($query) use ($logged_user_id){
            $query->where('notifications.user_id', '=', $logged_user_id)
            ->orWhere('notifications.created_by', '=', $logged_user_id);
        });
    }

    public function scopeStatusJoin($query){
        return $query->leftJoin('master_status', function ($join) {
            $join->on('master_status.value', '=', 'notifications.status');
            $join->where('master_status.key', '=', 'NOTIFICATION_STATUS');
        });
    }

    public function scopeNotifiedUser($query){
        return $query->leftJoin('users AS user_notified', function ($join) {
            $join->on('user_notified.id', '=', 'notifications.user_id');
        });
    }

    public function scopeCreatedUser($query){
        return $query->leftJoin('users AS user_created', function ($join) {
            $join->on('user_created.id', '=', 'notifications.created_by');
        });
    }

    public function scopeUpdatedUser($query){
        return $query->leftJoin('users AS user_updated', function ($join) {
            $join->on('user_created.id', '=', 'notifications.updated_by');
        });
    }

    /* For view files */

    public function store_data(){
        return $this->hasOne('App\Models\Store', 'id', 'store_id')->select('name', 'store_code');
    }

    public function notificationUser(){
        return $this->hasOne('App\Models\User', 'id', 'user_id')->select(['slack', 'fullname', 'email', 'user_code', 'profile_image']);
    }

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code', 'profile_image']);
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code', 'profile_image']);
    }
    
    public function status_data(){
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'NOTIFICATION_STATUS');
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}

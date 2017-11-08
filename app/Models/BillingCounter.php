<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class BillingCounter extends Model
{
    protected $table = 'billing_counters';
    protected $hidden = ['id', 'store_id'];
    protected $fillable = ['slack', 'store_id', 'billing_counter_code', 'counter_name', 'description', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new StoreScope);
    }

    public function scopeActive($query){
        return $query->where('billing_counters.status', 1);
    }

    public function scopeStatusJoin($query){
        return $query->leftJoin('master_status', function ($join) {
            $join->on('master_status.value', '=', 'billing_counters.status');
            $join->where('master_status.key', '=', 'BILLING_COUNTER_STATUS');
        });
    }

    public function scopeBusinessRegisterJoin($query){
        return $query->leftJoin('business_registers', function ($join) {
            $join->on('business_registers.billing_counter_id', '=', 'billing_counters.id');
            $join->where('business_registers.current_register', '=', 1);
        });
    }

    public function scopeCreatedUser($query){
        return $query->leftJoin('users AS user_created', function ($join) {
            $join->on('user_created.id', '=', 'billing_counters.created_by');
        });
    }

    public function scopeUpdatedUser($query){
        return $query->leftJoin('users AS user_updated', function ($join) {
            $join->on('user_created.id', '=', 'billing_counters.updated_by');
        });
    }

    /* For view files */

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function businessRegister(){
        return $this->hasOne('App\Models\BusinessRegister', 'billing_counter_id', 'id')->where("current_register", 1);
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function status_data(){
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'BILLING_COUNTER_STATUS');
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}

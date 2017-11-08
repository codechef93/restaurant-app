<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class BusinessRegister extends Model
{
    protected $table = 'business_registers';
    protected $hidden = ['id', 'store_id'];
    protected $fillable = ['slack', 'store_id', 'user_id', 'billing_counter_id', 'parent_register_id', 'current_register', 'opening_date', 'closing_date', 'joining_date', 'exit_date', 'opening_amount', 'closing_amount', 'credit_card_slips', 'cheques', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new StoreScope);
    }

    public function scopeUser($query){
        return $query->leftJoin('users AS user', function ($join) {
            $join->on('user.id', '=', 'business_registers.user_id');
        });
    }

    public function scopeCreatedUser($query){
        return $query->leftJoin('users AS user_created', function ($join) {
            $join->on('user_created.id', '=', 'business_registers.created_by');
        });
    }

    public function scopeUpdatedUser($query){
        return $query->leftJoin('users AS user_updated', function ($join) {
            $join->on('user_created.id', '=', 'business_registers.updated_by');
        });
    }

     /* For view files */

    public function user(){
        return $this->hasOne('App\Models\User', 'id', 'user_id')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function billing_counter(){
        return $this->hasOne('App\Models\BillingCounter', 'id', 'billing_counter_id');
    }

    public function sub_registers(){
        return $this->hasMany('App\Models\BusinessRegister', 'parent_register_id', 'id');
    }

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}

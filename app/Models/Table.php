<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class Table extends Model
{
    protected $table = 'restaurant_tables';
    protected $hidden = ['id', 'store_id'];
    protected $fillable = ['slack', 'store_id', 'table_number', 'no_of_occupants', 'status', 'restoarea_id', 'x', 'y', 'w', 'h', 'rounded', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new StoreScope);
    }

    public function scopeActive($query){
        return $query->where('restaurant_tables.status', 1);
    }

    public function scopeWaiterJoin($query){
        return $query->leftJoin('users', function ($join) {
            $join->on('users.id', '=', 'restaurant_tables.waiter_user_id');
        });
    }

    public function scopeStatusJoin($query){
        return $query->leftJoin('master_status', function ($join) {
            $join->on('master_status.value', '=', 'restaurant_tables.status');
            $join->where('master_status.key', '=', 'RESTAURANT_TABLE_STATUS');
        });
    }

    public function scopeCreatedUser($query){
        return $query->leftJoin('users AS user_created', function ($join) {
            $join->on('user_created.id', '=', 'restaurant_tables.created_by');
        });
    }

    public function scopeUpdatedUser($query){
        return $query->leftJoin('users AS user_updated', function ($join) {
            $join->on('user_created.id', '=', 'restaurant_tables.updated_by');
        });
    }

    /* For view files */

    public function waiter_data(){
        return $this->hasOne('App\Models\User', 'id', 'waiter_user_id')->active();
    }

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }
    
    public function status_data(){
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'RESTAURANT_TABLE_STATUS');
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}

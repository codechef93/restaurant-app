<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MasterOrderType extends Model
{
    protected $table = 'master_order_type';
    protected $hidden = ['id'];
    protected $fillable = ['order_type_constant', 'label', 'description', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
    }

    public function scopeActive($query){
        return $query->where('status', 1);
    }

    public function scopeSortLabelAsc($query){
        return $query->orderBy('master_order_type.label', 'asc');
    }

    public function scopeFilterByTransactionTypeConstant($query, $constant){
        return $query->where('master_order_type.order_type_constant', $constant);
    }

    public function scopeStatusJoin($query){
        return $query->leftJoin('master_status', function ($join) {
            $join->on('master_status.value', '=', 'master_order_type.status');
            $join->where('master_status.key', '=', 'MASTER_ORDER_TYPE_STATUS');
        });
    }

    public function scopeCreatedUser($query){
        return $query->leftJoin('users AS user_created', function ($join) {
            $join->on('user_created.id', '=', 'master_order_type.created_by');
        });
    }

    public function scopeUpdatedUser($query){
        return $query->leftJoin('users AS user_updated', function ($join) {
            $join->on('user_created.id', '=', 'master_order_type.updated_by');
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
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'MASTER_ORDER_TYPE_STATUS');
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}

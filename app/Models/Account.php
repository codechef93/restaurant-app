<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class Account extends Model
{
    protected $table = 'accounts';
    protected $hidden = ['id', 'store_id'];
    protected $fillable = ['slack', 'store_id', 'account_code', 'account_type', 'label', 'initial_balance', 'description', 'pos_default', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new StoreScope);
    }

    public function scopeActive($query){
        return $query->where('accounts.status', 1);
    }

    public function scopeSortLabelAsc($query){
        return $query->orderBy('accounts.label', 'asc');
    }

    public function scopeStatusJoin($query){
        return $query->leftJoin('master_status', function ($join) {
            $join->on('master_status.value', '=', 'accounts.status');
            $join->where('master_status.key', '=', 'ACCOUNT_STATUS');
        });
    }

    public function scopeCreatedUser($query){
        return $query->leftJoin('users AS user_created', function ($join) {
            $join->on('user_created.id', '=', 'accounts.created_by');
        });
    }

    public function scopeMasterAccountTypeJoin($query){
        return $query->leftJoin('master_account_type', function ($join) {
            $join->on('master_account_type.id', '=', 'accounts.account_type');
        });
    }

    public function scopeUpdatedUser($query){
        return $query->leftJoin('users AS user_updated', function ($join) {
            $join->on('user_created.id', '=', 'accounts.updated_by');
        });
    }

    /* For view files */

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function account_type_data(){
        return $this->hasOne('App\Models\MasterAccountType', 'id', 'account_type');
    }
    
    public function status_data(){
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'ACCOUNT_STATUS');
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}

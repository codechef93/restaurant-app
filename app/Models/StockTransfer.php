<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class StockTransfer extends Model
{
    protected $table = 'stock_transfer';
    protected $hidden = ['id', 'store_id', 'from_store_id' , 'to_store_id'];
    protected $fillable = ['slack', 'store_id', 'stock_transfer_reference', 'from_store_id', 'from_store_code', 'from_store_name', 'to_store_id', 'to_store_code', 'to_store_name', 'notes', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
    }

    public function scopeIsDeletable($query){
        return $query->where('stock_transfer.status', 0);
    }

    public function scopeResolveStore($query, $store_id){
        return $query->where(function($query) use ($store_id){
            $query->where('stock_transfer.from_store_id', '=', $store_id)
            ->orWhere('stock_transfer.to_store_id', '=', $store_id);
        });
    }

    public function scopeStatusJoin($query){
        return $query->leftJoin('master_status', function ($join) {
            $join->on('master_status.value', '=', 'stock_transfer.status');
            $join->where('master_status.key', '=', 'STOCK_TRANSFER_STATUS');
        });
    }

    public function scopeCreatedUser($query){
        return $query->leftJoin('users AS user_created', function ($join) {
            $join->on('user_created.id', '=', 'stock_transfer.created_by');
        });
    }

    public function scopeUpdatedUser($query){
        return $query->leftJoin('users AS user_updated', function ($join) {
            $join->on('user_created.id', '=', 'stock_transfer.updated_by');
        });
    }

    /* For view files */

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function fromStoreData(){
        return $this->hasOne('App\Models\Store', 'id', 'from_store_id');
    }

    public function toStoreData(){
        return $this->hasOne('App\Models\Store', 'id', 'to_store_id');
    }
    
    public function products(){
        return $this->hasMany('App\Models\StockTransferProduct', 'stock_transfer_id', 'id');
    }

    public function status_data(){
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'STOCK_TRANSFER_STATUS');
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $hidden = ['id', 'store_id', 'bill_to_id'];
    protected $fillable = ['slack', 'store_id', 'transaction_code', 'account_id', 'transaction_type', 'payment_method_id', 'payment_method', 'bill_to', 'bill_to_id', 'bill_to_name', 'bill_to_contact', 'bill_to_address', 'currency_code', 'amount', 'notes', 'pg_transaction_id', 'pg_transaction_status', 'transaction_date', 'transaction_merged', 'merged_to', 'merged_from', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new StoreScope);
    }

    public function scopeNotMerged($query){
        return $query->where([
            ['transactions.transaction_merged', '=', 0],
        ]);
    }

    public function scopeCreatedUser($query){
        return $query->leftJoin('users AS user_created', function ($join) {
            $join->on('user_created.id', '=', 'transactions.created_by');
        });
    }

    public function scopeUpdatedUser($query){
        return $query->leftJoin('users AS user_updated', function ($join) {
            $join->on('user_created.id', '=', 'transactions.updated_by');
        });
    }

    public function scopeMasterTransactionTypeJoin($query){
        return $query->leftJoin('master_transaction_type', function ($join) {
            $join->on('master_transaction_type.id', '=', 'transactions.transaction_type');
        });
    }

    public function scopeAccountJoin($query){
        return $query->leftJoin('accounts', function ($join) {
            $join->on('accounts.id', '=', 'transactions.account_id');
        });
    }

    /* For view files */

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function account(){
        return $this->hasOne('App\Models\Account', 'id', 'account_id');
    }

    public function order(){
        return $this->hasOne('App\Models\Order', 'id', 'bill_to_id');
    }

    public function invoice(){
        return $this->hasOne('App\Models\Invoice', 'id', 'bill_to_id');
    }

    public function customer(){
        return $this->hasOne('App\Models\Customer', 'id', 'bill_to_id');
    }

    public function supplier(){
        return $this->hasOne('App\Models\Supplier', 'id', 'bill_to_id');
    }

    public function payment_method_data(){
        return $this->hasOne('App\Models\PaymentMethod', 'id', 'payment_method_id');
    }

    public function transaction_type_data(){
        return $this->hasOne('App\Models\MasterTransactionType', 'id', 'transaction_type');
    }

    public function parseDateOnly($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_format")):null;
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}

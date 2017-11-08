<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class PaymentMethod extends Model
{
    protected $table = 'payment_methods';
    protected $hidden = ['id', 'store_id'];
    protected $fillable = ['slack', 'label', 'key_1', 'key_2', 'description', 'activate_on_digital_menu', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
    }

    public function scopeActive($query){
        return $query->where('payment_methods.status', 1);
    }

    public function scopeActiveOnDigitalMenu($query){
        return $query->where('payment_methods.activate_on_digital_menu', 1);
    }

    public function scopeSortLabelAsc($query){
        return $query->orderBy('payment_methods.label', 'asc');
    }

    public function scopeSkipPaymentGateway($query){
        return $query->where('payment_methods.payment_constant', '=', '')
        ->orWhereNull('payment_methods.payment_constant');
    }

    public function scopeStatusJoin($query){
        return $query->leftJoin('master_status', function ($join) {
            $join->on('master_status.value', '=', 'payment_methods.status');
            $join->where('master_status.key', '=', 'PAYMENT_METHOD_STATUS');
        });
    }

    public function scopeCreatedUser($query){
        return $query->leftJoin('users AS user_created', function ($join) {
            $join->on('user_created.id', '=', 'payment_methods.created_by');
        });
    }

    public function scopeUpdatedUser($query){
        return $query->leftJoin('users AS user_updated', function ($join) {
            $join->on('user_created.id', '=', 'payment_methods.updated_by');
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
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'PAYMENT_METHOD_STATUS');
    } 

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}

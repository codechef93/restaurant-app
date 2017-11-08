<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class Invoice extends Model
{
    protected $table = 'invoices';
    protected $hidden = ['id', 'store_id', 'invoice_to_id'];
    protected $fillable = ['slack', 'store_id', 'invoice_number', 'invoice_reference', 'invoice_date', 'invoice_due_date', 'parent_po_id', 'bill_to', 'bill_to_id', 'bill_to_code', 'bill_to_name', 'bill_to_email', 'bill_to_contact', 'bill_to_address', 'currency_name', 'currency_code', 'tax_option_id', 'subtotal_excluding_tax', 'total_discount_amount', 'total_after_discount', 'total_tax_amount', 'shipping_charge', 'packing_charge', 'total_order_amount', 'notes', 'terms', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new StoreScope);
    }

    public function scopeActive($query){
        return $query->where('status', 1);
    }

    public function scopeClosed($query){
        return $query->where('status', 4);
    }

    public function scopeStatusJoin($query){
        return $query->leftJoin('master_status', function ($join) {
            $join->on('master_status.value', '=', 'invoices.status');
            $join->where('master_status.key', '=', 'INVOICE_STATUS');
        });
    }

    public function scopeCreatedUser($query){
        return $query->leftJoin('users AS user_created', function ($join) {
            $join->on('user_created.id', '=', 'invoices.created_by');
        });
    }

    public function scopeUpdatedUser($query){
        return $query->leftJoin('users AS user_updated', function ($join) {
            $join->on('user_created.id', '=', 'invoices.updated_by');
        });
    }

    /* For view files */

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function storeData(){
        return $this->hasOne('App\Models\Store', 'id', 'store_id');
    }
    
    public function customer(){
        return $this->hasOne('App\Models\Customer', 'id', 'bill_to_id');
    }

    public function supplier(){
        return $this->hasOne('App\Models\Supplier', 'id', 'bill_to_id');
    }

    public function products(){
        return $this->hasMany('App\Models\InvoiceProduct', 'invoice_id', 'id')->where('invoice_products.status', 1);
    }

    public function transactions(){
        return $this->hasMany('App\Models\Transaction', 'bill_to_id', 'id')->where('transactions.bill_to', 'INVOICE')->orderBy('transactions.transaction_date', 'desc');;
    }

    public function status_data(){
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'INVOICE_STATUS');
    }

    public function tax_option_data(){
        return $this->hasOne('App\Models\MasterTaxOption', 'id', 'tax_option_id')->where('master_tax_option.status', 1);
    }

    public function parseDateOnly($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_format")):null;
    }
    
    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}

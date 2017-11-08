<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class Taxcode extends Model
{
    protected $table = 'tax_codes';
    protected $hidden = ['id', 'store_id'];
    protected $fillable = ['slack', 'store_id', 'tax_type', 'tax_code', 'label', 'tax_percentage', 'description', 'status', 'created_by', 'updated_by'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new StoreScope);
    }
    
    public function scopeActive($query){
        return $query->where('status', 1);
    }

    public function scopeFilterExclusive($query){
        return $query->where('tax_type', 'EXCLUSIVE');
    }

    public function scopeFilterInclusive($query){
        return $query->where('tax_type', 'INCLUSIVE');
    }

    public function scopeSortLabelAsc($query){
        return $query->orderBy('tax_codes.label', 'asc');
    }

    public function scopeStatusJoin($query){
        return $query->leftJoin('master_status', function ($join) {
            $join->on('master_status.value', '=', 'tax_codes.status');
            $join->where('master_status.key', '=', 'TAX_CODE_STATUS');
        });
    }

    public function scopeCreatedUser($query){
        return $query->leftJoin('users AS user_created', function ($join) {
            $join->on('user_created.id', '=', 'tax_codes.created_by');
        });
    }

    public function scopeUpdatedUser($query){
        return $query->leftJoin('users AS user_updated', function ($join) {
            $join->on('user_created.id', '=', 'tax_codes.updated_by');
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
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'TAX_CODE_STATUS');
    }

    public function tax_components(){
        return $this->hasMany('App\Models\TaxcodeType', 'tax_code_id', 'id');
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}

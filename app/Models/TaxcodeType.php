<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TaxcodeType extends Model
{
    protected $table = 'tax_code_type';
    protected $hidden = ['id'];
    protected $fillable = ['tax_code_id', 'tax_type', 'tax_percentage', 'created_by', 'created_at', 'updated_at'];

    public function scopeTaxCode($query){
        return $query->leftJoin('tax_codes', function ($join) {
            $join->on('tax_codes.id', '=', 'tax_code_type.tax_code_id');
        });
    }

    /* For view files */

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}

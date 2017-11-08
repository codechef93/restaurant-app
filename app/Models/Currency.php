<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'country';
    protected $hidden = ['id'];
    protected $fillable = ['name', 'code', 'dial_code', 'currency_name', 'currency_code', 'currency_symbol', 'status', 'created_at', 'updated_at'];

    public function scopeActive($query){
        return $query->where('status', 1);
    }

    /* For view files */

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by');
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by');
    }
    
    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}

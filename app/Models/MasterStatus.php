<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MasterStatus extends Model
{
    protected $table = 'master_status';
    protected $hidden = ['id'];

    public function scopeActive($query){
        return $query->where('status', 1);
    }

    public function scopeFilterByKey($query, $key){
        return $query->where('master_status.key', $key);
    }

    public function scopeSortValueAsc($query){
        return $query->orderBy('master_status.value', 'asc');
    }

    public function scopeFilterByValueConstant($query, $status_key, $status_constant){
        return $query->where([
            ['master_status.key', '=', $status_key],
            ['master_status.value_constant', '=', $status_constant]
        ]);
    }

    public function scopeFilterByValue($query, $status_key, $status){
        return $query->where([
            ['master_status.key', '=', $status_key],
            ['master_status.value', '=', $status]
        ]);
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}

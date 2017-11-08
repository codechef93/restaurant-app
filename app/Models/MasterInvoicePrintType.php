<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MasterInvoicePrintType extends Model
{
    protected $table = 'master_invoice_print_type';
    protected $hidden = ['id'];

    public function scopeActive($query){
        return $query->where('status', 1);
    }

    public function scopeStatusJoin($query){
        return $query->leftJoin('master_status', function ($join) {
            $join->on('master_status.value', '=', 'master_invoice_print_type.status');
            $join->where('master_status.key', '=', 'MASTER_INVOICE_PRINT_TYPE_STATUS');
        });
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}

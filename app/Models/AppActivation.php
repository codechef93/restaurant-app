<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AppActivation extends Model
{
    protected $table = 'app_activation';
    protected $hidden = ['app_activation'];
    protected $fillable = ['activation_code', 'created_at', 'updated_at'];
}

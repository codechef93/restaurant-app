<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $table = 'otp';
    protected $hidden = ['id', 'user_id', 'customer_id'];
    protected $fillable = ['event_type', 'user_id', 'customer_id', 'email', 'phone', 'otp', 'generate_counter', 'created_at', 'updated_at'];

}
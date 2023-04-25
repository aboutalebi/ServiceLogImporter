<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_name',
        'log_at',
        'request_type',
        'query_string',
        'status_code'
    ];
}

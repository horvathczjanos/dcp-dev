<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BadgeConnectExportedAssertion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'host_id',
        'assertion_id',
        'mode',
        'request_json',
        'response_json',
        'http_status',
        'success',
        'last_error',
        'exported_at',
    ];

    protected $casts = [
        'request_json' => 'array',
        'response_json' => 'array',
        'success' => 'boolean',
        'exported_at' => 'datetime',
    ];
}

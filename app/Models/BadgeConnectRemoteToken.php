<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BadgeConnectRemoteToken extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'host_id',
        'access_token',
        'refresh_token',
        'token_type',
        'expires_in',
        'expires_at',
        'scope',
        'token_response',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'token_response' => 'array',
    ];
}

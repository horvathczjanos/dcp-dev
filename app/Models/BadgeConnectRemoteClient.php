<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BadgeConnectRemoteClient extends Model
{
    use HasFactory;
    protected $fillable = [
        'host_id',
        'client_id',
        'client_secret',
        'client_id_issued_at',
        'client_secret_expires_at',
        'redirect_uri',
        'scope',
        'registration_request',
        'registration_response',
        'registered_at',
        'last_error',
    ];

    protected $casts = [
        'registration_request' => 'array',
        'registration_response' => 'array',
        'registered_at' => 'datetime',
    ];

    public function host()
    {
        return $this->belongsTo(BadgeConnectHost::class, 'host_id');
    }
}

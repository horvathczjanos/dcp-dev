<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BadgeConnectHost extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'base_url',
        'manifest_url',
        'api_base',
        'registration_url',
        'authorization_url',
        'token_url',
        'token_revocation_url',
        'scopes_offered',
        'manifest_json',
        'is_active',
        'manifest_fetched_at',
        'last_error',
    ];

    protected $casts = [
        'scopes_offered' => 'array',
        'manifest_json' => 'array',
        'is_active' => 'boolean',
        'manifest_fetched_at' => 'datetime',
    ];

    public function remoteClient()
    {
        return $this->hasOne(BadgeConnectRemoteClient::class, 'host_id');
    }
    public function authorizationSessions()
    {
        return $this->hasMany(BadgeConnectAuthorizationSession::class, 'host_id');
    }

    public function remoteTokens()
    {
        return $this->hasMany(\App\Models\BadgeConnectRemoteToken::class, 'host_id');
    }
}

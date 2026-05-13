<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BadgeConnectAuthorizationSession extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'host_id',
        'state',
        'code_verifier',
        'code_challenge',
        'requested_scope',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function host()
    {
        return $this->belongsTo(BadgeConnectHost::class, 'host_id');
    }
}

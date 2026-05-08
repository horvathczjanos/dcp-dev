<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BadgeAssertionsV21 extends Model
{
    protected $table = 'badge_assertions_v21';

    protected $fillable = [
        'user_id',
        'external_id',
        'badge_class_id',
        'issuer_id',
        'recipient',
        'issued_on',
        'expires_at',
        'revoked',
        'revoked_reason',
        'revoked_at',
        'image',
        'evidence',
        'payload',
    ];

    protected $casts = [
        'recipient' => 'array',
        'evidence' => 'array',
        'payload' => 'array',
        'issued_on' => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
        'revoked' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
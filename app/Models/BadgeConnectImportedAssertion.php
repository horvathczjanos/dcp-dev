<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BadgeConnectImportedAssertion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'host_id',
        'external_id',
        'assertion_json',
        'signed_assertion',
        'imported_at',
    ];

    protected $casts = [
        'assertion_json' => 'array',
        'imported_at' => 'datetime',
    ];
}

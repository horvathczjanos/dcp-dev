<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BadgeConnectImportedProfile extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'host_id',
        'profile_json',
        'response_json',
        'imported_at',
    ];

    protected $casts = [
        'profile_json' => 'array',
        'response_json' => 'array',
        'imported_at' => 'datetime',
    ];
}

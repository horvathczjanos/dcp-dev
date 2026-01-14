<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issuer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'url', 'email', 'customfield'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preassertion extends Model
{
    use HasFactory;


    public function getBadgeNameAttribute()
    {

        $badgeclassID = $this->badgeclassID;
        $badge = Badgeclass::where('badgeclassID', '=', $badgeclassID)->first();

        return $badge->name;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifierBadge extends Model
{
    use HasFactory;


    public function getBadgeclassDetails()
    {

        

        return $this->verifierbadgeID;
    }

    public function getBadgeClassAttribute()
    {

        $item = $this->getBadgeclassDetails();

        //$backData = unserialize($this->cachedBadgeReport);

        //$item = isset($backData['checkOB']['badgeClass']) ? $backData['checkOB']['badgeClass'] : false;

        return $item;
    }
}

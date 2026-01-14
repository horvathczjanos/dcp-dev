<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Badgeclass extends Model
{
    use HasFactory;
    protected $fillable = ['type'];

    public function getBadgeNameAttribute()
    {
        return $this->name;
    }

    public function getBadgeDescriptionAttribute()
    {
        return $this->description;
    }

    public function getBadgeClassImageAttribute()
    {

        $item = (Badgeimage::where('badgeclassID', '=', $this->badgeclassID)->pluck('filepath'))[0];

        return $item;
    }

    public function getBadgeclassImageDataAttribute()
    {

        //ddd($this->badge_class_image);
        //$thisBadgeImage = Badgeimage::where('badgeclassID', '=', $this->badgclassID)->first()->filepath;
        $badgeImage = Storage::get(substr((Badgeimage::where('badgeclassID', '=', $this->badgeclassID)->first('filepath')->filepath), 1));

        return $badgeImage;
    }

    public function getIsInMetabadgeruleAttribute()
    {

        $badgeclassID = $this->badgeclassID;

        $metabadgeRules = IssuerMetabadgeRule::where('deleted', '=', '0')->get();

        foreach ($metabadgeRules as $key => $rule) {

            $rulecontent = json_decode($rule->rulecontent, true);

            if ($rulecontent['badgeMustHaveRule']['metaBadge'] == $badgeclassID) {
                return true;
            }
        }

        return false;
    }

    public function getGetMetabadgeruleAttribute()
    {


        $badgeclassID = $this->badgeclassID;

        $metabadgeRules = IssuerMetabadgeRule::where('deleted', '=', '0')->get();

        foreach ($metabadgeRules as $key => $rule) {

            $rulecontent = json_decode($rule->rulecontent, true);

            if ($rulecontent['badgeMustHaveRule']['metaBadge'] == $badgeclassID) {
                return $rule->rulecontent;
            }
        }

        return false;
    }
}

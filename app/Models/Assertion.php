<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Assertion extends Model
{
    use HasFactory;

    protected $fillable = ['assertiontype'];

    public function numberOfAssertions()
    {
        return Assertion::count();
    }

    public function getBadgeNameAttribute()
    {

        $badgeclassID = $this->badgeclassID;
        $badge = Badgeclass::where('badgeclassID', '=', $badgeclassID)->first();

        return $badge->name;
    }

    public function getBadgeImageAttribute()
    {

        $badgeImage = Storage::get(substr((Badgeimage::where('badgeclassID', '=', $this->badgeclassID)->first('filepath')->filepath), 1));

        return $badgeImage;
    }


    public function getIssuerIdAttribute()
    {
        $badgeclassID = $this->badgeclassID;
        $badge = Badgeclass::where('badgeclassID', '=', $badgeclassID)->first();

        return $badge->issuerID;
    }


    public function getBadgeCriteriaAttribute()
    {
        $badgeclassID = $this->badgeclassID;
        $badge = Badgeclass::where('badgeclassID', '=', $badgeclassID)->first();

        return $badge->criteria;
    }

    public function getRecipientFirstNameAttribute()
    {
        $assertionType = json_decode($this->assertiontype, true);

        if ($this->is_green_cert) {
            return '';
        } else {
            return $assertionType['form'][0]['fields'][0]['content'];
        }
    }

    public function getRecipientLastNameAttribute()
    {
        $assertionType = json_decode($this->assertiontype, true);


        if ($this->is_green_cert) {
            return $assertionType['form'][0]['fields'][0]['load'][1]['content'];
        } else {

            return $assertionType['form'][0]['fields'][1]['content'];
        }
    }

    public function getEarnerEvidenceAttribute()
    {
        $assertionType = json_decode($this->assertiontype, true);


        return $assertionType['form'][0]['fields'][18]['content'];
    }

    public function getIsStackmemberAttribute()
    {
        if (count(Stackorder::where('badgeclassID', '=', $this->badgeclassID)->get())) {
            return true;
        } else {
            return false;
        }
    }


    public function getGetStackmemberIdAttribute()
    {
        if ($this->is_stackmember) {
            if ($item = (Stackorder::where('badgeclassID', '=', $this->badgeclassID)->first()->stackmemberID)) {

                return $item;
            } else {
                return false;
            }
        }
    }

    public function getAreAllPartsIfStackAttribute()
    {

        if ($this->is_stackmember) {

            if (($stackmemberRoles = (Stackorder::where('badgeclassID', '=', $this->badgeclassID)->first()->get_all_members_role_from_stack)) &&
                ($stackmemberBadgeclassIDs = (Stackorder::where('badgeclassID', '=', $this->badgeclassID)->first()->get_all_members_badgeclassid_from_stack))
            ) {

                $onlyParts = $stackmemberRoles->filter(function ($item) {
                    return $item == 'part';
                });

                //ddd($onlyParts);

                $onlyPartsBadgeclassID = $stackmemberBadgeclassIDs->intersectByKeys($onlyParts);

                //ddd($onlyPartsBadgeclassID);

                //ddd($onlyPartsBadgeclassID->toArray());

                //ddd(Assertion::whereIn('badgeclassID', $onlyPartsBadgeclassID->toArray())->where('originalIdentity', $this->originalIdentity)->get()->count());

                //ddd($onlyPartsBadgeclassID->count());

                //$a = Assertion::whereIn('badgeclassID', $onlyPartsBadgeclassID->toArray())->where('originalIdentity', $this->originalIdentity)->get()->count();
                //$b = $onlyPartsBadgeclassID->count();


                if (Assertion::whereIn('badgeclassID', $onlyPartsBadgeclassID->toArray())->where('originalIdentity', $this->originalIdentity)->get()->count() == $onlyPartsBadgeclassID->count()) {

                    return true;
                }
            }
        }
        return false;
    }

    public function getIsMetabadgeAwardedIfStackAttribute()
    {
        if ($this->is_stackmember) {

            if (($stackmemberRoles = (Stackorder::where('badgeclassID', '=', $this->badgeclassID)->first()->get_all_members_role_from_stack)) &&
                ($stackmemberBadgeclassIDs = (Stackorder::where('badgeclassID', '=', $this->badgeclassID)->first()->get_all_members_badgeclassid_from_stack))
            ) {

                $onlyMeta = $stackmemberRoles->filter(function ($item) {
                    return $item == 'meta';
                });

                $onlyMetaBadgeclassID = $stackmemberBadgeclassIDs->intersectByKeys($onlyMeta);

                //ddd($onlyPartsBadgeclassID->toArray());

                if (Assertion::whereIn('badgeclassID', $onlyMetaBadgeclassID->toArray())->where('originalIdentity', $this->originalIdentity)->get()->count() == $onlyMetaBadgeclassID->count()) {

                    return true;
                }

                //return ([$stackmemberRoles, $stackmemberBadgeclassIDs]);
            }
        }
        return false;
    }

    public function getGetRoleIfStackAttribute()
    {
        if ($this->is_stackmember) {



            if (($stackmemberRoles = (Stackorder::where('badgeclassID', '=', $this->badgeclassID)->first()->get_all_members_role_from_stack)) &&
                ($stackmemberBadgeclassIDs = (Stackorder::where('badgeclassID', '=', $this->badgeclassID)->first()->get_all_members_badgeclassid_from_stack))
            ) {


                $thisBadgeKey = $stackmemberBadgeclassIDs->search($this->badgeclassID);

                $thisBadgeRole = $stackmemberRoles->get($thisBadgeKey);


                return $thisBadgeRole;
            }
        }
        return false;
    }

    public function getGetMetaBadgeclassidIfStackAttribute()
    {

        if ($this->is_stackmember) {

            if (($stackmemberRoles = (Stackorder::where('badgeclassID', '=', $this->badgeclassID)->first()->get_all_members_role_from_stack)) &&
                ($stackmemberBadgeclassIDs = (Stackorder::where('badgeclassID', '=', $this->badgeclassID)->first()->get_all_members_badgeclassid_from_stack))
            ) {


                $thisBadgeKey = $stackmemberBadgeclassIDs->search($this->badgeclassID);

                //ddd($thisBadgeRole = $stackmemberRoles->get($thisBadgeKey));


                $onlyMeta = $stackmemberRoles->filter(function ($item) {
                    return $item == 'meta';
                });


                //ddd($onlyMeta);

                $onlyMetaBadgeclassID = $stackmemberBadgeclassIDs->intersectByKeys($onlyMeta);

                //ddd($onlyPartsBadgeclassID->toArray());            

                return ($onlyMetaBadgeclassID->first());

                /*  if (Assertion::whereIn('badgeclassID', $onlyMetaBadgeclassID->toArray())->where('originalIdentity', $this->originalIdentity)->get()->count() == $onlyMetaBadgeclassID->count()) {

                    return $onlyMetaBadgeclassID->pop();
                }*/

                //return ([$stackmemberRoles, $stackmemberBadgeclassIDs]);
            }
        }
        return false;
    }


    public function getIsBadgeclassInMetabadgeruleAttribute()
    {

        return (Badgeclass::where('badgeclassID', '=', $this->badgeclassID)->first()->is_in_metabadgerule);
    }

    public function getGetMetabadgeruleAttribute()
    {

        if ($this->is_badgeclass_in_metabadgerule) {
            return (Badgeclass::where('badgeclassID', '=', $this->badgeclassID)->first()->get_metabadgerule);
        }
        return false;
    }


    public function getIsGreenCertAttribute()
    {
        $badgeclassID = $this->badgeclassID;
        if (env('GREENCERT_ID') == $badgeclassID) {
            return true;
        }

        return false;
    }
}

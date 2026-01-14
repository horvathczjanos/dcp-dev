<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EarnerBadge extends Model
{
    use HasFactory;

    protected $fillable = ['status'];

    //protected $appends = ['badge_name'];

    public function getBadgeClassAttribute()
    {

        $backData = unserialize($this->cachedBadgeReport);

        $item = isset($backData['checkOB']['badgeClass']) ? $backData['checkOB']['badgeClass'] : '';

        return $item;
    }

    public function getBadgeClassImageAttribute()
    {
        $backData = unserialize($this->cachedBadgeReport);

        $item = isset($backData['checkOB']['badgeClass']['image']) ? $backData['checkOB']['badgeClass']['image'] : '';

        return $item;
    }

    public function getBadgeNameAttribute()
    {
        $backData = unserialize($this->cachedBadgeReport);

        $item = isset($backData['checkOB']['badgeClass']['name']) ? $backData['checkOB']['badgeClass']['name'] : '';

        return $item;
    }

    public function getBadgeTagsAttribute()
    {
        $backData = unserialize($this->cachedBadgeReport);

        $item = isset($backData['checkOB']['badgeClass']['tags']) ? $backData['checkOB']['badgeClass']['tags'] : array();

        return $item;
    }

    public function getBadgeDescriptionAttribute()
    {
        $backData = unserialize($this->cachedBadgeReport);

        $item = isset($backData['checkOB']['badgeClass']['description']) ? $backData['checkOB']['badgeClass']['description'] : array();

        return $item;
    }

    public function getBadgeIssuerNameAttribute()
    {
        $backData = unserialize($this->cachedBadgeReport);

        $item = isset($backData['checkOB']['issuer']['name']) ? $backData['checkOB']['issuer']['name'] : array();

        return $item;
    }

    public function getBadgeIssuedonAttribute()
    {
        $backData = unserialize($this->cachedBadgeReport);

        $item = isset($backData['checkOB']['issuedOn']) ? $backData['checkOB']['issuedOn'] : array();

        return $item;
    }

    public function getBadgeExpiresAtAttribute()
    {

        $backData = unserialize($this->cachedBadgeReport);

        $item = isset($backData['checkOB']['expires']) ? $backData['checkOB']['expires'] : array();


        return $item;
    }

    // Added 2022-06-21 by RG begin

    public function getBadgeIssuerUrlAttribute()
    {
        $backData = unserialize($this->cachedBadgeReport);

        $item = isset($backData['checkOB']['issuer']['url']) ? $backData['checkOB']['issuer']['url'] : array();

        return $item;
    }

    // Added 2022-06-21 by RG end

    // Added 2023-06-13 by RG begin

    public function getGetBadgeclassCriteriaAttribute()
    {
        if ($badgeclassID = $this->get_dcp_badgeclass_id) {

            $badgeclassCriteria = Badgeclass::where('badgeclassID', '=', $badgeclassID)->first()->criteria;

            return $badgeclassCriteria;
        }

        return false;
    }

    // Added 2023-06-13 by RG end

    public function getGetDcpBadgeclassIdAttribute()
    {
        if ($this->is_dcp_badge) {
            $backData = unserialize($this->cachedBadgeReport);
            $item = isset($backData['checkOB']['badge']) ? $backData['checkOB']['badge'] : '';
            $badgeclassID = str_replace(env('APP_URL') . '/badgeclasses/', '', $item);

            //ddd($badgeclassID);

            return $badgeclassID;
        } else {
            return false;
        }
    }

    public function getIsDcpBadgeAttribute()
    {

        $backData = unserialize($this->cachedBadgeReport);
        $item = isset($backData['checkOB']['badgeClass']['id']) ? $backData['checkOB']['badgeClass']['id'] : '';

        if (strpos($item, env('APP_URL')) !== false) {
            return true;
        }

        return false;
    }

    public function getIsDcpBadgeRevokedAttribute()
    {
        if ($this->is_dcp_badge) {

            $backData = unserialize($this->cachedBadgeReport);
            $tmp = explode("/", $backData['checkOB']['id']);
            $assertionID = end($tmp);
            $thisAssertion = Assertion::where('assertionID', '=', $assertionID)->first();


            /* try {
                return $thisAssertion->revoked == 'on' ? true : false;
            } catch (Exception $e) {
                ddd($thisAssertion);
            } */
            return $thisAssertion->revoked == 'on' ? true : false;
        }
        return false;
    }


    public function getDcpBadgeRevokedatAttribute()
    {
        if ($this->is_dcp_badge) {

            $backData = unserialize($this->cachedBadgeReport);
            $tmp = explode("/", $backData['checkOB']['id']);
            $assertionID = end($tmp);
            $thisAssertion = Assertion::where('assertionID', '=', $assertionID)->first();

            return $thisAssertion->revokedAt;
        }
        return false;
    }

    public function getDcpBadgeRevokedReasonAttribute()
    {
        if ($this->is_dcp_badge) {

            $backData = unserialize($this->cachedBadgeReport);
            $tmp = explode("/", $backData['checkOB']['id']);
            $assertionID = end($tmp);
            $thisAssertion = Assertion::where('assertionID', '=', $assertionID)->first();

            return $thisAssertion->revokedReason;
        }
        return false;
    }

    public function getDcpBadgeEvidenceUrlAttribute()
    {
        if ($this->is_dcp_badge) {

            switch ($this->get_dcp_badgeclass_id) {
                case env('GREENCERT_ID'):
                    return false;
                    break;

                default:
                    $backData = unserialize($this->cachedBadgeReport);
                    $tmp = explode("/", $backData['checkOB']['id']);
                    $assertionID = end($tmp);
                    $thisAssertion = Assertion::where('assertionID', '=', $assertionID)->first();



                    return $thisAssertion->earner_evidence;
                    break;
            }
        }


        return false;
    }

    public function getGetAssertionTypeAttribute()
    {
        if ($this->is_dcp_badge) {

            $backData = unserialize($this->cachedBadgeReport);
            $tmp = explode("/", $backData['checkOB']['id']);
            $assertionID = end($tmp);
            $assertionType = Assertion::where('assertionID', '=', $assertionID)->first();

            //$item = isset($backData['checkOB']['id']) ? $assertioType : '';
            //ddd($item);

            return $assertionType->assertiontype;
        }
        return false;
    }

    public function getGetAssertionTypeASAttribute()
    {

        if ($this->is_dcp_badge) {
            $assertionType = json_decode($this->get_assertion_type, true);

            $selectedTypes = [];

            foreach ($assertionType['form'] as $key => $item) {

                if ($item['selected'] == "true") {
                    $selectedTypes[] = $item['name'];
                }
            }

            return ($selectedTypes);
        }

        return false;
    }

    public function getGetMultilangpackAttribute()
    {
        if ($badgeclassID = $this->get_dcp_badgeclass_id) {

            $multilangPack = Badgeclass::where('badgeclassID', '=', $badgeclassID)->first()->multilangPack;

            return $multilangPack;
        }

        return false;
    }

    public function getGetOriginallangAttribute()
    {
        if ($badgeclassID = $this->get_dcp_badgeclass_id) {

            $originalLang = Badgeclass::where('badgeclassID', '=', $badgeclassID)->first()->originalLang;

            return $originalLang;
        }

        return false;
    }

    public function getIsStackmemberAttribute()
    {
        if ($this->is_dcp_badge) {

            if (count(Stackorder::where('badgeclassID', '=', $this->get_dcp_badgeclass_id)->get())) {
                return true;
            }
        } else {
            return false;
        }

        return false;
    }

    public function getGetStackmemberIdAttribute()
    {
        if ($this->is_dcp_badge) {

            //ddd($this->get_dcp_badgeclass_id);

            if ($item = (Stackorder::where('badgeclassID', '=', $this->get_dcp_badgeclass_id)->get()->map->only(['stackmemberID'])->toArray())[0]['stackmemberID']) {

                return $item;
            }
        }
        return false;
    }




    public function getGetDcpBadgeStackreportAttribute()
    {

        if ($this->is_stackmember) {

            $stackReport = [];

            $stackMember = Stackmember::where('stackmemberID', '=', $this->get_stackmember_id)->first();

            $stackReport['name'] = $stackMember->name;
            $stackReport['description'] = $stackMember->description;
            $stackReport['stackmemberID'] = $stackMember->stackmemberID;

            // ddd(Stackrole::where('part1', '=', '900')->get(['part1', 'relationship', 'part2'])->toArray());

            $stackReport['stackroles'] = Stackrole::where('part1', '=', $this->get_stackmember_id)->get(['part1', 'relationship', 'part2', 'stackgroupID'])->toArray();
            $holder = array();
            foreach ($stackReport['stackroles'] as $stackrole) {


                $tmpArray = array();

                if ($stackrole['relationship'] == 'partOf') {




                    $includeSideName = (Stackmember::where('stackmemberID', '=', $stackrole['part2'])->pluck('name'))[0];
                    $includeSideID = $stackrole['part2'];
                    $stackgroupID = $stackrole['stackgroupID'];

                    $message = 'This badge is part of this meta badge:' . $includeSideName . '. ';

                    $includePart['relationship'] =  'partOf';
                    $includePart['includePartName'] =  $includeSideName;
                    $includePart['includePartID'] =  $includeSideID;
                    $includePart['parts'] = array();

                    $partofs = Stackrole::where('part1', '=', $stackrole['part2'])->where('relationship', '=', 'includes')->get('part2')->toArray();



                    $newPartofs = array_unique(array_column($partofs, 'part2'));
                    $gotParts = array();

                    array_push($tmpArray, $includePart);



                    foreach ($partofs as $partof) {

                        $subArray = array();

                        $message .= '' . $partof['part2'] . ' badge is needed to get ' . $includeSideName . '. ';

                        $partID = $partof['part2'];
                        $partName = (Stackmember::where('stackmemberID', '=', $partID)->pluck('name'))[0];
                        $partIsOwned = false;
                        $partIsIdentified = false;

                        $includedBadgeclassID = (Stackorder::where('stackmemberID', '=', $partID)->where('stackgroupID', '=', $stackgroupID)
                            ->pluck('badgeclassID'))[0];
                        $includedBadgeclass = Badgeclass::where('badgeclassID', '=', $includedBadgeclassID)->first();


                        //ddd($includedBadgeclassID);

                        $subBadgeName = $includedBadgeclass->name;
                        $subBadgeImage = env('APP_URL') . '/badgeclasses/' . $includedBadgeclassID . '/image';



                        $allEarnerBadgesForUser = EarnerBadge::where('earnerID', '=', $this->earnerID)->where('status', '=', '1')->get();

                        $isMetabadgePresent = false;
                        $metaBadgeIsIdentified = false;

                        $metaBadgeclassID = (Stackorder::where('stackmemberID', '=',  $includeSideID)->where('stackgroupID', '=', $stackgroupID)
                            ->pluck('badgeclassID'))[0];
                        $metaBadgeclass = Badgeclass::where('badgeclassID', '=', $metaBadgeclassID)->first();


                        $metaBadgeName = $metaBadgeclass->name;
                        $metaBadgeImage = env('APP_URL') . '/badgeclasses/' . $metaBadgeclassID  . '/image';
                        foreach ($allEarnerBadgesForUser as $earnerBadge) {

                            //ddd($earnerBadge->is_stackmember);

                            //dd($earnerBadge->get_dcp_badgeclass_id);

                            if ($earnerBadge->is_stackmember && $partof['part2'] == $earnerBadge->get_stackmember_id) {
                                $message .= 'You have got it!';
                                $gotParts[] = $partof['part2'];
                                $partIsOwned = true;



                                if ($earnerBadge->is_recipient_valid) {
                                    $partIsIdentified = true;
                                }
                            }
                            if ($earnerBadge->is_stackmember && $includeSideID == $earnerBadge->get_stackmember_id) {
                                $isMetabadgePresent = true;
                                if ($earnerBadge->is_recipient_valid) {
                                    $metaBadgeIsIdentified = true;
                                }
                            }
                        }
                        array_push($subArray, $partID, $partName, $partIsOwned, $partIsIdentified, $subBadgeName, $subBadgeImage);
                        array_push($tmpArray[0]['parts'], $subArray);
                    }


                    $isComplete = false;
                    if ($gotParts == $newPartofs) {
                        $message .= ' You have all parts for the aggregator badge!';
                        $isComplete = true;
                    }
                    $tmpArray[0]['metaBadge'] = [$isComplete, $isMetabadgePresent, $metaBadgeIsIdentified, $metaBadgeName, $metaBadgeImage];
                }


                if ($stackrole['relationship'] == 'includes') {
                    $includePart['relationship'] =  'includes';
                    $includePart['includedPartName'] =  (Stackmember::where('stackmemberID', '=', $stackrole['part2'])->pluck('name'))[0];
                    $includePart['includedPartID'] =  $stackrole['part2'];
                    $includePart['stackgroupID'] = $stackrole['stackgroupID'];
                    $allEarnerBadgesForUser = EarnerBadge::where('earnerID', '=', $this->earnerID)->where('status', '=', '1')->get();
                    $isSubbadgePresent = false;
                    $subBadgeIsIdentified = false;

                    $includedBadgeclassID = (Stackorder::where('stackmemberID', '=', $includePart['includedPartID'])->where('stackgroupID', '=', $includePart['stackgroupID'])
                        ->pluck('badgeclassID'))[0];

                    $includedBadgeclass = Badgeclass::where('badgeclassID', '=', $includedBadgeclassID)->first();

                    $subBadgeName = $includedBadgeclass->name;
                    $subBadgeImage = env('APP_URL') . '/badgeclasses/' . $includedBadgeclassID . '/image';
                    foreach ($allEarnerBadgesForUser as $earnerBadge) {
                        if ($earnerBadge->is_stackmember && $includePart['includedPartID'] == $earnerBadge->get_stackmember_id) {

                            $isSubbadgePresent = true;

                            if ($earnerBadge->is_recipient_valid) {
                                $subBadgeIsIdentified = true;
                            }
                        }
                    }


                    array_push($tmpArray, $includePart, $isSubbadgePresent, $subBadgeIsIdentified, $subBadgeName, $subBadgeImage);
                }

                array_push($holder, $tmpArray);

                //$stackReport['stackmessage'] = $message;
            }

            $stackReport['stackroles'] = $holder;

            return $stackReport;
        }

        return false;
    }

    public function getIsRecipientValidAttribute()
    {

        $backData = unserialize($this->cachedBadgeReport);
        $recipientBlock = isset($backData['checkOB']['recipient']) ? $backData['checkOB']['recipient'] : '';

        if ($recipientBlock['hashed']) {

            $identity = "sha256$" . hash('sha256', ($this->recipientIdentity) . ($recipientBlock['salt']));

            if ($identity == $recipientBlock['identity']) {
                return true;
            }
        }

        return false;
    }

    public function getFiletypeOfBadgeAttribute()
    {
        $backData = unserialize($this->cachedBadgeReport);

        return $backData['fileType'];
    }


    public function getIsDCPMetabadgeAttribute()
    {
        
        if ($this->is_dcp_badge) {
            
            $badgeclassID = $this->get_dcp_badgeclass_id;
            
            return (Badgeclass::where('badgeclassID', '=', $badgeclassID)->first()->is_in_metabadgerule);
        }
        return false;
    }

    public function getGetMetabadgeruleAttribute()
    {

        if ($this->is_dcp_badge) {


            $badgeclassID = $this->get_dcp_badgeclass_id;
            return (Badgeclass::where('badgeclassID', '=', $badgeclassID)->first()->get_metabadgerule);
        }
        return false;
    }

}

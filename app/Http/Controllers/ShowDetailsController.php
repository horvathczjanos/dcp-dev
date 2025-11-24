<?php

namespace App\Http\Controllers;

use App\Models\Assertion;
use App\Models\Badgeclass;
use App\Models\Badgeimage;
use App\Models\EarnerBadge;
use App\Models\VerifierBadge;
use App\Models\Issuer;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;



class ShowDetailsController extends Controller
{

    public function filterSpecialChars($inputString)
    {
        return (preg_replace('/\\\r\\\n|\\\r|\\\n\\\r|\\\n/m', ' * ', $inputString));
    }



    public function showOrganization($id)
    {


        if ($issuer = Issuer::where('issuerID', $id)->get()->toArray()) {
            return response()->json(json_decode('{
                "@context": "https://w3id.org/openbadges/v2",
                "type": "Profile",
                "id": "' . env('APP_URL') . '/organizations/' . $issuer[0]['issuerID'] . '",
                "name": "' . $issuer[0]['name'] . '",
                "image": "' . env('APP_URL') . '/organizations/' . $issuer[0]['issuerID'] . "/image" . '",
                "url": "' . $issuer[0]['url'] . '",
                "email": "' . $issuer[0]['email'] . '"
            }'));
        }



        return ('No such organization!');
    }


    public function showImage($id)
    {

        if ($issuer = Issuer::where('issuerID', $id)->get()->toArray()) {

            $headers = ['Content-Type: application/png'];

            return Storage::response('public/images/' . $issuer[0]['logofile'], 'image.png', $headers);
        }

        return ('No such image!');
    }


    public function showAssertion($id)
    {

        if ($assertion = Assertion::where('assertionID', $id)->get()->toArray()) {


            $expireString = function () use ($assertion) {

                if ($assertion[0]['neverexpires'] == 'off') {

                    return ('"expires": "' . $assertion[0]['expiresAt'] . '",');
                } else {
                    return ('');
                }
            };


            if ($assertion[0]['revoked'] == 'on') {
                $statusCode = 410;
            } else {
                $statusCode = 200;
            }

            $revokedStrings = function () use ($assertion) {

                if ($assertion[0]['revoked'] == 'on') {

                    //ddd($assertion[0]['revokedReason']);
                    return (array('"revoked": true,', '"revocationReason": "' . $assertion[0]['revokedReason'] . '",'));
                } else {


                    return (array('', ''));
                }
            };

            /*     return response()->json(json_decode(
            '{
                "@context": "https://w3id.org/openbadges/v2",
                "type": "Assertion",
                "id": "' . env('APP_URL') . '/assertions/' . $assertion[0]['assertionID'] . '",
                "recipient": {
                    "hashed": true,
                    "type": "email",
                    "identity": "' . $assertion[0]['identity'] . '",
                    "salt": "' . $assertion[0]['salt'] . '"
                },
                "image": {
                    "id": "' . env('APP_URL') . '/badgeclasses/' . $assertion[0]['badgeclassID'] . '/image"
                },
                "evidence": "' . env('APP_URL') . '/evidences/' . $assertion[0]['assertionID'] . '",
                "revoked": true,
                "revocationReason": "Temporarily revoked",
                "issuedOn": "' . $assertion[0]['issuedOn'] . '",' .
                    $expireString($assertion) .              
                '"badge": "' . env('APP_URL') . '/badgeclasses/' . $assertion[0]['badgeclassID'] . '",
                "verification": {
                    "type": "HostedBadge"
                }
            }'

               
            ), 410); */



            return response()->json(json_decode(
                '{
                        "@context": "https://w3id.org/openbadges/v2",
                        "type": "Assertion",
                        "id": "' . env('APP_URL') . '/assertions/' . $assertion[0]['assertionID'] . '",
                        "recipient": {
                            "hashed": true,
                            "type": "email",
                            "identity": "' . $assertion[0]['identity'] . '",
                            "salt": "' . $assertion[0]['salt'] . '"
                        },
                        "image": {
                            "id": "' . env('APP_URL') . '/badgeclasses/' . $assertion[0]['badgeclassID'] . '/image"
                        },
                        "evidence": "' . env('APP_URL') . '/evidences/' . $assertion[0]['assertionID'] . '",
                        "issuedOn": "' . $assertion[0]['issuedOn'] . '",' .
                    $expireString($assertion) .
                    $revokedStrings($assertion)[0] .
                    $revokedStrings($assertion)[1] .
                    '"badge": "' . env('APP_URL') . '/badgeclasses/' . $assertion[0]['badgeclassID'] . '",
                        "verification": {
                            "type": "HostedBadge"
                        }
                    }'
            ), $statusCode);
        }

        //  "issuedOn": "2021-09-01T11:11:52.487Z",

        return ('No such assertion!');
    }

    public function showBadgeClass($id)
    {

        // ddd($id);

        if ($badgeclass = Badgeclass::where('badgeclassID', $id)->get()->toArray()) {

            //   "tags": ["BME", "MTI", "Open Badge", "DIScoPLAYER", "demo", "DISCO VET", "2021"],
            //ddd($badgeclass);

            //ddd($badgeclass);

            /*           ddd('{
    "@context": "https://w3id.org/openbadges/v2",
    "type": "BadgeClass",
    "id": "' . env('APP_URL') . '/badgeclasses/' . $badgeclass[0]['badgeclassID'] . '",
    "name": "' . $badgeclass[0]['name'] . '",
    "description": "' . $badgeclass[0]['description'] . '",
    "image": "' . env('APP_URL') . '/badgeclasses/' . $badgeclass[0]['badgeclassID'] . '/image",
    "criteria": "' . env('APP_URL') . '/criteria/' . $badgeclass[0]['badgeclassID'] . '",
    "tags": ' . $badgeclass[0]['tags'] . ',
    "issuer": "' . env('APP_URL') . '/organizations/' . $badgeclass[0]['issuerID'] . '"
}');

            ddd(
                json_decode(
                    '{
                            "@context": "https://w3id.org/openbadges/v2",
                            "type": "BadgeClass",
                            "id": "' . env('APP_URL') . '/badgeclasses/' . $badgeclass[0]['badgeclassID'] . '",
                            "name": "' . $badgeclass[0]['name'] . '",
                            "description": "' . $badgeclass[0]['description'] . '",
                            "image": "' . env('APP_URL') . '/badgeclasses/' . $badgeclass[0]['badgeclassID'] . '/image",
                            "criteria": "' . env('APP_URL') . '/criteria/' . $badgeclass[0]['badgeclassID'] . '",
                            "tags": ' . $badgeclass[0]['tags'] . ',
                            "issuer": "' . env('APP_URL') . '/organizations/' . $badgeclass[0]['issuerID'] . '"
                        }'
                )
            ); */


            return response()->json(json_decode(
                '{
                        "@context": "https://w3id.org/openbadges/v2",
                        "type": "BadgeClass",
                        "id": "' . env('APP_URL') . '/badgeclasses/' . $badgeclass[0]['badgeclassID'] . '",
                        "name": "' . $badgeclass[0]['name'] . '",
                        "description": "' . $badgeclass[0]['description'] . '",
                        "image": "' . env('APP_URL') . '/badgeclasses/' . $badgeclass[0]['badgeclassID'] . '/image",
                        "criteria": "' . env('APP_URL') . '/criteria/' . $badgeclass[0]['badgeclassID'] . '",
                        "tags": ' . $badgeclass[0]['tags'] . ',
                        "issuer": "' . env('APP_URL') . '/organizations/' . $badgeclass[0]['issuerID'] . '"
                    }'
            ));
        }

        return ('No such assertion!');
    }

    public function showBadgeClassImage($id)
    {


        if ($badgeimage = Badgeimage::where('badgeclassID', $id)->get()->toArray()) {

            $headers = ['Content-Type: application/png'];

            return Storage::response($badgeimage[0]['filepath'], $badgeimage[0]['outputfilename'], $headers);
        }


        return ('No such badge image!');
    }

    public function showAssertionBadge($id)
    {


        if ($digitalbadge = Assertion::where('assertionID', $id)->get()->toArray()) {

            $headers = ['Content-Type: application/png'];

            return Storage::response($digitalbadge[0]['digitalbadgepath'], 'digitalBadge.png', $headers);
        }




        return ('No such digital badge!');
    }


    public function showEvidence($id)
    {

        if ($assertion = Assertion::where('assertionID', $id)->first()) {


            //ddd($assertion->earner_evidence);

            if (!is_null($evidenceURL = $assertion->earner_evidence)) {

                return ($evidenceURL);
            } else {
                return ('This is an evidence for ID ' . $id);
            }
        }


        return ('No such evidence!');
    }

    public function showCriteria($id)
    {

        if ($badgeclass = Badgeclass::where('badgeclassID', $id)->get()->toArray()) {

            //ddd($badgeclass);

            return ($badgeclass[0]['criteria']);
        }

        return ('No such criteria!');
    }

    public function showEarnerBadge($id)
    {
        if ($earnerbadgeimage = EarnerBadge::where('earnerbadgeID', $id)->get()->toArray()) {

            $headers = ['Content-Type: application/png'];

            return Storage::response($earnerbadgeimage[0]['earnerbadgepath'], 'mybadge_' . $earnerbadgeimage[0]['earnerbadgecontenthash'] . '.png', $headers);
        }


        return ('No such badge image!');
    }

    public function showVerifierBadge($id)
    {
        if ($verifierbadgeimage = VerifierBadge::where('verifierbadgeID', $id)->get()->toArray()) {

            $headers = ['Content-Type: application/png'];

            return Storage::response($verifierbadgeimage[0]['verifierbadgepath'], 'badge_' . $verifierbadgeimage[0]['verifierbadgecontenthash'] . '.png', $headers);
        }


        return ('No such badge image!');
    }
}

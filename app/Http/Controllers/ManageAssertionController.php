<?php

namespace App\Http\Controllers;

use App\DCP\LogSession;
use App\Mail\BadgeMail;
use App\Models\Assertion;
use App\Models\Badgeclass;
use App\Models\Badgeimage;
use App\Models\Issuer;
use App\Models\Preassertion;
use App\Models\Stackorder;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

use App\DCP\SimpleXLSX;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class ManageAssertionController extends Controller
{



    public function showAllAssertions_old20260908()
    {
        $issuerID = session()->get('issuerID');
        $badgeclassIDs = Badgeclass::select('badgeclassID')->where('status', '=', '1')->where('issuerID', $issuerID)->get()->toArray();


        return view('allAssertions')
            ->with('numberofassertions', Assertion::whereIn('badgeclassID', $badgeclassIDs)->count())
            ->with('assertionlist', Assertion::whereIn('badgeclassID', $badgeclassIDs)->orderBy('created_at', 'DESC')->get())
            ->with('issuerID', $issuerID);
    }

    public function showAllAssertions()
    {
        $issuerID = session()->get('issuerID');

        $badgeclassIDs = Badgeclass::where('status', '=', '1')->where('issuerID', '=', $issuerID)->pluck('badgeclassID');
        $numberofassertions = Assertion::whereIn('badgeclassID', $badgeclassIDs)->count();

        return view('allAssertions')
            ->with('numberofassertions', $numberofassertions)
            ->with('issuerID', $issuerID);
    }

    public function loadIssuerAssertionsInfinite(Request $request)
    {

        $issuerID = session()->get('issuerID');

        if (!$issuerID) {

            return response()->json(
                [
                    'error' =>
                    'Issuer session not found.'
                ],
                403
            );
        }

        $badgeclassIDs = Badgeclass::where('status', '=', '1')->where('issuerID', '=', $issuerID)->pluck('badgeclassID');

        $advancedIssuerConfig =
            json_decode(
                env('ADVANCED_ISSUERS'),
                true
            );

        $advancedIssuers = $advancedIssuerConfig['advaced_issuers'] ?? [];

        $isAdvancedIssuer = in_array($issuerID, $advancedIssuers, true);

        $assertions = Assertion::whereIn('badgeclassID', $badgeclassIDs)->select(
            [
                'assertionID',
                'badgeclassID',
                'issuedOn',
                'originalIdentity',
                'revoked',
                'created_at',
            ]
        )
            ->orderByDesc('created_at')
            ->orderByDesc('assertionID')
            ->cursorPaginate(30);



        $html =
            view(
                'parts.issuerAssertionInfiniteRows',
                [
                    'assertions' =>
                    $assertions,

                    'issuerID' =>
                    $issuerID,

                    'isAdvancedIssuer' =>
                    $isAdvancedIssuer,
                ]
            )
            ->render();

        return response()->json(
            [
                'html' =>
                $html,

                'next_url' =>
                $assertions->hasMorePages()
                    ? $assertions->nextPageUrl()
                    : null,
            ]
        );
    }



    public function showAllPreAssertion()
    {

        $issuerID = session()->get('issuerID');

        //ddd(Preassertion::first());

        $preassertions = Preassertion::where('issuerID', '=', $issuerID)->where('status', '=', '1')->orderBy('created_at', 'DESC')->get();

        return view('issuerShowPreAssertions')
            ->with('numberofpreassertions', $preassertions->count())
            ->with('preassertionlist', $preassertions);

        /* $badgeclassIDs = Badgeclass::select('badgeclassID')->where('issuerID', $issuerID)->get()->toArray();

        return view('issuerShowPreAssertions')
            ->with('numberofassertions', Assertion::whereIn('badgeclassID', $badgeclassIDs)->count())
            ->with('assertionlist', Assertion::whereIn('badgeclassID', $badgeclassIDs)->get()); */
    }

    public function showAssertionInfo($id)
    {

        $thisAssertion = Assertion::where('AssertionID', '=', $id)->first();


        if ($thisAssertion->count()) {

            $badgeclassID = $thisAssertion->badgeclassID;

            $thisBadgeclass = Badgeclass::where('badgeclassID', '=', $badgeclassID)->first();



            $badgeImage = Storage::get(substr((Badgeimage::where('badgeclassID', '=', $badgeclassID)->first('filepath')->filepath), 1));
            $inStack = Stackorder::where('badgeclassID', '=', $badgeclassID)->get();


            return view('issuerAssertionInfopage')
                ->with('badgeImage', $badgeImage)
                ->with('badgeName', $thisBadgeclass->name)
                ->with('badgeDescription', $thisBadgeclass->description)
                ->with('thisBadgeClass', $thisBadgeclass)
                ->with('inStack', $inStack)
                ->with('recipientType', $thisAssertion->recipientType)
                ->with('awardedTo', $thisAssertion->originalIdentity)
                ->with('issuedOn', $thisAssertion->issuedOn)
                ->with('assertionID', $id)
                ->with('badgeclassID', $badgeclassID)
                ->with('criteria', $thisBadgeclass->criteria)
                ->with('thisAssertion', $thisAssertion);
        }

        /* $thisBadgeClass = Badgeclass::where('badgeclassID', '=', $id)->where('status', '=', '1')->first();
        if ($thisBadgeClass->count()) {


            $badgeImage = Storage::get(substr((Badgeimage::where('badgeclassID', '=', $id)->first('filepath')->filepath), 1));


            $inStack = Stackorder::where('badgeclassID', '=', $id)->get();


            return view('issuerAssertionInfopage')
                ->with('badgeImage', $badgeImage)
                ->with('badgeName', $thisBadgeClass->badge_name)
                ->with('badgeDescription', $thisBadgeClass->badge_description)
                ->with('thisBadgeClass', $thisBadgeClass)
                ->with('inStack', $inStack);
        } */

        return redirect('adminMain');
    }

    public function beginCreateNewAssertion()
    {

        $issuerID = session()->get('issuerID');
        $badgeclassIDs = Badgeclass::select('badgeclassID')->where('issuerID', $issuerID)->where('status', '=', '1')->get()->toArray();


        return view('newAssertion')
            ->with('numberofbadgeclasses', Badgeclass::select('badgeclassID')->where('status', '=', '1')->where('issuerID', $issuerID)->count())
            ->with('badgeclasslist', Badgeclass::where('issuerID', '=', $issuerID)->where('status', '=', '1')->get());
    }

    public function beginCreatePreAssertion()
    {

        session()->put('sessionAction', 'create');

        return view('issuerNewPreAssertion')
            ->with('badgeclasslist', Badgeclass::where('issuerID', '=', session()->get('issuerID'))->where('status', '=', '1')->orderBy('created_at', 'DESC')->get())
            ->with('testValue', 'Szia Világ!');
    }

    public function engineGreencertPreAssertion($contents)
    {

        //ddd($contents);

        $nowTime = time();

        $coursetableHeader = ['Tantárgykód', 'Tantárgy címe', 'Kreditérték', 'Érdemjegy'];



        try {

            $allPreAssertioID = [];


            foreach ($contents as $content) {


                $preassertion = new Preassertion;


                $preassertion->recorderID = session()->get('userID');
                $preassertion->modifierID = '0';

                $preassertion->badgeclassID = $content['badgeclassID'];

                $preassertion->issuerID = session()->get('issuerID');

                //ddd($preassertion->badgeclassID);

                $assertionType = json_decode(Badgeclass::where("badgeclassID", '=', $preassertion->badgeclassID)->first()->type, true);


                //ddd($assertionType);

                $preassertion->recipientType = 'email';
                $preassertion->originalIdentity = $content['email'];


                $issuedDate = strtotime($content['_date_issued_en']);
                $preassertion->issuedOn = (date('Y-m-d\T08:00:00\Z', $issuedDate));



                $preassertion->assertiontype = json_encode($assertionType, true);

                $preassertion->released = date("Y-m-d\TH:i:s\Z", $nowTime);

                $preassertion->neverexpires = 'on';

                $preassertion->status = '1';


                // Név
                $assertionType['form'][0]['fields'][0]['load'][0]['content'] = $content['Név'];
                $assertionType['form'][0]['fields'][0]['load'][1]['content'] = $content['Név'];

                // Neptun kód
                $assertionType['form'][0]['fields'][1]['load'] = $content['Neptun kód'];

                // Sorszám
                $assertionType['form'][0]['fields'][2]['load'] = $content['Sorszám'];

                // Születési dátum
                $assertionType['form'][0]['fields'][3]['load'] = $content['Születési dátum']; // ????

                // Születési hely 
                $assertionType['form'][0]['fields'][4]['load'][0]['content'] = $content['Születési hely angolul'];
                $assertionType['form'][0]['fields'][4]['load'][1]['content'] = $content['Születési hely magyarul'];

                // Születési hely jellege
                $assertionType['form'][0]['fields'][5]['load'][0]['content'] = $content['Születési hely jellege angolul'];
                $assertionType['form'][0]['fields'][5]['load'][1]['content'] = $content['Születési hely jellege magyarul, ragozva'];

                // Születési térség
                $assertionType['form'][0]['fields'][6]['load'][0]['content'] = $content['Születési térség angolul'];
                $assertionType['form'][0]['fields'][6]['load'][1]['content'] = $content['Születési térség magyarul'];

                // Születési térség jellege
                $assertionType['form'][0]['fields'][7]['load'][0]['content'] = $content['Születési térség jellege angolul'];
                $assertionType['form'][0]['fields'][7]['load'][1]['content'] = $content['Születési térség jellege magyarul, ragozva'];

                // Születési ország
                $assertionType['form'][0]['fields'][8]['load'][0]['content'] = $content['Születési ország angolul'];
                $assertionType['form'][0]['fields'][8]['load'][1]['content'] = $content['Születési ország magyarul, ragozva'];

                // 01 tantárgy és adatai
                $assertionType['form'][0]['fields'][9]['load'] = [$coursetableHeader, [$content['_c1code'], $content['_c1name'], $content['_c1cred'], $content['_c1res']]];

                // 01 tantárgy TAD hivatkozás
                $assertionType['form'][0]['fields'][10]['load'] = $content['_c1sublink'];


                // 02 tantárgy és adatai
                $assertionType['form'][0]['fields'][11]['load'] = [$coursetableHeader, [$content['_c2code'], $content['_c2name'], $content['_c2cred'], $content['_c2res']]];

                // 02 tantárgy TAD hivatkozás
                $assertionType['form'][0]['fields'][12]['load'] = $content['_c2sublink'];


                // 03 tantárgy és adatai
                $assertionType['form'][0]['fields'][13]['load'] = [$coursetableHeader, [$content['_c3code'], $content['_c3name'], $content['_c3cred'], $content['_c3res']]];

                // 03 tantárgy TAD hivatkozás
                $assertionType['form'][0]['fields'][14]['load'] = $content['_c3sublink'];



                // 04 tantárgy és adatai
                $assertionType['form'][0]['fields'][15]['load'] = [$coursetableHeader, [$content['_c4code'], $content['_c4name'], $content['_c4cred'], $content['_c4res']]];

                // 04 tantárgy TAD hivatkozás
                $assertionType['form'][0]['fields'][16]['load'] = $content['_c4sublink'];



                // 05 tantárgy és adatai
                $assertionType['form'][0]['fields'][17]['load'] = [$coursetableHeader, [$content['_c5code'], $content['_c5name'], $content['_c5cred'], $content['_c5res']]];

                // 05 tantárgy TAD hivatkozás
                $assertionType['form'][0]['fields'][18]['load'] = $content['_c5sublink'];


                // 06 tantárgy és adatai
                $assertionType['form'][0]['fields'][19]['load'] = [$coursetableHeader, [$content['_c6code'], $content['_c6name'], $content['_c6cred'], $content['_c6res']]];


                // 06 tantárgy TAD hivatkozás
                $assertionType['form'][0]['fields'][20]['load'] = $content['_c6sublink'];


                // 20240924 - új mezőkkel bővítünk


                // 07 tantárgy és adatai
                $assertionType['form'][0]['fields'][21]['load'] = [$coursetableHeader, [$content['_c7code'], $content['_c7name'], $content['_c7cred'], $content['_c7res']]];

                // 07 tantárgy TAD hivatkozás
                $assertionType['form'][0]['fields'][22]['load'] = $content['_c7sublink'];


                // 08 tantárgy és adatai
                $assertionType['form'][0]['fields'][23]['load'] = [$coursetableHeader, [$content['_c8code'], $content['_c8name'], $content['_c8cred'], $content['_c8res']]];

                // 08 tantárgy TAD hivatkozás
                $assertionType['form'][0]['fields'][24]['load'] = $content['_c8sublink'];



                // Keltezés
                $assertionType['form'][0]['fields'][25]['load'][0]['content'] = $content['_date_issued_en'];
                $assertionType['form'][0]['fields'][25]['load'][1]['content'] = $content['_date_issued_hu'];

                // Elektronikus levélcím (e-mail)
                $assertionType['form'][0]['fields'][26]['load'] = $content['email'];


                //ddd($assertionType);

                $preassertion->assertiontype = json_encode($assertionType, true);

                $preassertion->released = date("Y-m-d\TH:i:s\Z", $nowTime);

                $preassertion->neverexpires = 'on';

                $preassertion->status = '1';

                //ddd($assertionType);

                $preassertion->save();

                $allPreAssertioID[] = $preassertion->id;
            }
        } catch (Exception $e) {
            ddd($e);
            ddd($assertionType);
            return ($e);
        }
    }

    public function enginePreAssertion($contents)
    {
        $nowTime = time();



        try {

            $allPreAssertioID = [];

            foreach ($contents as $content) {
                $preassertion = new Preassertion;

                $preassertion->recorderID = session()->get('userID');
                $preassertion->modifierID = '0';

                $preassertion->badgeclassID = $content[3];

                $preassertion->issuerID = session()->get('issuerID');

                $assertionType = json_decode(Badgeclass::where("badgeclassID", '=', $preassertion->badgeclassID)->first()->type, true);

                //ddd($contents);

                $preassertion->recipientType = 'email';
                $preassertion->originalIdentity = $content[2];
                //ddd(is_null($content[4]));
                // $preassertion->realname = $content[0];
                if (!isset($content[4])) {
                    $preassertion->issuedOn = date("Y-m-d\TH:i:s\Z", $nowTime);
                } else {
                    $preassertion->issuedOn = $content[4];
                }
                $assertionType['form'][0]['fields'][0]['content'] = $content[0];
                $assertionType['form'][0]['fields'][1]['content'] = $content[1];


                if (isset($content[5])) {
                    $assertionType['form'][0]['fields'][13]['content'] = $content[5];
                }

                if (isset($content[6])) {
                    $assertionType['form'][0]['fields'][14]['content'] = $content[6];
                }

                // Earner Description
                if (isset($content[7])) {
                    $assertionType['form'][0]['fields'][10]['content'] = $content[7];
                }

                // Earning criteria
                if (isset($content[8])) {
                    $assertionType['form'][0]['fields'][17]['content'] = $content[8];
                }

                // Evidence
                if (isset($content[9])) {
                    $assertionType['form'][0]['fields'][18]['content'] = $content[9];
                }

                $preassertion->assertiontype = json_encode($assertionType, true);

                $preassertion->released = date("Y-m-d\TH:i:s\Z", $nowTime);

                $preassertion->neverexpires = 'on';

                $preassertion->status = '1';


                //ddd($preassertion); 

                $preassertion->save();

                $allPreAssertioID[] = $preassertion->id;
            }

            return ($allPreAssertioID);
        } catch (Exception $e) {

            return ($e);
        }
    }

    public function recordPreAssertion(Request $request)
    {

        //ddd(session()->all());

        //ddd($request->get('neverExpires'));
        //ddd($request->all());

        /* if (!$request->has('badgeclass')) {
            return back()
                ->withErrors('You have not selected a badgeclass!');
        }
*/
        $nowTime = time();

        if (session()->get('sessionAction') == 'create') {

            $preassertion = new Preassertion;

            $preassertion->recorderID = session()->get('userID');
            $preassertion->modifierID = '0';

            $preassertion->badgeclassID = $request->get('badgeclass');

            $preassertion->issuerID = session()->get('issuerID');

            $assertionType = json_decode(Badgeclass::where("badgeclassID", '=', $preassertion->badgeclassID)->first()->type, true);
        } else if (session()->get('sessionAction') == 'edit') {

            $preassertion = Preassertion::where("id", '=', session()->get('editedPreAssertionID'))->first();

            $assertionType = json_decode(Preassertion::where("id", '=', session()->get('editedPreAssertionID'))->first()->assertiontype, true);

            $preassertion->modifierID = session()->get('userID');
        }





        //$preassertion->realname = $request->get('realname');
        $preassertion->recipientType = 'email';


        $isEmailValid = Validator::make(['Email' => $request->get('email')], [
            'Email' => 'email:rfc,dns'
        ]);

        if ($isEmailValid->fails()) {
            session()->forget('sessionAction');
            return redirect('backtocompany');
        }


        $preassertion->originalIdentity = $request->get('email');
        $preassertion->issuedOn = date_create($request->get('date'))->format('c');


        if (!is_null($request->get('neverExpires'))) {
            $preassertion->neverexpires = 'on';
        } else {
            $preassertion->neverexpires = 'off';
        }


        $preassertion->expiresAt = date_create($request->get('date_2'))->format('c');



        //ddd(Badgeclass::where("badgeclassID", '=', $preassertion->badgeclassID)->first()->type);
        //ddd($assertionType);

        foreach ($assertionType['form'] as &$form) {
            foreach ($form['fields'] as &$field) {
                foreach ($request->all() as $inputfield => $value) {

                    if ($field['id'] == $inputfield) {

                        $field['content'] = $value;
                    }
                }
            }
        }


        $assertionType['form'][0]['fields'][0]['content'] = $request->get('firstname');
        $assertionType['form'][0]['fields'][1]['content'] = $request->get('lastname');

        //ddd($assertionType);


        $preassertion->assertiontype = json_encode($assertionType, true);

        $preassertion->released = date("Y-m-d\TH:i:s\Z", $nowTime);

        $preassertion->status = '1';

        $preassertion->save();

        //ddd(session()->all());

        session()->forget('sessionAction');

        return redirect('backtocompany');
    }

    public function deletePreAssertion($id)
    {

        if ($thisPreAssertion = Preassertion::where('id', '=', $id)->first()) {

            $thisPreAssertion->status = '0';

            $thisPreAssertion->save();

            return redirect('showPreAssertions');
        }


        return redirect('backtocompany');
    }


    public function editPreAssertion($id)
    {

        if ($thisPreAssertion = Preassertion::where('id', '=', $id)->first()) {

            //ddd(substr($thisPreAssertion['issuedOn'], 0, 14));

            session()->put('sessionAction', 'edit');
            session()->put('editedPreAssertionID', $id);




            return view('issuerEditPreAssertion')
                ->with('thisPreAssertion', $thisPreAssertion)
                ->with('badgeclasslist', Badgeclass::where('issuerID', '=', session()->get('issuerID'))->get());
        }

        return redirect('backtocompany');
    }

    public function awardPreAssertionEngine($id)
    {

        if ($thisPreAssertion = Preassertion::where('id', '=', $id)->first()) {

            $nowTime = time();

            $salt = hash('sha256', random_bytes(128) . $nowTime);

            $identity = "sha256$" . hash('sha256', ($thisPreAssertion->originalIdentity) . ($salt));


            $assertion = new Assertion;

            $assertion->assertionID = hash('sha256', $nowTime . random_bytes(128));



            $assertion->badgeclassID = $thisPreAssertion->badgeclassID;

            $assertion->salt = $salt;

            $assertion->identity = $identity;

            $assertion->originalIdentity = $thisPreAssertion->originalIdentity;

            //$assertion->realname = $thisPreAssertion->realname;

            $assertion->recipientType = 'email';

            $assertion->issuedOn = $thisPreAssertion->issuedOn;


            $assertion->neverexpires = $thisPreAssertion->neverexpires;

            $assertion->expiresAt = $thisPreAssertion->expiresAt;

            $expireString = function ($assertion2) use ($assertion) {

                if ($assertion2->neverexpires == 'off') {

                    return ('"expires": "' . $assertion->expiresAt . '",');
                } else {
                    return ('');
                }
            };


            $badgeclassImagePath = Badgeimage::where('badgeclassID',  $assertion->badgeclassID)->get('filepath')->toArray();




            //ddd($badgeclassImagePath);

            $assertionJSON = json_decode(
                '{
            "@context": "https://w3id.org/openbadges/v2",
            "type": "Assertion",
            "id": "' . env('APP_URL') . '/assertions/' . $assertion->assertionID . '",
            "recipient": {
                "hashed": true,
                "type": "' . $assertion->recipientType . '",
                "identity": "' .  $assertion->identity . '",
                "salt": "' . $assertion->salt . '"
            },
            "image": {
                "id": "' . env('APP_URL') . '/badgeclasses/' . $assertion->badgeclassID . '/image"
            },
            "evidence": "' . env('APP_URL') . '/evidences/' . $assertion->assertionID . '",
            "issuedOn": "' . $assertion->issuedOn . '",' .
                    $expireString($assertion) . '
            "badge": "' . env('APP_URL') . '/badgeclasses/' . $assertion->badgeclassID . '",
            "verification": {
                "type": "HostedBadge"
            }
            }'
            );

            ///ddd($assertionJSON);

            $outputFilename =  $assertion->assertionID . '_badge.png';
            $assertion->digitalbadgepath = 'public/badges/' . $outputFilename;

            $thisBadgeclass = Badgeclass::where('badgeclassID', '=', $assertion->badgeclassID)->first();

            $assertion->assertiontype = $thisPreAssertion->assertiontype;


            if ($assertionBadgePath = $this->createAssertionBadge($assertionJSON, $outputFilename, $badgeclassImagePath[0]['filepath'])) {


                $assertion->save();
                $thisPreAssertion->status = '2';
                $thisPreAssertion->save();
            }




            return ([$assertionBadgePath, $assertion->assertionID]);
        } else {
            return false;
        }
    }

    public function awardPreAssertion($id)
    {
        if ($thisPreAssertion = Preassertion::where('id', '=', $id)->first()) {

            if ($result = $this->awardPreAssertionEngine($id)) {

                $nowAssertion = Assertion::where('assertionID', '=', $result[1])->first();


                if ($nowAssertion->is_stackmember && $nowAssertion->are_all_parts_if_stack && !$nowAssertion->is_metabadge_awarded_if_stack) {
                    // ddd("Már csak a metabadge hiányzik!");

                    $contents = [];

                    $subContent = [];
                    $subContent[] = $nowAssertion->recipient_last_name;
                    $subContent[] = $nowAssertion->recipient_first_name;
                    $subContent[] = $nowAssertion->originalIdentity;
                    $subContent[] = $nowAssertion->get_meta_badgeclassid_if_stack;
                    $contents[] = $subContent;

                    $tmpPreAssertionID = (new ManageAssertionController)->enginePreAssertion($contents);

                    $tmpAssertionID = (new ManageAssertionController)->awardPreAssertionEngine($tmpPreAssertionID[0]);
                }
            }


            return redirect('showAssertions');

            /*     $nowTime = time();

            $salt = hash('sha256', random_bytes(128) . $nowTime);

            $identity = "sha256$" . hash('sha256', ($thisPreAssertion->originalIdentity) . ($salt));


            $assertion = new Assertion;

            $assertion->assertionID = hash('sha256', $nowTime . random_bytes(128));



            $assertion->badgeclassID = $thisPreAssertion->badgeclassID;

            $assertion->salt = $salt;

            $assertion->identity = $identity;

            $assertion->originalIdentity = $thisPreAssertion->originalIdentity;

            //$assertion->realname = $thisPreAssertion->realname;

            $assertion->recipientType = 'email';

            $assertion->issuedOn = $thisPreAssertion->issuedOn;

            $assertion->neverexpires = $thisPreAssertion->neverexpires;

            $assertion->expiresAt = $thisPreAssertion->expiresAt;


            $badgeclassImagePath = Badgeimage::where('badgeclassID',  $assertion->badgeclassID)->get('filepath')->toArray();


            //ddd($badgeclassImagePath);

            $expireString = function ($assertion2) use ($assertion) {

                if ($assertion2->neverexpires == 'off') {

                    return ('"expires": "' . $assertion->expiresAt . '",');
                } else {
                    return ('');
                }
            };

            //ddd($expireString($assertion));

            $assertionJSON = json_decode(
                '{
            "@context": "https://w3id.org/openbadges/v2",
            "type": "Assertion",
            "id": "' . env('APP_URL') . '/assertions/' . $assertion->assertionID . '",
            "recipient": {
                "hashed": true,
                "type": "' . $assertion->recipientType . '",
                "identity": "' .  $assertion->identity . '",
                "salt": "' . $assertion->salt . '"
            },
            "image": {
                "id": "' . env('APP_URL') . '/badgeclasses/' . $assertion->badgeclassID . '/image"
            },
            "evidence": "' . env('APP_URL') . '/evidences/' . $assertion->assertionID . '",
            "issuedOn": "' . $assertion->issuedOn . '",' .
                    $expireString($assertion) .
                    '"badge": "' . env('APP_URL') . '/badgeclasses/' . $assertion->badgeclassID . '",
            "verification": {
                "type": "HostedBadge"
            }
        }'
            );



            //ddd($assertionJSON);

            $outputFilename =  $assertion->assertionID . '_badge.png';
            $assertion->digitalbadgepath = 'public/badges/' . $outputFilename;

            $thisBadgeclass = Badgeclass::where('badgeclassID', '=', $assertion->badgeclassID)->first();

            $assertion->assertiontype = $thisPreAssertion->assertiontype;


            if ($assertionBadgePath = $this->createAssertionBadge($assertionJSON, $outputFilename, $badgeclassImagePath[0]['filepath'])) {



                $assertion->save();
                $thisPreAssertion->status = '2';
                $thisPreAssertion->save();
            }


            */
        }

        return redirect('backtocompany');
    }

    public function processCreateNewAssertion(Request $request)
    {

        if (!$request->has('badgeclass')) {
            return back()
                ->withErrors('You have not selected a badgeclass!');
        }



        $nowTime = time();

        $salt = hash('sha256', random_bytes(128) . $nowTime);

        $identity = "sha256$" . hash('sha256', ($request->email) . ($salt));


        $assertion = new Assertion;

        $assertion->assertionID = hash('sha256', $nowTime . random_bytes(128));



        $assertion->badgeclassID = $request->badgeclass;

        $assertion->salt = $salt;

        $assertion->identity = $identity;

        $assertion->originalIdentity = ($request->email);

        $assertion->recipientType = 'email';

        $assertion->issuedOn = date_create($request->date)->format('c');


        $badgeclassImagePath = Badgeimage::where('badgeclassID',  $assertion->badgeclassID)->get('filepath')->toArray();

        // ddd($badgeclassImagePath);

        $assertionJSON = json_decode(
            '{
        "@context": "https://w3id.org/openbadges/v2",
        "type": "Assertion",
        "id": "' . env('APP_URL') . '/assertions/' . $assertion->assertionID . '",
        "recipient": {
            "hashed": true,
            "type": "' . $assertion->recipientType . '",
            "identity": "' .  $assertion->identity . '",
            "salt": "' . $assertion->salt . '"
        },
        "image": {
            "id": "' . env('APP_URL') . '/badgeclasses/' . $assertion->badgeclassID . '/image"
        },
        "evidence": "' . env('APP_URL') . '/evidences/' . $assertion->assertionID . '",
        "issuedOn": "' . $assertion->issuedOn . '",
        "badge": "' . env('APP_URL') . '/badgeclasses/' . $assertion->badgeclassID . '",
        "verification": {
            "type": "HostedBadge"
        }
    }'
        );

        //ddd($assertionJSON);

        $outputFilename =  $assertion->assertionID . '_badge.png';
        $assertion->digitalbadgepath = 'public/badges/' . $outputFilename;

        $thisBadgeclass = Badgeclass::where('badgeclassID', '=', $assertion->badgeclassID)->first();

        $assertion->assertiontype = json_encode(json_decode($thisBadgeclass->type, true));


        if ($assertionBadgePath = $this->createAssertionBadge($assertionJSON, $outputFilename, $badgeclassImagePath[0]['filepath'])) {



            $assertion->save();
        }



        return redirect('showAssertions');
    }

    public function createAssertionBadge($assertionJSON, $outputFilename, $badgeclassImagePath)
    {
        //ddd($badgeclassImagePath);

        $chunkType = "iTXt";


        $dataparts[] = "openbadges";

        // Null separator:      1 byte.
        $dataparts[] = "\x00";
        // Compression flag:    1 byte
        // A value of 0 means no compression.
        $dataparts[] = "\x00";
        // Compression method:  1 byte
        // If compression is disabled, the method should also be 0.
        $dataparts[] = "\x00";
        // Language tag:        0 or more bytes (character string)
        // When there is no language specified leave empty.

        // Null separator:      1 byte.
        $dataparts[] = "\x00";
        // Translated keyword:  0 or more bytes
        // When there is no translation specified, leave empty.

        // Null separator:      1 byte.
        $dataparts[] = "\x00";
        // Text:                0 or more bytes.

        $dataparts[] = json_encode($assertionJSON);

        $data = implode("", $dataparts);

        $crc = pack("N", crc32($chunkType . $data));
        $len = pack("N", strlen($data));

        // Chunk format: length + type + data + CRC.
        // CRC is a CRC-32 computed over the chunk type and chunk data.
        $newchunk = $len . $chunkType . $data . $crc;

        $fileContent = Storage::get($badgeclassImagePath);
        $fileSize = strlen($fileContent);

        $result = substr($fileContent, 0, $fileSize - 12)
            . $newchunk
            . substr($fileContent, $fileSize - 12, 12);


        $isFileThere = Storage::put('public/badges/' . $outputFilename, $result);

        return ($isFileThere);
    }


    public function bulkUploadUserAndBadge()
    {
        // Just for a safe state :-)
        $fileContent = Storage::get('/public/' . 'discovet_userbadge_upload_20220505+.csv');

        $csvLines = explode("\n", $fileContent);

        for ($i = 1; $i < 13; $i++) {
            $csvLine =  explode(";", $csvLines[$i]);
            $name = $csvLine[0];
            $email = $csvLine[1];
            $username = $csvLine[2];
            $password = $csvLine[3];
            $badgeClass = $csvLine[4];
            /*    try {

                // create a new user

                $user = new User;
                $user->name = $name;
                $user->email = $email;
                $user->username = $username;
                $user->password = $password;
                $user->roles = '["earner"]';

                $user->save();
            } catch (Exception $e) {
            } */


            try {
                // create a new assertion

                $nowTime = time();

                $salt = hash('sha256', random_bytes(128) . $nowTime);

                $identity = "sha256$" . hash('sha256', ($email) . ($salt));


                $assertion = new Assertion;

                $assertion->assertionID = hash('sha256', $nowTime . random_bytes(128));


                $assertion->badgeclassID = $badgeClass;

                $assertion->salt = $salt;

                $assertion->identity = $identity;

                $assertion->originalIdentity = $email;

                $assertion->recipientType = 'email';

                $assertion->issuedOn = $nowTime;


                $badgeclassImagePath = Badgeimage::where('badgeclassID',  $assertion->badgeclassID)->get('filepath')->toArray();

                // ddd($badgeclassImagePath);

                $assertionJSON = json_decode(
                    '{
        "@context": "https://w3id.org/openbadges/v2",
        "type": "Assertion",
        "id": "' . env('APP_URL') . '/assertions/' . $assertion->assertionID . '",
        "recipient": {
            "hashed": true,
            "type": "' . $assertion->recipientType . '",
            "identity": "' .  $assertion->identity . '",
            "salt": "' . $assertion->salt . '"
        },
        "image": {
            "id": "' . env('APP_URL') . '/badgeclasses/' . $assertion->badgeclassID . '/image"
        },
        "evidence": "' . env('APP_URL') . '/evidences/' . $assertion->assertionID . '",
        "issuedOn": "' . $assertion->issuedOn . '",
        "badge": "' . env('APP_URL') . '/badgeclasses/' . $assertion->badgeclassID . '",
        "verification": {
            "type": "HostedBadge"
        }
    }'
                );

                $outputFilename =  $assertion->assertionID . '_badge.png';
                $assertion->digitalbadgepath = 'public/badges/' . $outputFilename;

                //ddd($assertion);

                if ($assertionBadgePath = $this->createAssertionBadge($assertionJSON, $outputFilename, $badgeclassImagePath[0]['filepath'])) {



                    //$assertion->save();
                }
            } catch (Exception $e) {
            }
        }

        ddd('Bulk upload');
    }

    public function bulkPreAssertionUpload()
    {

        return view('issuerBulkPreassertionUpload');
    }


    public function greencertAssertionUpload()
    {
        return view('issuerGreencertPreassertionUpload');
    }

    function assertISO8601Date($dateStr)
    {
        if (preg_match('/^([\+-]?\d{4}(?!\d{2}\b))((-?)((0[1-9]|1[0-2])(\3([12]\d|0[1-9]|3[01]))?|W([0-4]\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\d|[12]\d{2}|3([0-5]\d|6[1-6])))([T\s]((([01]\d|2[0-3])((:?)[0-5]\d)?|24\:?00)([\.,]\d+(?!:))?)?(\17[0-5]\d([\.,]\d+)?)?([zZ]|([\+-])([01]\d|2[0-3]):?([0-5]\d)?)?)?)?$/', $dateStr) > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }


    public function doGreencertPreAssertionUploadXLSX(Request $request)
    {

        $isError = false;
        $errorMesage[] = 'Error!';

        $content = [];

        $posArray = [];

        try {

            if ($xlsx = SimpleXLSX::parse($request->file('file'))) {

                foreach ($xlsx->readRows() as $k => $r) {

                    if ($k == 0) {


                        $posArray['Név'] = array_search('Név', $r);
                        $posArray['Neptun kód'] = array_search('Neptun kód', $r);
                        $posArray['Sorszám'] = array_search('Sorszám', $r);
                        $posArray['Születési dátum'] = array_search('Születési dátum', $r);
                        $posArray['Születési hely magyarul'] = array_search('Születési hely magyarul', $r);
                        $posArray['Születési hely jellege magyarul, ragozva'] = array_search('Születési hely jellege magyarul, ragozva', $r);
                        $posArray['Születési térség magyarul'] = array_search('Születési térség magyarul', $r);
                        $posArray['Születési térség jellege magyarul, ragozva'] = array_search('Születési térség jellege magyarul, ragozva', $r);
                        $posArray['Születési ország magyarul, ragozva'] = array_search('Születési ország magyarul, ragozva', $r);
                        $posArray['Születési hely angolul'] = array_search('Születési hely angolul', $r);
                        $posArray['Születési hely jellege angolul'] = array_search('Születési hely jellege angolul', $r);
                        $posArray['Születési térség angolul'] = array_search('Születési térség angolul', $r);
                        $posArray['Születési térség jellege angolul'] = array_search('Születési térség jellege angolul', $r);
                        $posArray['Születési ország angolul'] = array_search('Születési ország angolul', $r);

                        $posArray['_c1code'] = array_search('_c1code', $r);
                        $posArray['_c1name'] = array_search('_c1name', $r);
                        $posArray['_c1cred'] = array_search('_c1cred', $r);
                        $posArray['_c1res'] = array_search('_c1res', $r);



                        $posArray['_c2code'] = array_search('_c2code', $r);
                        $posArray['_c2name'] = array_search('_c2name', $r);
                        $posArray['_c2cred'] = array_search('_c2cred', $r);
                        $posArray['_c2res'] = array_search('_c2res', $r);


                        $posArray['_c3code'] = array_search('_c3code', $r);
                        $posArray['_c3name'] = array_search('_c3name', $r);
                        $posArray['_c3cred'] = array_search('_c3cred', $r);
                        $posArray['_c3res'] = array_search('_c4res', $r);


                        $posArray['_c4code'] = array_search('_c4code', $r);
                        $posArray['_c4name'] = array_search('_c4name', $r);
                        $posArray['_c4cred'] = array_search('_c4cred', $r);
                        $posArray['_c4res'] = array_search('_c4res', $r);


                        $posArray['_c5code'] = array_search('_c5code', $r);
                        $posArray['_c5name'] = array_search('_c5name', $r);
                        $posArray['_c5cred'] = array_search('_c5cred', $r);
                        $posArray['_c5res'] = array_search('_c5res', $r);


                        $posArray['_c6code'] = array_search('_c6code', $r);
                        $posArray['_c6name'] = array_search('_c6name', $r);
                        $posArray['_c6cred'] = array_search('_c6cred', $r);
                        $posArray['_c6res'] = array_search('_c6res', $r);


                        $posArray['_c7code'] = array_search('_c7code', $r);
                        $posArray['_c7name'] = array_search('_c7name', $r);
                        $posArray['_c7cred'] = array_search('_c7cred', $r);
                        $posArray['_c7res'] = array_search('_c7res', $r);


                        $posArray['_c8code'] = array_search('_c8code', $r);
                        $posArray['_c8name'] = array_search('_c8name', $r);
                        $posArray['_c8cred'] = array_search('_c8cred', $r);
                        $posArray['_c8res'] = array_search('_c8res', $r);



                        $posArray['_date_issued_hu'] = array_search('_date_issued_hu', $r);
                        $posArray['_date_issued_en'] = array_search('_date_issued_en', $r);

                        $posArray['email'] = array_search('email', $r);


                        $posArray['_c1sublink'] = array_search('_c1sublink', $r);
                        $posArray['_c2sublink'] = array_search('_c2sublink', $r);
                        $posArray['_c3sublink'] = array_search('_c3sublink', $r);
                        $posArray['_c4sublink'] = array_search('_c4sublink', $r);
                        $posArray['_c5sublink'] = array_search('_c5sublink', $r);
                        $posArray['_c6sublink'] = array_search('_c6sublink', $r);
                        $posArray['_c7sublink'] = array_search('_c7sublink', $r);
                        $posArray['_c8sublink'] = array_search('_c8sublink', $r);

                        ///ddd($posArray);

                        foreach ($posArray as $item) {

                            if ($item === false) {
                                $errorMesage[] = 'Bad header in CSV!';
                                $isError = true;
                                break;
                            }
                        }

                        if ($isError) {
                            break;
                        } else {
                            continue;
                        }
                    }


                    $subContent = [];

                    foreach ($posArray as $key => $actItemPos) {
                        $subContent[$key] = $r[$actItemPos];

                        if ($actItemPos == 89) {
                            //ddd($r[$actItemPos]);
                            //ddd($subContent);
                        }
                    }

                    //ddd($subContent);

                    //$subContent[] = 'horvath.cz.janos@mti.bme.hu';

                    $subContent['badgeclassID'] = env('GREENCERT_ID');




                    $content[] = $subContent;
                }
            } else {
                $errorMesage[] = SimpleXLSX::parseError();
                $errorMesage[] = 'File can not be parsed as XLSX!';
                $isError = true;
            }
        } catch (Exception $e) {

            //ddd($e);
            $errorMesage[] = 'General error during reading XLSX!';
            $isError = true;
        }



        if ($isError) {
            return back()
                ->withErrors($errorMesage);
        } else {


            //ddd($content);

            $this->engineGreencertPreAssertion($content);

            return  redirect('backtocompany');
        }
    }


    //2024-ben átállunk XLSX bemeneti állományra, ZD kérésére
    public function doPreAssertionUploadXLSX(Request $request)
    {

        $isError = false;
        $errorMesage[] = 'Error!';

        $content = [];


        try {

            //$filePath1 = $request->file('file')->storeAs('uploads', 'tmpBulkAssertionList.xlsx', 'public');
            //$fileContent = Storage::get('/public/' . $filePath1);
            //ddd($fileContent);

            if ($xlsx = SimpleXLSX::parse($request->file('file'))) {


                foreach ($xlsx->readRows() as $k => $r) {

                    if ($k == 0) {

                        $firstnamePos = array_search('firstname', $r);
                        $lastnamePos = array_search('lastname', $r);
                        $emailPos = array_search('email', $r);
                        $badgeclassIDPos = array_search('badgeclassID', $r);

                        $issuedOnPos  = array_search('issuedOn', $r);

                        // EarnerIsntitution	EarnerDepartment	EarnerDescription	EarnerCritearia

                        $earnerIsntitutionPos  = array_search('earnerIsntitution', $r);
                        $earnerDepartmentPos  = array_search('earnerDepartment', $r);
                        $earnerDescriptionPos  = array_search('earnerDescription', $r);
                        $earnerCriteriaPos  = array_search('earnerCriteria', $r);
                        $earnerEvidencePos = array_search('earnerEvidence', $r);

                        if ($firstnamePos === false || $lastnamePos === false || $emailPos === false || $badgeclassIDPos === false) {
                            $errorMesage[] = 'Bad header in CSV!';
                            $isError = true;
                            break;
                        } else {

                            continue;
                        }
                    }



                    $subContent = [];
                    //0
                    $subContent[] = $r[$firstnamePos];
                    //1
                    $subContent[] = $r[$lastnamePos];


                    $isEmailValid = Validator::make(['Email' => trim($r[$emailPos])], [
                        'Email' => 'email:rfc,dns'
                    ]);

                    if ($isEmailValid->fails()) {
                        $errorMesage[] = 'Bad format of e-mail!';
                        $errorMesage[] = $r[$emailPos];
                        $isError = true;
                    }

                    //2
                    $subContent[] = trim($r[$emailPos]);

                    $numberOfBadgeclass = Badgeclass::where('badgeclassID', '=', $r[$badgeclassIDPos])->count();

                    if (!($numberOfBadgeclass == 1)) {
                        $errorMesage[] = 'Badgeclass does not exist!';
                        $errorMesage[] = $r[$badgeclassIDPos];
                        $isError = true;
                    }

                    //3
                    $subContent[] = $r[$badgeclassIDPos];

                    //ddd((bool)strtotime($r[$issuedOnPos]));
                    //ddd($this->assertISO8601Date($r[$issuedOnPos]));

                    if ($issuedOnPos !== false) {

                        // Itt majd ezt kifejtjük

                        // ddd($this->assertISO8601Date($csvAsArray[$i][$issuedOnPos]));

                        // ddd((bool)strtotime($csvAsArray[$i][$issuedOnPos]));
                        //  ddd($csvAsArray[$i][$issuedOnPos]);


                        //4
                        if ((bool)strtotime($r[$issuedOnPos]) && $this->assertISO8601Date($r[$issuedOnPos])) {
                            $subContent[] = $r[$issuedOnPos];
                        } else {
                            $subContent[] = NULL;
                        }
                    } else {
                        $subContent[] = NULL;
                    }

                    //5
                    if ($earnerIsntitutionPos !== false) {
                        $subContent[] = $r[$earnerIsntitutionPos];
                    } else {
                        $subContent[] = NULL;
                    }

                    //6
                    if ($earnerDepartmentPos !== false) {
                        $subContent[] = $r[$earnerDepartmentPos];
                    } else {
                        $subContent[] = NULL;
                    }

                    //7
                    if ($earnerDescriptionPos !== false) {
                        $subContent[] = $r[$earnerDescriptionPos];
                    } else {
                        $subContent[] = NULL;
                    }

                    //8
                    if ($earnerCriteriaPos !== false) {
                        $subContent[] = $r[$earnerCriteriaPos];
                    } else {
                        $subContent[] = NULL;
                    }

                    //9
                    if ($earnerEvidencePos !== false) {
                        $subContent[] = $r[$earnerEvidencePos];
                    } else {
                        $subContent[] = NULL;
                    }

                    $content[] = $subContent;
                }
            } else {
                $errorMesage[] = SimpleXLSX::parseError();
                $errorMesage[] = 'File can not be parsed as XLSX!';
                $isError = true;
            }
        } catch (Exception $e) {

            $errorMesage[] = 'General error during reading XLSX!';
            $isError = true;
        }

        if ($isError) {
            return back()
                ->withErrors($errorMesage);
        } else {


            //ddd($content);

            $this->enginePreAssertion($content);

            return  redirect('backtocompany');
        }
    }


    public function doPreAssertionUpload(Request $request)
    {

        $isError = false;
        $errorMesage[] = 'Error!';

        $content = [];

        try {



            $csvAsArray = array_map(function ($v) {
                return str_getcsv($v, ";");
            }, file($request->file->getPathName()));

            // BOM
            $csvAsArray[0][0] = str_replace("\xEF\xBB\xBF", '', $csvAsArray[0][0]);


            $firstnamePos = array_search('firstname', $csvAsArray[0]);
            $lastnamePos = array_search('lastname', $csvAsArray[0]);
            $emailPos = array_search('email', $csvAsArray[0]);
            $badgeclassIDPos = array_search('badgeclassID', $csvAsArray[0]);

            $issuedOnPos  = array_search('issuedOn', $csvAsArray[0]);

            // EarnerIsntitution	EarnerDepartment	EarnerDescription	EarnerCritearia

            $earnerIsntitutionPos  = array_search('earnerIsntitution', $csvAsArray[0]);
            $earnerDepartmentPos  = array_search('earnerDepartment', $csvAsArray[0]);
            $earnerDescriptionPos  = array_search('earnerDescription', $csvAsArray[0]);
            $earnerCriteariaPos  = array_search('earnerCritearia', $csvAsArray[0]);


            $earnerEvidencePos = array_search('earnerEvidence', $csvAsArray[0]);

            //ddd(array_search('firstname', $csvAsArray[0]));

            if ($firstnamePos === false || $lastnamePos === false || $emailPos === false || $badgeclassIDPos === false) {
                $errorMesage[] = 'Bad header in CSV!';
                $isError = true;
            } else {

                $emails = array_column($csvAsArray, $emailPos);

                for ($i = 1; $i < sizeof($emails); $i++) {

                    $subContent = [];
                    $subContent[] = $csvAsArray[$i][$firstnamePos];
                    $subContent[] = $csvAsArray[$i][$lastnamePos];


                    $isEmailValid = Validator::make(['Email' => $csvAsArray[$i][$emailPos]], [
                        'Email' => 'email:rfc,dns'
                    ]);

                    if ($isEmailValid->fails()) {
                        $errorMesage[] = 'Bad format of e-mail!';
                        $errorMesage[] = $csvAsArray[$i][$emailPos];
                        $isError = true;
                    }
                    $subContent[] = $csvAsArray[$i][$emailPos];

                    $numberOfBadgeclass = Badgeclass::where('badgeclassID', '=', $csvAsArray[$i][$badgeclassIDPos])->count();

                    if (!($numberOfBadgeclass == 1)) {
                        $errorMesage[] = 'Badgeclas does not exist!';
                        $errorMesage[] = $csvAsArray[$i][$badgeclassIDPos];
                        $isError = true;
                    }

                    $subContent[] = $csvAsArray[$i][$badgeclassIDPos];



                    if ($issuedOnPos !== false) {

                        // Itt majd ezt kifejtjük

                        // ddd($this->assertISO8601Date($csvAsArray[$i][$issuedOnPos]));

                        // ddd((bool)strtotime($csvAsArray[$i][$issuedOnPos]));
                        //  ddd($csvAsArray[$i][$issuedOnPos]);

                        if ((bool)strtotime($csvAsArray[$i][$issuedOnPos]) && $this->assertISO8601Date($csvAsArray[$i][$issuedOnPos])) {
                            $subContent[] = $csvAsArray[$i][$issuedOnPos];
                        } else {
                            $subContent[] = NULL;
                        }
                    }

                    if ($earnerIsntitutionPos !== false) {
                        $subContent[] = $csvAsArray[$i][$earnerIsntitutionPos];
                    } else {
                        $subContent[] = NULL;
                    }

                    if ($earnerDepartmentPos !== false) {
                        $subContent[] = $csvAsArray[$i][$earnerDepartmentPos];
                    } else {
                        $subContent[] = NULL;
                    }

                    if ($earnerDescriptionPos !== false) {
                        $subContent[] = $csvAsArray[$i][$earnerDescriptionPos];
                    } else {
                        $subContent[] = NULL;
                    }

                    if ($earnerCriteariaPos !== false) {
                        $subContent[] = $csvAsArray[$i][$earnerCriteariaPos];
                    } else {
                        $subContent[] = NULL;
                    }

                    if ($earnerEvidencePos !== false) {
                        $subContent[] = $csvAsArray[$i][$earnerEvidencePos];
                    } else {
                        $subContent[] = NULL;
                    }

                    $content[] = $subContent;
                }
            }
        } catch (Exception $e) {
            $errorMesage[] = 'General error during reading CSV!';
            $isError = true;
        }

        if ($isError) {
            return back()
                ->withErrors($errorMesage);
        } else {


            //ddd($content);

            $this->enginePreAssertion($content);

            return  redirect('backtocompany');
        }
    }

    public function fillTypeRecords($id)
    {
        $thisAssertion =  Assertion::where('assertionID', '=', $id)->first();
        if ($thisAssertion->count()) {

            $badgeImage = Storage::get(substr((Badgeimage::where('badgeclassID', '=', Assertion::where('assertionID', '=', $id)->pluck('badgeclassID'))->first('filepath')->filepath), 1));
            $thisBadgeClass = Badgeclass::where('badgeclassID', '=', Assertion::where('assertionID', '=', $id)->pluck('badgeclassID'))->where('status', '=', '1')->first();
            session()->put('assertionID', $id);


            return view('assertionFillTypeRecord')
                ->with('badgeImage', $badgeImage)
                ->with('thisIssuerBadge', $thisBadgeClass)
                ->with('thisAssertion', $thisAssertion);
        }

        return redirect('issuerMain');
    }

    public function recordAssertionType(Request $request)
    {
        $thisAssertion = Assertion::where('assertionID', '=', session()->get('assertionID'))->first();
        if ($thisAssertion->count()) {

            $assertionType = json_decode($thisAssertion->assertiontype, true);

            foreach ($assertionType['form'] as &$form) {
                foreach ($form['fields'] as &$field) {
                    foreach ($request->all() as $inputfield => $value) {

                        if ($field['id'] == $inputfield) {

                            $field['content'] = $value;
                        }
                    }
                }
            }
            //ddd(session()->all());
            //ddd($request->all());

            $thisAssertion->update(['assertiontype' => json_encode($assertionType)]);
            return redirect('showAssertions');
        }
        return redirect('issuerMain');
    }

    public function sendAssertionByEmail($id)
    {


        if ($thisAssertion = Assertion::where('assertionID', '=', $id)->first()) {


            $details = [
                'blade' => 'mailBadge',
                'badgeName' => $thisAssertion->badge_name,
                'subject' => 'You got this badge from DCP: ' . $thisAssertion->badge_name,
                'originalIdentity' => $thisAssertion->originalIdentity,
                'digitalbadgepath' => $thisAssertion->digitalbadgepath,
                'id' => $id,

            ];

            //ddd($thisAssertion->digitalbadgepath);

            Mail::to($thisAssertion->originalIdentity)->send(new BadgeMail($details));
        }



        /* $data = array('name'=>"Horváth Cz. János");
   
        Mail::send(['text'=>'mail'], $data, function($message) {
           $message->to('horvath.cz.janos@mti.bme.hu', 'Tutorials Point')->subject
              ('Laravel Basic Testing Mail');
           $message->from('horvath.cz.janos@mti.bme.hu','HCzJ');
        }); */

        // return $id->header("Refresh", "5;url=/profile");

        return redirect('backtocompany');
    }


    public function assistantSendAssertionLetterChoosing($id)
    {

        session()->forget('mailTemplate');
        session()->forget('details');
        session()->forget('controlButtons');
        session()->forget('whatRole');

        if ($thisAssertion = Assertion::where('assertionID', '=', $id)->first()) {

            if ($issuerID = $thisAssertion->issuer_id) {

                if ($mailTemplate = Issuer::where('issuerID', '=', $issuerID)->first()->mailTemplate) {

                    $mailTemplate = json_decode($mailTemplate, true);
                    $assertionType = json_decode($thisAssertion->assertiontype, true);

                    $details = [
                        'badgeName' => $thisAssertion->badge_name,
                        'badgeCriteria' => $thisAssertion->badge_criteria,
                        'recipientFirstName' => $assertionType['form'][0]['fields'][0]['content'],
                        'recipientLastName' => $assertionType['form'][0]['fields'][1]['content'],
                        'assertionID' => $id,
                        'originalIdentity' => $thisAssertion->originalIdentity,
                        'digitalbadgepath' => $thisAssertion->digitalbadgepath,
                        'id' => $id,
                    ];

                    session()->put('mailTemplate', $mailTemplate);
                    session()->put('details', $details);

                    return view('assistantAssertionMailChooser')
                        ->with('mailTemplate', $mailTemplate)
                        ->with('details', $details);
                }
            }
        }
        return redirect('backtoassistantcompany');
    }

    public function doAssistantSendingAssertionByEmail2(Request $request)
    {

        $isError = false;
        $errorMesage[] = 'Error!';



        if ($request->exists('mailTemplate')) {
        } else {
            $errorMesage[] = 'You have not chosen a mail template!';
            $isError = true;
        }


        if ($isError) {
            return back()
                ->withErrors($errorMesage);
        } else {


            foreach ((session('mailTemplate'))['body'] as $tempMail) {

                if ($tempMail['id'] == $request->get('mailTemplate')) {


                    session()->put('controlButtons', true);
                    session()->put('whatRole', 'assistant');

                    $tmpDetails = session('details');
                    $tmpDetails['blade'] = $tempMail['blade'];

                    $subject = explode('$assertionID', $tempMail['subject']);
                    $subject = $subject[0] . $tmpDetails['badgeName'] . $subject[1];
                    $tmpDetails['subject'] = $subject;

                    $tmpDetails['issuerName'] = Issuer::where('issuerID', '=', session()->get('issuerID'))->first()->name;

                    session()->put('details', $tmpDetails);

                    return view($tempMail['blade'])
                        ->with('mailTemplate', session('mailTemplate'))
                        ->with('details', session('details'));
                }
            }


            return  redirect('backtoassistantcompany');
        }
    }

    public function assistantSendAssertionByEmail3($id)
    {


        if ($thisAssertion = Assertion::where('assertionID', '=', $id)->first()) {

            $thisIssuer = Issuer::where('issuerID', '=', session()->get('issuerID'))->first();

            session()->put('controlButtons', false);

            $details = session('details');



            Mail::to($thisAssertion->originalIdentity)->send(new BadgeMail($details));
        }

        return redirect('backtoassistantcompany');
    }

    public function assistantMultipleNotificationback()
    {

        ddd(session()->all());

        return view('assistantAssertionMultipleMailChooser')
            ->with('mailTemplate', session()->get('mailTemplate'))
            ->with('details', session()->get('details'));
    }

    public function assistantMultipleNotification(Request $request)
    {


        // session()->forget('mailTemplate');
        // session()->forget('details');
        // session()->forget('controlButtons');

        $isError = false;
        $errorMesage[] = 'Error!';

        if ($request->exists('tmpinput')) {
        } else {
            $errorMesage[] = 'No selected assertions!';
            $isError = true;
        }

        if (empty($assertionsList = explode(',', $request->get('tmpinput')))) {
            $errorMesage[] = 'Empty list of assertions!';
            $isError = true;
        } else {

            $allSelectedAssertions = collect();
            foreach ($assertionsList as $item) {

                $thisAssertion = Assertion::where('assertionID', '=', $item)->first();

                if (!$thisAssertion) {
                    $errorMesage[] = 'Not a valid assertion!';
                    $isError = true;
                    break;
                } else {

                    $allSelectedAssertions->add($thisAssertion);
                }
            }
        }



        if ($isError) {

            return redirect('backtoassistantcompany');
        } else {

            $issuerID = session()->get('issuerID');

            if ($mailTemplate = Issuer::where('issuerID', '=', $issuerID)->first()->mailTemplate) {

                $mailTemplate = json_decode($mailTemplate, true);

                session()->put('mailTemplate', $mailTemplate);
                session()->put('details', $allSelectedAssertions);


                return view('assistantAssertionMultipleMailChooser')
                    ->with('mailTemplate', $mailTemplate)
                    ->with('details', $allSelectedAssertions);
            }


            return redirect('backtoassistantcompany');
        }
    }

    public function doAssistantSendingAssertionByMultipleEmail(Request $request)
    {

        $isError = false;
        $errorMesage[] = 'Error!';

        if ($request->exists('mailTemplate')) {
        } else {
            $errorMesage[] = 'You have not chosen a mail template!';
            $isError = true;
        }

        if ($isError) {

            return back()
                ->with($errorMesage);
        } else {

            foreach ((session('mailTemplate'))['body'] as $tempMail) {
                if ($tempMail['id'] == $request->get('mailTemplate')) {

                    foreach ((session()->get('details')) as $thisAssertion) {

                        $subject = explode('$assertionID', $tempMail['subject']);
                        $subject = $subject[0] . $thisAssertion->badge_name . $subject[1];


                        $details = [
                            'blade' => $tempMail['blade'],
                            'subject' => $subject,
                            'issuerName' => Issuer::where('issuerID', '=', session()->get('issuerID'))->first()->name,
                            'badgeName' => $thisAssertion->badge_name,
                            'badgeCriteria' => $thisAssertion->badge_criteria,
                            'recipientFirstName' => $thisAssertion->recipient_first_name,
                            'recipientLastName' => $thisAssertion->recipient_last_name,
                            'assertionID' => $thisAssertion->assertionID,
                            'originalIdentity' => $thisAssertion->originalIdentity,
                            'digitalbadgepath' => $thisAssertion->digitalbadgepath,
                            'id' => $thisAssertion->assertionID,
                        ];

                        session()->put('controlButtons', false);
                        Mail::to($thisAssertion->originalIdentity)->send(new BadgeMail($details));
                    }
                }
            }
        }

        return redirect('backtoassistantcompany');
    }


    public function sendAssertionLetterChoosing($id)
    {

        session()->forget('mailTemplate');
        session()->forget('details');
        session()->forget('controlButtons');
        session()->forget('whatRole');
        session()->save();

        if ($thisAssertion = Assertion::where('assertionID', '=', $id)->first()) {

            if ($issuerID = $thisAssertion->issuer_id) {

                if ($mailTemplate = Issuer::where('issuerID', '=', $issuerID)->first()->mailTemplate) {

                    $mailTemplate = json_decode($mailTemplate, true);



                    //ddd($mailTemplate);

                    // return ('Letter-chooser will be here! ' . $issuerID . ' ' . json_decode($mailTemplate, true));

                    /*     $thisIssuer = Issuer::where('issuerID', '=', session()->get('issuerID'))->first();

                    $thisMailTemplate = (json_decode($thisIssuer['mailTemplate'], true))['body'][1];

                    $subject = explode('$assertionID', $thisMailTemplate['subject']);

                    $subject = $subject[0] . $thisAssertion->badge_name . $subject[1];

                    $blade = $thisMailTemplate['blade'];

                    $details = [

                        'badgeName' => $thisAssertion->badge_name,
                        'subject' => $subject,
                        'originalIdentity' => $thisAssertion->originalIdentity,
                        'digitalbadgepath' => $thisAssertion->digitalbadgepath,
                        'id' => $id,
                        'blade' => $blade,

                    ];
*/
                    $assertionType = json_decode($thisAssertion->assertiontype, true);

                    ///ddd($assertionType);

                    // greencert

                    if ($thisAssertion->is_green_cert) {
                        $details = [
                            'badgeName' => $thisAssertion->badge_name,
                            'badgeCriteria' => $thisAssertion->badge_criteria,
                            'recipientFirstName' => '',
                            'recipientLastName' => $assertionType['form'][0]['fields'][0]['load'][1]['content'],
                            'assertionID' => $id,
                            'originalIdentity' => $thisAssertion->originalIdentity,
                            'digitalbadgepath' => $thisAssertion->digitalbadgepath,
                            'id' => $id,
                        ];
                        //ddd($details);
                    } else {


                        $details = [
                            'badgeName' => $thisAssertion->badge_name,
                            'badgeCriteria' => $thisAssertion->badge_criteria,
                            'recipientFirstName' => $assertionType['form'][0]['fields'][0]['content'],
                            'recipientLastName' => $assertionType['form'][0]['fields'][1]['content'],
                            'assertionID' => $id,
                            'originalIdentity' => $thisAssertion->originalIdentity,
                            'digitalbadgepath' => $thisAssertion->digitalbadgepath,
                            'id' => $id,
                        ];
                    }

                    session()->put('mailTemplate', $mailTemplate);
                    session()->put('details', $details);
                    session()->save();
                    // session()->regenerate();

                    $log = new LogSession;
                    $log->writeLineToLog(time());
                    $log->writeLineToLog(session()->getId());
                    $log->writeLineToLog('sendAssertionLetterChoosing');
                    $log->writeLineToLog(print_r(session()->all(), true));

                    return view('issuerAssertionMailChooser')
                        ->with('mailTemplate', $mailTemplate)
                        ->with('details', $details);
                }
            }
        }
        return redirect('backtocompany');
    }

    public function doSendingAssertionByEmail2(Request $request)
    {

        $log = new LogSession;
        $log->writeLineToLog(time());
        $log->writeLineToLog(session()->getId());
        $log->writeLineToLog('doSendingAssertionByEmail2');
        $log->writeLineToLog(print_r(session()->all(), true));

        $isError = false;
        $errorMesage[] = 'Error!';



        if ($request->exists('mailTemplateX')) {
        } else {
            $errorMesage[] = 'You have not chosen a mail template!';
            $isError = true;
        }


        if ($isError) {
            return back()
                ->withErrors($errorMesage);
        } else {



            if (session('mailTemplate') !== NULL) {
                foreach ((session('mailTemplate'))['body'] as $tempMail) {

                    if ($tempMail['id'] == $request->get('mailTemplateX')) {


                        session()->put('controlButtons', true);
                        session()->put('whatRole', 'issuer');
                        //session()->save();


                        $tmpDetails = session('details');
                        $tmpDetails['blade'] = $tempMail['blade'];

                        $subject = explode('$assertionID', $tempMail['subject']);
                        $subject = $subject[0] . $tmpDetails['badgeName'] . $subject[1];
                        $tmpDetails['subject'] = $subject;

                        $tmpDetails['issuerName'] = Issuer::where('issuerID', '=', session()->get('issuerID'))->first()->name;

                        session()->put('details', $tmpDetails);
                        session()->save();
                        //session()->regenerate();


                        return view($tempMail['blade'])
                            ->with('mailTemplate', session('mailTemplate'))
                            ->with('details', session('details'));
                    }
                }
            } else {

                return view('error');
            }




            //return  redirect('backtocompany');
        }
    }



    public function sendAssertionByEmail3($id)
    {


        if ($thisAssertion = Assertion::where('assertionID', '=', $id)->first()) {

            $thisIssuer = Issuer::where('issuerID', '=', session()->get('issuerID'))->first();

            session()->put('controlButtons', false);

            $details = session('details');



            Mail::to($thisAssertion->originalIdentity)->send(new BadgeMail($details));
        }



        return redirect('backtocompany');
    }



    public function doSendingAssertionByMultipleEmail(Request $request)
    {

        $isError = false;
        $errorMesage[] = 'Error!';



        if ($request->exists('mailTemplate')) {
        } else {
            $errorMesage[] = 'You have not chosen a mail template!';
            $isError = true;
        }

        if ($isError) {

            //ddd($errorMesage);

            return back()
                ->with($errorMesage);;
        } else {

            //ddd(session('mailTemplate'));

            foreach ((session('mailTemplate'))['body'] as $tempMail) {
                if ($tempMail['id'] == $request->get('mailTemplate')) {


                    // ddd(session()->get('details'));

                    foreach ((session()->get('details')) as $thisAssertion) {

                        $subject = explode('$assertionID', $tempMail['subject']);
                        $subject = $subject[0] . $thisAssertion->badge_name . $subject[1];


                        $details = [
                            'blade' => $tempMail['blade'],
                            'subject' => $subject,
                            'issuerName' => Issuer::where('issuerID', '=', session()->get('issuerID'))->first()->name,
                            'badgeName' => $thisAssertion->badge_name,
                            'badgeCriteria' => $thisAssertion->badge_criteria,
                            'recipientFirstName' => $thisAssertion->recipient_first_name,
                            'recipientLastName' => $thisAssertion->recipient_last_name,
                            'assertionID' => $thisAssertion->assertionID,
                            'originalIdentity' => $thisAssertion->originalIdentity,
                            'digitalbadgepath' => $thisAssertion->digitalbadgepath,
                            'id' => $thisAssertion->assertionID,
                        ];

                        session()->put('controlButtons', false);
                        Mail::to($thisAssertion->originalIdentity)->send(new BadgeMail($details));
                    }







                    //return ('Hello');
                }
            }
        }


        return redirect('backtocompany');
    }

    public function multipleNotificationback()
    {



        return view('issuerAssertionMultipleMailChooser')
            ->with('mailTemplate', session()->get('mailTemplate'))
            ->with('details', session()->get('details'));
    }

    public function multipleNotification(Request $request)
    {


        // session()->forget('mailTemplate');
        // session()->forget('details');
        // session()->forget('controlButtons');


        //ddd(session()->all());

        $isError = false;
        $errorMesage[] = 'Error!';

        if ($request->exists('tmpinput')) {
        } else {
            $errorMesage[] = 'No selected assertions!';
            $isError = true;
        }

        if (empty($assertionsList = explode(',', $request->get('tmpinput')))) {
            $errorMesage[] = 'Empty list of assertions!';
            $isError = true;
        } else {

            $allSelectedAssertions = collect();
            foreach ($assertionsList as $item) {

                $thisAssertion = Assertion::where('assertionID', '=', $item)->first();

                if (!$thisAssertion) {
                    $errorMesage[] = 'Not a valid assertion!';
                    $isError = true;
                    break;
                } else {

                    $allSelectedAssertions->add($thisAssertion);
                }
            }
        }



        if ($isError) {

            return redirect('backtocompany');
        } else {

            $issuerID = session()->get('issuerID');

            if ($mailTemplate = Issuer::where('issuerID', '=', $issuerID)->first()->mailTemplate) {

                //ddd($mailTemplate);

                $mailTemplate = json_decode($mailTemplate, true);

                //ddd($mailTemplate);

                //ddd($mailTemplate['body']);


                session()->put('mailTemplate', $mailTemplate);
                session()->put('details', $allSelectedAssertions);

                //ddd(session()->all());

                return view('issuerAssertionMultipleMailChooser')
                    ->with('mailTemplate', $mailTemplate)
                    ->with('details', $allSelectedAssertions);
            }


            return redirect('backtocompany');

            //ddd($allSelectedAssertions);

            //ddd($request->all());
        }
    }

    public function multipleAwardPreAssertion(Request $request)
    {

        // ddd($request);

        $isError = false;
        $errorMesage[] = 'Error!';

        if ($request->exists('tmpinput')) {
        } else {
            $errorMesage[] = 'No selected pre-assertions!';
            $isError = true;
        }

        if (empty($preAssertionsList = explode(',', $request->get('tmpinput')))) {
            $errorMesage[] = 'Empty list of assertions!';
            $isError = true;
        } else {

            $allSelectedPreAssertions = collect();
            foreach ($preAssertionsList as $item) {

                $thisPreAssertion = Preassertion::where('id', '=', $item)->first();

                if (!$thisPreAssertion) {
                    $errorMesage[] = 'Not a valid preassertion!';
                    $isError = true;
                    break;
                } else {

                    $allSelectedPreAssertions->add($thisPreAssertion);
                }
            }
        }

        if ($isError) {

            return redirect('backtocompany');
        } else {

            $issuerID = session()->get('issuerID');

            if ($thisIssuer = Issuer::where('issuerID', '=', $issuerID)->first()) {
                foreach ($allSelectedPreAssertions as $item) {
                    if ($result = $this->awardPreAssertionEngine($item->id)) {

                        $nowAssertion = Assertion::where('assertionID', '=', $result[1])->first();


                        // ddd($nowAssertion->are_all_parts_if_stack);


                        if ($nowAssertion->is_stackmember && $nowAssertion->are_all_parts_if_stack && !$nowAssertion->is_metabadge_awarded_if_stack) {
                            // ddd("Már csak a metabadge hiányzik!");

                            $contents = [];

                            $subContent = [];
                            $subContent[] = $nowAssertion->recipient_last_name;
                            $subContent[] = $nowAssertion->recipient_first_name;
                            $subContent[] = $nowAssertion->originalIdentity;
                            $subContent[] = $nowAssertion->get_meta_badgeclassid_if_stack;
                            $contents[] = $subContent;

                            $tmpPreAssertionID = (new ManageAssertionController)->enginePreAssertion($contents);


                            //ddd($tmpPreAssertionID);

                            $tmpAssertionID = (new ManageAssertionController)->awardPreAssertionEngine($tmpPreAssertionID[0]);
                        }
                    }
                }
            }


            return redirect('backtocompany');
        }
    }

    public function revokeAssertion($id)
    {

        if (($thisAssertion = Assertion::where('assertionID', '=', $id)->first()) && ($thisAssertion->issuer_id == session()->get('issuerID'))) {

            return view('issuerAssertionRevocationpage')
                ->with('thisAssertion', $thisAssertion);
        }

        return redirect('backtocompany');
    }

    public function questionRevoke($id)
    {

        if (($thisAssertion = Assertion::where('assertionID', '=', $id)->first()) && ($thisAssertion->issuer_id == session()->get('issuerID'))) {

            session()->put('thisAssertioID', $thisAssertion->assertionID);

            return view('issuerAssertionQuestionRevoke')
                ->with('thisAssertion', $thisAssertion);
        }

        return redirect('backtocompany');
    }


    public function finalRevoke(Request $request)
    {


        if (($thisAssertion = Assertion::where('assertionID', '=', session()->get('thisAssertioID'))->first()) && ($thisAssertion->issuer_id == session()->get('issuerID'))) {

            $thisAssertion->revoked = "on";

            $nowTime = time();
            $thisAssertion->revokedAt = date("Y-m-d\TH:i:s\Z", $nowTime);
            $thisAssertion->revokedReason = $request->revokedReason;

            $thisAssertion->save();
        }


        session()->forget('thisAssertioID');
        return redirect('backtocompany');
    }


    public function basicEnginePreAssertion($content, $settings)
    {

        $nowTime = time();




        try {
            $preassertion = new Preassertion;

            $preassertion->recorderID = 0;
            if (isset($settings['recorderID'])) {
                $preassertion->recorderID = $settings['recorderID'];
            }

            $preassertion->modifierID = '0';

            $preassertion->badgeclassID = $content[3];

            if (isset($settings['issuerID'])) {
                $preassertion->issuerID = $settings['issuerID'];
            } else {

                throw ValidationException::withMessages([
                    'error' => 'No issuerID set!'
                ]);
            }

            $assertionType = json_decode(Badgeclass::where("badgeclassID", '=', $preassertion->badgeclassID)->first()->type, true);

            $preassertion->recipientType = 'email';
            $preassertion->originalIdentity = $content[2];

            if (!isset($content[4])) {
                $preassertion->issuedOn = date("Y-m-d\TH:i:s\Z", $nowTime);
            } else {
                $preassertion->issuedOn = $content[4];
            }
            $assertionType['form'][0]['fields'][0]['content'] = $content[0];
            $assertionType['form'][0]['fields'][1]['content'] = $content[1];


            if (isset($content[5])) {
                $assertionType['form'][0]['fields'][13]['content'] = $content[5];
            }

            if (isset($content[6])) {
                $assertionType['form'][0]['fields'][14]['content'] = $content[6];
            }

            if (isset($content[7])) {
                $assertionType['form'][0]['fields'][10]['content'] = $content[7];
            }

            if (isset($content[8])) {
                $assertionType['form'][0]['fields'][17]['content'] = $content[8];
            }

            if (isset($content[9])) {
                $assertionType['form'][0]['fields'][18]['content'] = $content[9];
            }



            if (isset($settings['MetaAssoc'])) {
                // Metabadge: basebadgesclass and assertions in JSON



                $assertionType['form'][0]['fields'][19]['content'] = $settings['MetaAssoc'];
            }

            $preassertion->assertiontype = json_encode($assertionType, true);

            $preassertion->released = date("Y-m-d\TH:i:s\Z", $nowTime);

            $preassertion->neverexpires = 'on';

            $preassertion->status = '1';

            $preassertion->save();

            return ($preassertion->id);
        } catch (Exception $e) {

            return ($e);
        }
    }
}

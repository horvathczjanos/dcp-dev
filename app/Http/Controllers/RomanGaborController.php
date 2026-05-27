<?php

namespace App\Http\Controllers;

use App\DCP\BadgeExplorer;
use App\DCP\CheckBadgeFile;
use App\Models\Assertion;
use App\Models\Badgeclass;
use App\Models\EarnerBadge;
use App\Models\EarnerBadgeCheck;
use App\Models\User;
use DOMDocument;
use Exception;
// use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\TryCatch;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RomanGaborController extends Controller
{
    public function rgabor($id)
    {
        //---


        $thisEarnerbadge = EarnerBadge::where('earnerID', '=', Auth::id())->where('earnerbadgeID', '=', $id)->where('status', '=', '1')->get();


        if ($thisEarnerbadge->count()) {



            $user = User::where('id', '=', $thisEarnerbadge->pluck('earnerID'))->first();

            $discoCertTemplate = Storage::get('/public/certificate_template.svg');

            $earnerbadge =  EarnerBadge::where('earnerbadgeID', '=', $id)->get();
            $badgeImage = Storage::get($earnerbadge[0]['earnerbadgepath']);
            $discovetImage = Storage::get('/public/discovet_long_header.png');
            // Added 2022-06-20 by RG
            $discovetLogo = Storage::get('/public/discovet_logo.png');
            $certBgImage = Storage::get('/public/certificate_template_v2.jpg');
            // Added 2022-06-20 by RG

            //ddd($earnerbadge->first()->badge_issuedon);

            $earnerBadgeURL = env('APP_URL') . '/earnerbadges/' . $earnerbadge[0]['earnerbadgeID'] . '/badge';

            $qrCode = QrCode::size(500)
                ->format('png')
                ->backgroundColor(255, 255, 255)
                ->generate($earnerBadgeURL);


            $subject = [];
            //            Log::info("RGabor Test HTML Page, " . $userid  . ", " . $username . ", " . $thisBadge->getBadgeNameAttribute() . ", " . $id . ", " . json_encode($subject) . ", " . \Request::ip() . ", " . \Request::userAgent());
            return view('rgabor2')
                ->with('user', $user)
                ->with('badgeImage', $badgeImage)
                ->with('qrCode', $qrCode)
                ->with('discovetLogo', $discovetLogo)
                ->with('certBgImage', $certBgImage)
                ->with('thisEarnerbadge', $thisEarnerbadge);
        }

        //---
        return view('rgabor');
    }

    public function leetcode()
    {


        $dividend = 2147483647;
        $divisor = 1;

        //$dividend = 2147483647;
        //$divisor = 1;

        $resultSign = (($dividend > 0) - ($dividend < 0)) * (($divisor > 0) - ($divisor < 0));
        $absDividend = abs($dividend);
        $absDivisor = abs($divisor);

        if ($absDivisor > $absDividend) {
            return 0;
        } else {

            $logResult = log10($absDividend) - log10($absDivisor);



            return min($resultSign * intval(strval(pow(10, $logResult))), 2147483647);

            //            return intval(strval(pow(10, $logResult)));

            //return $resultSign*intval(pow(10, $logResult));

            //return min($resultSign*intval(pow(10, $logResult)), 2147483647);           


        }
    }


    public function test()
    {


        // HCzJ digitális kitűzője
        //$myTestBadge = new BadgeExplorer('/public/earnerbadges/1686143778_badge_19b1ee3ac35a933d00c1151fdfd98f66d989e1a5341386a1935becc24d636a76.png');

        //certificate_template.jpg
        //$myTestBadge = new BadgeExplorer('/public/certificate_template.jpg');


        //return ($myTestBadge->getHello());

        return ("20240405");
    }


    /* public function test(Request $request)
    {
        if ($request->expectsJson()) {
            return ("JSON");
        } else {
            return ("HTML");
        }


        /*  if ($request->accepts(['application/json'])) {
            return ("JSON");
        } elseif ($request->accepts(['text/html'])) {
            return ("HTML");
        } */
    //ddd($request->getAcceptableContentTypes());

    //$languages = json_decode(env('LANGUAGES'), true);
    //ééreturn ($languages['en']);



    //  } 

    // Consumer - read

    public function test2()
    {
        $host = \App\Models\BadgeConnectHost::findOrFail(1);


        return view('consumerTest', compact('host'));
    }

    public function test3()
    {
        $host = \App\Models\BadgeConnectHost::findOrFail(1);


        return view('consumerTest3', compact('host'));
    }

    public function test4()
    {
        $host = \App\Models\BadgeConnectHost::findOrFail(1);


        return view('consumerTest4', compact('host'));
    }

    public function test5()
    {
        $host = \App\Models\BadgeConnectHost::findOrFail(1);


        return view('consumerTest5', compact('host'));
    }

    public function test6()
    {
        $host = \App\Models\BadgeConnectHost::findOrFail(1);


        return view('consumerTest6', compact('host'));
    }

    // Consumer - write


    public function test7()
    {
        $host = \App\Models\BadgeConnectHost::findOrFail(1);


        return view('consumerTest7', compact('host'));
    }

    public function test8()
    {
        $host = \App\Models\BadgeConnectHost::findOrFail(1);


        return view('consumerTest8', compact('host'));
    }

    public function test9()
    {
        $host = \App\Models\BadgeConnectHost::findOrFail(1);

        $assertion = \App\Models\BadgeAssertionsV21::findOrFail(1);




        return view('consumerTest9', compact('host', 'assertion'));
    }


    public function test10()
    {
        $host = \App\Models\BadgeConnectHost::findOrFail(1);


        return view('consumerTest10', compact('host'));
    }

    // HCzJ, 20230620
    public function readSVG()
    {

        $svgURL = 'https://openbadgesvalidator.imsglobal.org/SampleResources/OB20-assertion-conformance-servicelearning.svg';
        //$svgURL = 'http://www.w3schools.com/xml/plant_catalog.xml';
        //$svgURL = 'https://mdn.dev/archives/media/attachments/2012/07/09/3075/89b1e0a26e8421e19f907e0522b188bd/svgdemo1.xml';

        $xmlDoc = '<?xml version = "1.0" encoding = "UTF-8"?>
        <client xmlns:info="http://openbadges.org">
            <order id="1">
                <login>login</login>
                <info:asd>
                <![CDATA[
        some data
        ]]>
                </info:asd>
            </order>
        </client>
        
        ';

        try {
            $actSVG = file_get_contents($svgURL);

            $doc = new DOMDocument();
            $doc->loadXML($actSVG);

            $test = $doc->getElementsByTagName("assertion");

            ddd(json_decode(($test[0]->childNodes)[0]->textContent, true));
        } catch (\Throwable $th) {
            return  redirect('abc');
        }



        /*      $actSVG = file_get_contents($svgURL);

        $doc = new DOMDocument();
        $doc->loadXML($actSVG);

        $test = $doc->getElementsByTagName("assertion");

        ddd(json_decode(($test[0]->childNodes)[0]->textContent, true));

       $data = array();
        foreach ($test as $tag) 
        {

            //ddd($tag);
            foreach($tag->childNodes as $child) 
            {

                //ddd($child->textContent);
                if ($child->nodeType == XML_CDATA_SECTION_NODE) 
                {
                    $data=  $child->textContent;
                }
            }
        }



        ddd(json_decode($data, true)); */

        return ('x');
    }

    public function assertionLink()
    {
        $assertionURL = 'https://badges.imsglobal.org/public/assertions/tR56GnX_RKeX9k1nCj_niQ?v=2_0';

        try {
            $actAssertion = json_decode(file_get_contents($assertionURL), true);

            if ($assertionURL == $actAssertion['id']) {

                $nowTime = time();

                $assertionImage = file_get_contents($actAssertion['image']);

                $fileName = $nowTime . '_' . basename($actAssertion['image']);

                $isFileThere01 = Storage::put('public/uploads/' . $fileName, $assertionImage);

                $myReport = new CheckBadgeFile('uploads/' . $fileName);

                $backData = $myReport->getReportOfBadgeFile();


                if ($backData['summary']) {

                    $filePath2 = "earnerbadges/" . $fileName;

                    $isFileThere01 = Storage::put('public/earnerbadges/' . $fileName, $assertionImage);

                    $earnerbadge = new EarnerBadge;

                    $fileContent = Storage::get('/public/' . $filePath2);

                    // Ez itt a file leíró adatok alapján állít elő hash-t, nem a tartalom alapján!
                    $earnerbadgecontenthash = hash('sha256', $fileContent);


                    $salt = hash('sha256', random_bytes(128) . $nowTime);
                    $identity = "sha256$" . hash('sha256', $earnerbadgecontenthash  . ($salt));

                    $earnerbadge->earnerID = "1";
                    $earnerbadge->earnerbadgeID = $identity;
                    $earnerbadge->earnerbadgecontenthash = $earnerbadgecontenthash;
                    $earnerbadge->earnerbadgepath = '/public/' . $filePath2;
                    $earnerbadge->description = "Minta SVG alapú kitűző, IMS hivatkozásról behúzva.";
                    $earnerbadge->issearchable = '1';

                    $earnerbadge->cachedBadgeReport = serialize($backData);


                    $earnerbadge->recipientType = $backData['checkOB']['recipient']['type'];

                    $userEmail = User::where('id', '=', $earnerbadge->earnerID)->first()->email;

                    //ddd($userEmail);

                    $earnerbadge->recipientIdentity = $userEmail;

                    $earnerbadge->earnerBadgeTags = '[]';

                    $earnerbadge->status = '1';

                    $earnerbadge->save();
                    $this->recordCheckEarnerBadge($identity);
                }

                return  redirect('abc');
            } else {
                return  redirect('abc');
            }
        } catch (\Throwable $th) {
            return  redirect('abc');
        }
    }


    public function convertTojson()
    {

        $zdFile = fopen('ZD_alapadatok.csv', 'r');
        while ($csvRow = fgetcsv($zdFile, null, ';')) {
            $ZDcsv[] = $csvRow;
        }
        fclose($zdFile);

        // ddd(sizeof($ZDcsv));

        $outArray = [];

        for ($i = 1; $i < sizeof($ZDcsv); $i++) {
            $tmpArray = [];

            $tmpArray['name'] = array(array('lang' => 'en', 'content' => $ZDcsv[$i][2]), array('lang' => 'hu', 'content' => $ZDcsv[$i][1]));
            $tmpArray['description'] = array(array('lang' => 'en', 'content' => $ZDcsv[$i][4]), array('lang' => 'hu', 'content' => $ZDcsv[$i][3]));
            $tmpArray['type'] = $ZDcsv[$i][5];
            $tmpArray['id'] = $ZDcsv[$i][0];

            if (str_contains($ZDcsv[$i][6], '*')) {
                $tmpArray['content'] = '';
            } else {
                $tmpArray['content'] = array(array('lang' => 'en', 'content' => ""), array('lang' => 'hu', 'content' => ""));
            }


            //$tmpArray[] = array('name' => array(array('lang' => 'en', 'content' => $ZDcsv[$i][2]), array('lang' => 'hu', 'content' => $ZDcsv[$i][1])));
            //$tmpArray[] = array('description' => array(array('lang' => 'en', 'content' => $ZDcsv[$i][4]), array('lang' => 'hu', 'content' => $ZDcsv[$i][3])));
            //$tmpArray[] = array('type' => $ZDcsv[$i][5]);
            //$tmpArray[] = array('id' => $ZDcsv[$i][0]);

            /*    if (str_contains($ZDcsv[$i][6], '*')) {
                $tmpArray[] = array('content' => "");
            } else {
                $tmpArray[] = array('content' => array(array('lang' => 'en', 'content' => ""), array('lang' => 'hu', 'content' => "")));
            }*/


            $outArray[] = $tmpArray;
        }



        return (json_encode($outArray));
    }



    public function getIssuerBadgeclasses($issuerID)
    {
        $issuersBadgeclasses = Badgeclass::where('issuerID', '=', $issuerID)->get('badgeclassID')->toArray();
        return $issuersBadgeclasses;
    }

    public function getAwardeesByIssuer($issuerID)
    {
        $issuersBadgeclasses = $this->getIssuerBadgeclasses($issuerID);
        $assertionList = Assertion::select('assertionID', 'badgeclassID', 'originalIdentity', 'issuedOn')->whereIn('badgeclassID', $issuersBadgeclasses)->get()->sortBy('originalIdentity')->groupBy('originalIdentity')->toArray();

        return ($assertionList);
    }

    public function makeAssertionGreatAgain($content, $settings)
    {
        $tmpPreAssertionID = (new ManageAssertionController)->basicEnginePreAssertion($content, $settings);
        $tmpAssertionID = (new ManageAssertionController)->awardPreAssertionEngine($tmpPreAssertionID);
        return ($tmpAssertionID);
    }




    public function engineMetabadge($issuerID, $rulePack, $dryRun)
    {

        $outputMessage = [];

        $assertionList = $this->getAwardeesByIssuer($issuerID);


        foreach ($assertionList as $key => $userProfile) {

            $originalIdentity = $userProfile[0]['originalIdentity'];


            $userBadgeclassList = [];
            $userBadgeAssertionAssoc = [];

            foreach ($userProfile as $key2 => $badgeclassItem) {
                $userBadgeclassList[] = $badgeclassItem['badgeclassID'];

                //Getting badgeclassID and assertionIDs connections
                $userBadgeAssertionAssoc[$badgeclassItem['badgeclassID']][] = $badgeclassItem['assertionID'];
            }

            $badgeOccurence = array_count_values($userBadgeclassList);

            $stop = false;
            foreach ($rulePack['stopBadges'] as $key => $stopBadge) {

                if (array_key_exists($stopBadge, $badgeOccurence)) {

                    $stop = true;
                }
            }

            if (!$stop) {

                //ddd(array_intersect($rulePack['badgeMustHaveRule']['subjectBadge'], array_keys($badgeOccurence)));

                if (array_intersect($rulePack['badgeMustHaveRule']['subjectBadge'], array_keys($badgeOccurence))) {

                    $numberOfOccurence = [];

                    //ddd($rulePack['badgeMustHaveRule']['subjectBadge']);

                    foreach ($rulePack['badgeMustHaveRule']['subjectBadge'] as $key => $subjectBadge) {


                        if (!isset($badgeOccurence[$subjectBadge])) {
                            //ddd($subjectBadge);
                        } else {
                            $numberOfOccurence[] = $badgeOccurence[$subjectBadge];
                        }
                    }

                    //ddd($assertionList);
                    //ddd($userProfile);
                    //ddd($userBadgeAssertionAssoc);
                    //ddd($userBadgeclassList);
                    //ddd($badgeOccurence);
                    //ddd($numberOfOccurence);


                    //ddd($rulePack['badgeMustHaveRule']['subjectBadge']);

                    // ddd(Assertion::where("badgeclassID", '=', $rulePack['badgeMustHaveRule']['subjectBadge'][0]));
                    // ddd((Assertion::where("badgeclassID", '=', $rulePack['badgeMustHaveRule']['subjectBadge'][0])->where('originalIdentity', '=', $userProfile[0]['originalIdentity'])->first()));

                    $cancel = false;

                    try {
                        $assertionType = json_decode(Assertion::where("badgeclassID", '=', $rulePack['badgeMustHaveRule']['subjectBadge'][0])->where('originalIdentity', '=', $userProfile[0]['originalIdentity'])->first()->assertiontype, true);
                    } catch (Exception $e) {
                        //ddd($userProfile[0]['originalIdentity']);
                        $cancel = true;
                    }



                    $nowTime = time();

                    $content = [];

                    $content[] = $assertionType['form'][0]['fields'][0]['content'];
                    $content[] = $assertionType['form'][0]['fields'][1]['content'];
                    $content[] = $userProfile[0]['originalIdentity'];
                    $content[] = $rulePack['badgeMustHaveRule']['metaBadge'];
                    $content[] = date("Y-m-d\TH:i:s\Z", $nowTime);

                    $settings['recorderID'] = 0;
                    $settings['issuerID'] = $issuerID;


                    $conditionalAssertions = [];

                    switch ($rulePack['rule']) {
                        case 'ThisMuchOrMore':


                            foreach ($rulePack['badgeMustHaveRule']['badgeCount'] as $key => $badgeCount) {
                                if ($numberOfOccurence[$key] >= $badgeCount) {

                                    $actBadgeclassID = $rulePack['badgeMustHaveRule']['subjectBadge'][$key];
                                    $conditionalAssertions[$actBadgeclassID] = $userBadgeAssertionAssoc[$actBadgeclassID];
                                } else {
                                    $cancel = true;
                                }
                            }


                            //        ddd($conditionalAssertions);

                            if (!$cancel) {

                                if (!$dryRun) {
                                    $settings['MetaAssoc'] = json_encode($conditionalAssertions);
                                    $assertionID = $this->makeAssertionGreatAgain($content, $settings);
                                    $outputMessage[$originalIdentity]['result'] = ['New assertion was awarded!', $assertionID];
                                } else {

                                    $outputMessage[$originalIdentity]['dryRun'] = ['There were matches but dryrun is active!'];
                                }
                            } else {
                                $outputMessage[$originalIdentity]['noMatch'] = ['Rule has been processed but condition has not met!'];
                            }

                            break;

                        case 'ExactlyAsMuch':

                            foreach ($rulePack['badgeMustHaveRule']['badgeCount'] as $key => $badgeCount) {
                                if ($numberOfOccurence[$key] == $badgeCount) {

                                    $actBadgeclassID = $rulePack['badgeMustHaveRule']['subjectBadge'][$key];
                                    $conditionalAssertions[$actBadgeclassID] = $userBadgeAssertionAssoc[$actBadgeclassID];
                                } else {
                                    $cancel = true;
                                }
                            }


                            //        ddd($conditionalAssertions);

                            if (!$cancel) {
                                if (!$dryRun) {
                                    $settings['MetaAssoc'] = json_encode($conditionalAssertions);
                                    $assertionID = $this->makeAssertionGreatAgain($content, $settings);
                                    $outputMessage[$originalIdentity]['result'] = ['New assertion was awarded!', $assertionID];
                                } else {

                                    $outputMessage[$originalIdentity]['dryRun'] = ['There were matches but dryrun is active!'];
                                }
                            } else {
                                $outputMessage[$originalIdentity]['noMatch'] = ['Rule has been processed but condition has not met!'];
                            }

                            break;

                        case 'ThisAndThatMuch':

                            foreach ($rulePack['badgeMustHaveRule']['badgeCount'] as $key => $badgeCount) {

                                //ddd($numberOfOccurence[$key]);

                                //   try {
                                if (isset($numberOfOccurence[$key])) {
                                    if ($numberOfOccurence[$key] == $badgeCount) {

                                        $actBadgeclassID = $rulePack['badgeMustHaveRule']['subjectBadge'][$key];

                                        // ZD futott be ebbe a hibába, 20251016 (a feltételben szereplő badgeclass-nak nincs még assertion-je!!)
                                        if (!isset($userBadgeAssertionAssoc[$actBadgeclassID])) {
                                            $cancel = true;
                                            break;
                                        }
                                        $conditionalAssertions[$actBadgeclassID] = $userBadgeAssertionAssoc[$actBadgeclassID];
                                    } else {
                                        $cancel = true;
                                    }
                                } else {
                                    $cancel = true;
                                }
                                //   } catch (Exception $e) {
                                //       ddd($userBadgeAssertionAssoc);
                                //   }



                                /*   try {
                                    if ($numberOfOccurence[$key] == $badgeCount) {
                                    } else {
                                        $cancel = true;
                                    }
                                } catch (Exception $e) {
                                    //ddd($rulePack['badgeMustHaveRule']['badgeCount']);
                                }*/
                            }


                            //     ddd($conditionalAssertions);


                            if (!$cancel) {
                                if (!$dryRun) {
                                    $settings['MetaAssoc'] = json_encode($conditionalAssertions);
                                    $assertionID = $this->makeAssertionGreatAgain($content, $settings);
                                    $outputMessage[$originalIdentity]['result'] = ['New assertion was awarded!', $assertionID];
                                } else {

                                    $outputMessage[$originalIdentity]['dryRun'] = ['There were matches but dryrun is active!'];
                                }
                            } else {
                                $outputMessage[$originalIdentity]['noMatch'] = ['Rule has been processed but condition has not met!'];
                            }


                            break;

                        case 'ThisOrThatMuch':

                            foreach ($rulePack['badgeMustHaveRule']['badgeCount'] as $key => $badgeCount) {

                                //ddd($numberOfOccurence[$key]);


                                if (isset($numberOfOccurence[$key])) {
                                    if ($numberOfOccurence[$key] == $badgeCount) {


                                        $actBadgeclassID = $rulePack['badgeMustHaveRule']['subjectBadge'][$key];
                                        $conditionalAssertions[$actBadgeclassID] = $userBadgeAssertionAssoc[$actBadgeclassID];


                                        // Már az első találat elegendő a továbblépéshez
                                        $cancel = false;
                                        break;
                                    } else {
                                    }
                                } else {
                                    /* */
                                }

                                $cancel = true;

                                /*   try {
                                    if ($numberOfOccurence[$key] == $badgeCount) {
                                    } else {
                                        $cancel = true;
                                    }
                                } catch (Exception $e) {
                                    //ddd($rulePack['badgeMustHaveRule']['badgeCount']);
                                }*/
                            }

                            //   ddd($conditionalAssertions);

                            if (!$cancel) {
                                if (!$dryRun) {
                                    $settings['MetaAssoc'] = json_encode($conditionalAssertions);
                                    $assertionID = $this->makeAssertionGreatAgain($content, $settings);
                                    $outputMessage[$originalIdentity]['result'] = ['New assertion was awarded!', $assertionID];
                                } else {

                                    $outputMessage[$originalIdentity]['dryRun'] = ['There were matches but dryrun is active!'];
                                }
                            } else {
                                $outputMessage[$originalIdentity]['noMatch'] = ['Rule has been processed but condition has not met!'];
                            }


                            break;



                        default:
                            # code...
                            break;
                    }
                }
            } else {
                $outputMessage[$originalIdentity]['stop'] = ['Some stopBadges were there!', $rulePack['stopBadges']];
            }
        }

        return ($outputMessage);
    }
}

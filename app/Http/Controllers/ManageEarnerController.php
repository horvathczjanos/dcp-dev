<?php

namespace App\Http\Controllers;


use App\DCP\CheckBadgeFile;
use App\Mail\EarnerBadgeMail;
use App\Models\Assertion;
use App\Models\EarnerBadge;
use App\Models\EarnerBadgeCheck;
use App\Models\User;
use Dompdf\Options;
use Exception;
// use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class ManageEarnerController extends Controller
{
    public function showEarnerDashboard(Request $request)
    {

        $earnerbadgenames = [];

        session()->forget('actBadgeToSendByEmail');

        $earnerbadges = EarnerBadge::where('earnerID', '=', Auth::id())->where('status', '=', '1')->get();
        foreach ($earnerbadges as $earnerbadge) {

            // $myReport = new CheckBadgeFile(ltrim($earnerbadge->earnerbadgepath, '/public/'));
            // $backData = $myReport->getReportOfBadgeFile();

            $thisBadge = EarnerBadge::where('earnerbadgeID', '=', $earnerbadge->earnerbadgeID)->where('status', '=', '1')->first();
            $backData = unserialize($thisBadge->cachedBadgeReport);

            //ddd($backData);
            if (array_key_exists("checkOB", $backData)) {
                array_push($earnerbadgenames, $backData['checkOB']['badgeClass']['name']);
            } else {
                array_push($earnerbadgenames, "");
            }
        }

        //ddd($earnerbadgenames);

        $earnerbadgesUpgraded = EarnerBadge::where('earnerID', '=', Auth::id())->where('status', '=', '1')->get()->map(function ($item) {
            $item->name = $item->badge_name;

            $issuedOn = 'NA';

            $backData = unserialize($item->cachedBadgeReport);
            $issuedOn = $backData['checkOB']['issuedOn'];


            // Különféle kibocsátási dátumformátumok lehetnek, ezeket hozzuk egyenalakra

            //ddd($issuedOn);

            //ddd(gmdate("Y-m-d\TH:i:s\Z", strtotime($issuedOn)));


            if (is_numeric($issuedOn)) {
                $issuedOn = gmdate("Y-m-d\TH:i:s\Z", $issuedOn);
            } else {
                $issuedOn = gmdate("Y-m-d\TH:i:s\Z", strtotime($issuedOn));
            }

            /*  try {
                if (checkdate(date('m', $issuedOn), date('d', $issuedOn), date('Y', $issuedOn))) {
                    $issuedOn = gmdate("Y-m-d\TH:i:s\Z", $issuedOn);
                }
            } catch (Exception $e) {
                //ddd('Hello');
            } */

            $item->issued_at =  $issuedOn;
            return $item;
        });


        //ddd(session()->exists('userID'));


        if ($request->exists('orderbutton')) {

            //ddd($request->orderbutton);

            $order = explode(" ", $request->orderbutton);

            $orderAtom = "";

            //ddd($request->all());
            switch ($order[0]) {
                case 'Name':
                    $orderAtom = 'name';
                    break;

                case 'Uploading':
                    $orderAtom = 'created_at';
                    break;

                case 'Issued':
                    $orderAtom = 'issued_at';
                    break;

                default:
                    # code...
                    break;
            }

            switch ($order[1]) {
                case 'ASC':
                    $earnerbadgesUpgraded = $earnerbadgesUpgraded->sortBy($orderAtom);
                    break;

                case 'DESC':
                    $earnerbadgesUpgraded = $earnerbadgesUpgraded->sortByDesc($orderAtom);


                    break;

                default:
                    # code...
                    break;
            }
        } else {
            //$earnerbadgesUpgraded = $earnerbadgesUpgraded->sortBy('name');
            $earnerbadgesUpgraded = $earnerbadgesUpgraded->sortByDesc('created_at');
        }


        // ddd($earnerbadgesUpgraded);

        return view('earnerBadgeboard')
            ->with('numberofearnerbadges', EarnerBadge::where('earnerID', '=', Auth::id())->where('status', '=', '1')->count())
            ->with('earnerbadges', $earnerbadgesUpgraded);
    }


    public function showEarnerBadgePage()
    {

        return view('addEarnerBadge');
    }

    public function doEarnerBadgeRecord(Request $request)
    {
        $isValid = Validator::make($request->all(), [
            'file' => 'required|mimes:png,svg'
        ]);

        if ($isValid->fails()) {
            return back()
                ->withErrors($isValid);
        } else {
            if ($request->file()) {
                $fileName = time() . '_' . $request->file->getClientOriginalName();
                $filePath1 = $request->file('file')->storeAs('uploads', $fileName, 'public');



                $myReport = new CheckBadgeFile($filePath1);
                $backData = $myReport->getReportOfBadgeFile();

                //ddd($backData );

                if ($backData['summary']) {

                    $filePath2 = $request->file('file')->storeAs('earnerbadges', $fileName, 'public');

                    $earnerbadge = new EarnerBadge;

                    $fileContent = Storage::get('/public/' . $filePath2);

                    // Ez itt a file leíró adatok alapján állít elő hash-t, nem a tartalom alapján!
                    $earnerbadgecontenthash = hash('sha256', $fileContent);

                    $nowTime = time();
                    $salt = hash('sha256', random_bytes(128) . $nowTime);
                    $identity = "sha256$" . hash('sha256', $earnerbadgecontenthash  . ($salt));

                    $earnerbadge->earnerID = Auth::id();
                    $earnerbadge->earnerbadgeID = $identity;
                    $earnerbadge->earnerbadgecontenthash = $earnerbadgecontenthash;
                    $earnerbadge->earnerbadgepath = '/public/' . $filePath2;
                    $earnerbadge->description = $request->earnerbadge_description;
                    $earnerbadge->issearchable = '0';

                    $earnerbadge->cachedBadgeReport = serialize($backData);

                    //ddd($backData);

                    $earnerbadge->recipientType = $backData['checkOB']['recipient']['type'];

                    $userEmail = User::where('id', '=', $earnerbadge->earnerID)->first()->email;

                    //ddd($userEmail);

                    $earnerbadge->recipientIdentity = $userEmail;

                    $earnerbadge->earnerBadgeTags = '[]';

                    $earnerbadge->status = '1';

                    $earnerbadge->save();
                    $this->recordCheckEarnerBadge($identity);


                    return back()
                        ->with('success', __('addEarnerBadgePage.add_earnerbadge_success'))
                        ->with('id', $backData['checkOB']['id'])
                        ->with('badge', $backData['checkOB']['badge'])
                        ->with('issuedOn', $backData['checkOB']['issuedOn']);
                } else {

                    //ddd($backData);

                    return back()
                        ->withErrors([__('addEarnerBadgePage.add_earnerbadge_error-not-ob')]);
                }
            }
            return back()
                ->withErrors('General error');
        }
    }

    public function showEarnerBadgeAddByURL()
    {
        return view('addEarnerBadgeAddByURL');
    }

    public function doEarnerBadgeRecordByURL(Request $request)
    {
        $assertionURL = $request->get('earnerbadgeurl');

        try {
            $result = Http::get($assertionURL);

            try {


                $actAssertion = json_decode(file_get_contents($assertionURL), true);



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

                    $earnerbadge->earnerID = Auth::id();;
                    $earnerbadge->earnerbadgeID = $identity;
                    $earnerbadge->earnerbadgecontenthash = $earnerbadgecontenthash;
                    $earnerbadge->earnerbadgepath = '/public/' . $filePath2;
                    $earnerbadge->description = $request->get('earnerbadge_description');
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
                    return redirect('earnerMain');
                }

                return  redirect('abc');
            } catch (\Throwable $th) {

                return  redirect('abc');
            }
        } catch (Exception $ex) {
            return redirect('earnerMain');
        }
    }

    public function deleteEarnerBadge($id)
    {


        if ($thisEarnerbadge = EarnerBadge::where('earnerID', '=', Auth::id())->where('earnerbadgeID', '=', $id)->where('status', '=', '1')->first()) {


            $backData = unserialize($thisEarnerbadge->cachedBadgeReport);

            return view('earnerBadgeDelete')
                ->with('thisEarnerbadge', $thisEarnerbadge)
                ->with('backData', $backData);

            //$thisEarnerbadge->update(['status' => '0']);
        }

        //ddd(EarnerBadge::where('earnerID', '=', Auth::id())->where('earnerbadgeID', '=', $id)->where('status', '=', '1')->count());
        /* $thisEarnerbadge = EarnerBadge::where('earnerID', '=', Auth::id())->where('earnerbadgeID', '=', $id)->where('status', '=', '1');
        if ($thisEarnerbadge->count()) {


            $thisEarnerbadge->update(['status' => '0']);
        } */

        return redirect('earnerMain');
    }

    public function finalDeleteEarnerBadge($id)
    {

        if ($thisEarnerbadge = EarnerBadge::where('earnerID', '=', Auth::id())->where('earnerbadgeID', '=', $id)->where('status', '=', '1')->first()) {
            $thisEarnerbadge->update(['status' => '0']);
        }

        return redirect('earnerMain');
    }

    public function editEarnerBadge($id)
    {
        $thisEarnerbadge = EarnerBadge::where('earnerID', '=', Auth::id())->where('earnerbadgeID', '=', $id)->where('status', '=', '1');
        if ($thisEarnerbadge->count()) {

            session()->put('earnerbadgeID', $id);


            // log
            $userid = 'NA';
            $username = 'NA';

            try {

                $userid  = Auth::user()->id;
            } catch (Exception $e) {
            }

            try {

                $username = User::where('id', '=', Auth::user()->id)->pluck('name')[0];
            } catch (Exception $e) {
            }

            $subject = [];
            // Log::info("Earner Badge Edit Page, " . $userid  . ", " . $username . ", " . EarnerBadge::where('earnerbadgeID', '=', $id)->first()->getBadgeNameAttribute() . ", " . $id . ", " . json_encode($subject) . ", " . \Request::ip() . ", " . \Request::userAgent());


            return view('editEarnerBadge')
                ->with('earnerbadge', EarnerBadge::where('earnerbadgeID', '=', $id)->where('status', '=', '1')->get());
        }

        return redirect('earnerMain');
    }

    public function updateEarnerBadgeRecord(Request $request)
    {
        $id = session()->get('earnerbadgeID');

        $thisEarnerbadge = EarnerBadge::where('earnerbadgeID', '=', $id)->where('status', '=', '1');
        if ($thisEarnerbadge->count()) {

            $thisEarnerbadge->update(['description' => $request->earnerbadge_description]);
            // $thisEarnerbadge->update(['recipientType' => $request->recipient_type]);
            $thisEarnerbadge->update(['recipientType' => 'email']);
            $thisEarnerbadge->update(['recipientIdentity' => $request->recipient_identity]);

            $tags = explode(";", $request->earner_badge_tags);

            foreach ($tags as &$tag) {
                $tag = trim($tag);
            }



            $thisEarnerbadge->update(['earnerBadgeTags' => json_encode($tags, JSON_UNESCAPED_UNICODE)]);

            if ($request->has('isSearchable')) {
                $thisEarnerbadge->update(['issearchable' => '1']);
            } else {
                $thisEarnerbadge->update(['issearchable' => '0']);
            }
        }

        session()->forget('earnerbadgeID');
        return redirect('earnerMain');
        //$earnerbadge->description = $request->earnerbadge_description;
    }


    public function recordCheckEarnerBadge($id)
    {
        $earnerbadgepath =  EarnerBadge::where('earnerbadgeID', '=', $id)
            ->where('status', '=', '1')
            ->pluck('earnerbadgepath');


        $myReport = new CheckBadgeFile(ltrim($earnerbadgepath[0], '/public/'));
        $backData = $myReport->getReportOfBadgeFile();


        //ddd($backData);


        $actCheck = new EarnerBadgeCheck();
        $actCheck->earnerbadgeID = $id;
        $actCheck->checkerID = Auth::id();
        if ($backData['summary']) {
            $actCheck->result = "1";
        } else {
            $actCheck->result = "0";
        }

        $actCheck->save();
    }

    public function checkEarnerBadge($id)
    {
        $earnerbadgepath =  EarnerBadge::where('earnerbadgeID', '=', $id)
            ->where('status', '=', '1')
            ->pluck('earnerbadgepath');

        $myReport = new CheckBadgeFile(ltrim($earnerbadgepath[0], '/public/'));
        $backData = $myReport->getReportOfBadgeFile();

        $this->recordCheckEarnerBadge($id);


        // log
        $userid = 'NA';
        $username = 'NA';

        try {

            $userid  = Auth::user()->id;
        } catch (Exception $e) {
        }

        try {

            $username = User::where('id', '=', Auth::user()->id)->pluck('name')[0];
        } catch (Exception $e) {
        }

        $subject = [];
        // Log::info("Earner Badge Check Page, " . $userid  . ", " . $username . ", " . EarnerBadge::where('earnerbadgeID', '=', $id)->first()->getBadgeNameAttribute() . ", " . $id . ", " . json_encode($subject) . ", " . \Request::ip() . ", " . \Request::userAgent());



        return view('checkEarnerBadge')
            ->with('obReport', $backData);
    }


    public function certEarnerBadge($id)
    {
        $thisEarnerbadge = EarnerBadge::where('earnerID', '=', Auth::id())->where('earnerbadgeID', '=', $id)->where('status', '=', '1')->get();

        // A ZD miatt a PDF cert másképpen néz ki :-)
        if ($thisEarnerbadge->first()->get_dcp_badgeclass_id == env('GREENCERT_ID')) {


            $pdf = app('dompdf.wrapper');

            // Dirty magic
            $thisEarnerbadge = $thisEarnerbadge->first();

            $assertionType = json_decode($thisEarnerbadge->get_assertion_type, true);

            // Collecting data for Green Cert

            //ddd($assertionType['form'][0]['fields'][25]['load'][0]['content']);

            //ddd($assertionType['form'][0]['fields'][0]['load'][0]['content']);

            $variableA = [


                // Név
                'Név' => $assertionType['form'][0]['fields'][0]['load'][0]['content'],


                // Sorszám
                'Sorszám' => $assertionType['form'][0]['fields'][2]['load'],

                // Születési dátum
                'Születési dátum' => $assertionType['form'][0]['fields'][3]['load'],

                // Születési hely 
                'Születési hely magyarul' => $assertionType['form'][0]['fields'][4]['load'][1]['content'],
                'Születési hely angolul' => $assertionType['form'][0]['fields'][4]['load'][0]['content'],

                // Születési hely jellege
                'Születési hely jellege magyarul, ragozva' => $assertionType['form'][0]['fields'][5]['load'][1]['content'],
                'Születési hely jellege angolul' => $assertionType['form'][0]['fields'][5]['load'][0]['content'],

                // Születési térség
                'Születési térség magyarul' => $assertionType['form'][0]['fields'][6]['load'][1]['content'],
                'Születési térség angolul' => $assertionType['form'][0]['fields'][6]['load'][0]['content'],

                // Születési térség jellege
                'Születési térség jellege magyarul, ragozva' => $assertionType['form'][0]['fields'][7]['load'][1]['content'],
                'Születési térség jellege angolul' => $assertionType['form'][0]['fields'][7]['load'][0]['content'],

                // Születési ország
                'Születési ország magyarul, ragozva' => $assertionType['form'][0]['fields'][8]['load'][1]['content'],
                'Születési ország angolul' => $assertionType['form'][0]['fields'][8]['load'][0]['content'],

                //** */

                // Keltezés
                '_date_issued_hu' => $assertionType['form'][0]['fields'][25]['load'][1]['content'],
                '_date_issued_en' => $assertionType['form'][0]['fields'][25]['load'][0]['content'],


            ];


            $variableB = [
                // Név
                'Név' => $assertionType['form'][0]['fields'][0]['load'][0]['content'],

                // Sorszám
                'Sorszám' => $assertionType['form'][0]['fields'][2]['load'],

                // Neptun kód
                'Neptun kód' => $assertionType['form'][0]['fields'][1]['load'],

                // 01 tantárgy és adatai
                '01course' => $assertionType['form'][0]['fields'][9]['load'],

                // 01 tantárgy TAD hivatkozás
                '_c1sublink' => $assertionType['form'][0]['fields'][10]['load'],


                // 02 tantárgy és adatai
                '02course' => $assertionType['form'][0]['fields'][11]['load'],

                // 02 tantárgy TAD hivatkozás
                '_c2sublink' => $assertionType['form'][0]['fields'][12]['load'],


                // 03 tantárgy és adatai
                '03course' => $assertionType['form'][0]['fields'][13]['load'],

                // 03 tantárgy TAD hivatkozás
                '_c3sublink' => $assertionType['form'][0]['fields'][14]['load'],



                // 04 tantárgy és adatai
                '04course' => $assertionType['form'][0]['fields'][15]['load'],

                // 04 tantárgy TAD hivatkozás
                '_c4sublink' => $assertionType['form'][0]['fields'][16]['load'],


                // 05 tantárgy és adatai
                '05course' => $assertionType['form'][0]['fields'][17]['load'],

                // 05 tantárgy TAD hivatkozás
                '_c5sublink' => $assertionType['form'][0]['fields'][18]['load'],


                // 06 tantárgy és adatai
                '06course' => $assertionType['form'][0]['fields'][19]['load'],


                // 06 tantárgy TAD hivatkozás
                '_c6sublink' => $assertionType['form'][0]['fields'][20]['load'],


                // 07 tantárgy és adatai
                '07course' => $assertionType['form'][0]['fields'][21]['load'],


                // 07 tantárgy TAD hivatkozás
                '_c7sublink' => $assertionType['form'][0]['fields'][22]['load'],

                // 08 tantárgy és adatai
                '08course' => $assertionType['form'][0]['fields'][23]['load'],


                // 08 tantárgy TAD hivatkozás
                '_c8sublink' => $assertionType['form'][0]['fields'][24]['load'],


                // Keltezés
                '_date_issued_hu' => $assertionType['form'][0]['fields'][25]['load'][0]['content'],
                '_date_issued_en' => $assertionType['form'][0]['fields'][25]['load'][1]['content'],


            ];

            //ddd($variableB);

              // Get HTML content from Blade views for each page
            /*            $pageA = View::make('earnerGreenCertPrintA', ['variableA' => $variableA])->render();
            $pageB = View::make('earnerGreenCertPrintB', ['variableB' => $variableB])->render();


            $htmlPages = "<!doctype html><html><head><meta charset=\"utf-8\"><title>Certificate print</title>
            <style> * { font-family: DejaVu Sans, sans-serif; }</style>
            </head><body>{$pageA}<div style='page-break-after: always;'></div>{$pageB}</body></html>";*/


            //	    $htmlPages = View::make('earnerGreenCertPrint', ['variableA' => $variableA], ['variableB' => $variableB])->render();
            $greenCertBgImageA = Storage::get('/public/green_certificate_template_p1.png');
            $greenCertBgImageB = Storage::get('/public/green_certificate_template_p2.png');
            $htmlPages = View::make('earnerGreenCertPrint', ['variableA' => $variableA, 'variableB' => $variableB, 'greenCertBgImageA' => $greenCertBgImageA, 'greenCertBgImageB' => $greenCertBgImageB])->render();

            $pdf->loadHtml($htmlPages);


            $pdf->setPaper('a4', 'landscape');



            return $pdf->download('greencert_' . $id . '.pdf');
            //	    return $htmlPages;


            //ddd('GreenCert');

            /* $user = User::where('id', '=', $thisEarnerbadge->pluck('earnerID'))->first();

            $discoCertTemplate = Storage::get('/public/certificate_template.svg');

            $earnerbadge =  EarnerBadge::where('earnerbadgeID', '=', $id)->get();
            $badgeImage = Storage::get($earnerbadge[0]['earnerbadgepath']);
            $discovetImage = Storage::get('/public/discovet_long_header.png');
            // Added 2022-06-20 by RG
            $discovetLogo = Storage::get('/public/discovet_logo.png');
            $certBgImageJPG = Storage::get('/public/certificate_template.jpg');
            $certBgImagePNG = Storage::get('/public/certificate_template.png');
            // Added 2022-06-20 by RG

            //ddd($earnerbadge->first()->badge_issuedon);

            $earnerBadgeURL = env('APP_URL') . '/earnerbadges/' . $earnerbadge[0]['earnerbadgeID'] . '/badge';

            $qrCode = QrCode::size(500)
                ->format('png')
                ->backgroundColor(255, 255, 255)
                ->generate($earnerBadgeURL);


            $pdf = app('dompdf.wrapper');
            $pdf->loadView(
                'earnerGreenCertPrint',
                [
                    'discoCertTemplate' =>  $discoCertTemplate,
                    'user' => $user,
                    'badgeImage' => $badgeImage,
                    'qrCode' => $qrCode,
                    'discovetImage' => $discovetImage,
                    // Added 2022-06-20 by RG
                    'discovetLogo' => $discovetLogo,
                    'certBgImageJPG' => $certBgImageJPG,
                    'certBgImagePNG' => $certBgImagePNG,
                    // Added 2022-06-20 by RG
                    'thisBadge' => $thisEarnerbadge,

                ]
            )->setPaper('a4', 'landscape');

            // Added 2022-06-21 by RG
            //	    $pdf->setOptions(['debugCSS' => 'true']);
            // Added 2022-06-21 by RG

            return $pdf->download('greencert_' . $id . '.pdf'); */
        } else {



            if ($thisEarnerbadge->count()) {



                $user = User::where('id', '=', $thisEarnerbadge->pluck('earnerID'))->first();

                $discoCertTemplate = Storage::get('/public/certificate_template.svg');

                $earnerbadge =  EarnerBadge::where('earnerbadgeID', '=', $id)->get();
                $badgeImage = Storage::get($earnerbadge[0]['earnerbadgepath']);
                $discovetImage = Storage::get('/public/discovet_long_header.png');
                // Added 2022-06-20 by RG
                $discovetLogo = Storage::get('/public/discovet_logo.png');
                $certBgImageJPG = Storage::get('/public/certificate_template.jpg');
                $certBgImagePNG = Storage::get('/public/certificate_template.png');
                // Added 2022-06-20 by RG

                //ddd($earnerbadge->first()->badge_issuedon);

                $earnerBadgeURL = env('APP_URL') . '/earnerbadges/' . $earnerbadge[0]['earnerbadgeID'] . '/badge';

                $qrCode = QrCode::size(500)
                    ->format('png')
                    ->backgroundColor(255, 255, 255)
                    ->generate($earnerBadgeURL);


                $pdf = app('dompdf.wrapper');
                $pdf->loadView(
                    'earnerBadgeCertPrint',
                    [
                        'discoCertTemplate' =>  $discoCertTemplate,
                        'user' => $user,
                        'badgeImage' => $badgeImage,
                        'qrCode' => $qrCode,
                        'discovetImage' => $discovetImage,
                        // Added 2022-06-20 by RG
                        'discovetLogo' => $discovetLogo,
                        'certBgImageJPG' => $certBgImageJPG,
                        'certBgImagePNG' => $certBgImagePNG,
                        // Added 2022-06-20 by RG
                        'thisBadge' => $thisEarnerbadge,

                    ]
                )->setPaper('a4', 'landscape');

                // Added 2022-06-21 by RG
                //	    $pdf->setOptions(['debugCSS' => 'true']);
                // Added 2022-06-21 by RG

                return $pdf->download('cert_' . $id . '.pdf');
            }
        }
        return redirect('earnerMain');
    }

    public function shareByEmail($id)
    {
        if ($thisEarnerbadge = EarnerBadge::where('earnerID', '=', Auth::id())->where('earnerbadgeID', '=', $id)->where('status', '=', '1')->first()) {

            $backData = unserialize($thisEarnerbadge->cachedBadgeReport);

            session()->put('actBadgeToSendByEmail', $thisEarnerbadge->earnerbadgeID);

            //ddd($backData);

            return view('earnerBadgeShareByEmail')
                ->with('thisEarnerbadge', $thisEarnerbadge)
                ->with('backData', $backData);
        }

        return redirect('earnerMain');
    }

    public function doShareByEmail(Request $request)
    {
        $isError = false;
        $errorMesage[] = 'Error!';

        $isValid = Validator::make($request->all(), [
            'user_email_text' => 'required|email'
        ]);

        if ($isValid->fails()) {
            $errorMesage[] = 'Given e-mail address ( ' . $request->user_email_text . ' ) is not valid, choose other one!';
            $isError = true;
        }

        if ($isError) {
            return back()
                ->withErrors($errorMesage);
        } else {
            //ddd(EarnerBadge::where('earnerbadgeID', '=', (session()->get('actBadgeToSendByEmail')))->first());
            if (($thisUser = User::where('id', '=', Auth::id())->first()) && ($thisEarnerBadge = EarnerBadge::where('earnerbadgeID', '=', (session()->get('actBadgeToSendByEmail')))->first())) {
                $backData = unserialize($thisEarnerBadge->cachedBadgeReport);
                $details = [

                    'name' => $thisUser->name,
                    'email' => $thisUser->email,
                    'receiverEmail' => $request->user_email_text,
                    'subject' => $thisUser->name . ' sent you a badge',
                    'badgeName' => $backData['checkOB']['badgeClass']['name'],
                    'earnerbadgepath' => $thisEarnerBadge->earnerbadgepath,
                    'earnerbadgeID' => session()->get('actBadgeToSendByEmail'),

                ];

                //ddd($details);
                //return ('Sending...');
                Mail::to($request->user_email_text)->send(new EarnerBadgeMail($details));
            }

            return redirect('earnerMain');
        }
    }

    public function printEarnerBadge($id)
    {
        $thisEarnerbadge = EarnerBadge::where('earnerID', '=', Auth::id())->where('earnerbadgeID', '=', $id)->where('status', '=', '1');

        if ($thisEarnerbadge->count()) {

            //session()->put('earnerbadgeID', $id);

            // ddd(session()->all());

            $user = User::where('id', '=', session()->get('userID'))->first();
            //$user = User::where('id', '=', '1');
            //ddd($user);

            $earnerbadge =  EarnerBadge::where('earnerbadgeID', '=', $id)->get();

            $badgeImage = Storage::get($earnerbadge[0]['earnerbadgepath']);



            $discovetImage = Storage::get('/public/discovet_long_header.png');



            $memo = $earnerbadge[0]['description'];

            //$myReport = new CheckBadgeFile(ltrim($earnerbadge[0]['earnerbadgepath'], '/public/'));
            //$backData = $myReport->getReportOfBadgeFile();


            $thisBadge = EarnerBadge::where('earnerbadgeID', '=', $id)->where('status', '=', '1')->first();
            $backData = unserialize($thisBadge->cachedBadgeReport);


            $assertionURL = 'NA';
            $badgeclassURL = 'NA';
            $issuedOn = 'NA';
            $badgeClass = [
                'name' => 'NA',
                'description' => 'NA',
            ];
            $issuer = [
                'name' => 'NA',
                'url' => 'NA',
                'email' => 'NA',
                'image' => 'NA',
            ];

            if ($backData['summary']) {

                //ddd($backData['checkOB']);

                $assertionURL = $backData['checkOB']['id'];
                $badgeclassURL = $backData['checkOB']['badge'];
                $issuedOn = $backData['checkOB']['issuedOn'];


                if (is_numeric($issuedOn)) {
                    $issuedOn = gmdate("Y-m-d\TH:i:s\Z", $issuedOn);
                } else {
                    $issuedOn = gmdate("Y-m-d\TH:i:s\Z", strtotime($issuedOn));
                }


                /*   try {
                    if (checkdate(date('m', $issuedOn), date('d', $issuedOn), date('Y', $issuedOn))) {
                        $issuedOn = gmdate("Y-m-d\TH:i:s\Z", $issuedOn);
                    }
                } catch (Exception $e) {
                    //ddd('Hello');
                } */


                $badgeClass = $backData['checkOB']['badgeClass'];
                $issuer = $backData['checkOB']['issuer'];

                if (!array_key_exists('image', $backData['checkOB']['issuer'])) {
                    $issuer['image'] = env('APP_URL') . '/no_image.png';
                }

                //ddd($backData['checkOB']);
            }

            //ddd($backData);



            $earnerBadgeURL = env('APP_URL') . '/earnerbadges/' . $earnerbadge[0]['earnerbadgeID'] . '/badge';

            $qrCode = QrCode::size(500)
                ->format('png')
                ->backgroundColor(255, 255, 255)
                ->generate($earnerBadgeURL);


            $tags = json_decode($earnerbadge[0]['earnerBadgeTags']);

            $pdf = app('dompdf.wrapper');
            // $pdf->loadView('printEarnerBadge', [
            $pdf->loadView('earnerBadgeDetailPrint', [
                'earnerbadge' =>  $earnerbadge,
                'image' => $badgeImage,
                'discovetImage' => $discovetImage,
                'user' => $user,
                'qrcode' => $qrCode,
                'earnerBadgeURL' => $earnerBadgeURL,
                'assertionURL' => $assertionURL,
                'badgeclassURL' => $badgeclassURL,

                'memo' => $memo,
                'earnerBadgeTags' => $tags,
                'issuedOn' => $issuedOn,

                'printEarnerBadge' => [

                    'user' => $user,
                    'badgeClass' => $badgeClass,
                    'issuer' => $issuer,

                ]

            ]);

            // log
            $userid = 'NA';
            $username = 'NA';

            try {

                $userid  = Auth::user()->id;
            } catch (Exception $e) {
            }

            try {

                $username = User::where('id', '=', Auth::user()->id)->pluck('name')[0];
            } catch (Exception $e) {
            }

            $subject = [];
            // Log::info("Earner Badge Print Page, " . $userid  . ", " . $username . ", " . $thisBadge->getBadgeNameAttribute() . ", " . $id . ", " . json_encode($subject) . ", " . \Request::ip() . ", " . \Request::userAgent());


            return $pdf->download($id . '.pdf');

            // For preview, soon
            $qrCode = '';
            return view('printEarnerBadge')
                ->with('earnerbadge', $earnerbadge)
                ->with('image', $badgeImage)
                ->with('qrcode', $qrCode);
        }

        return redirect('earnerMain');
    }

    public function infoEarnerBadge($id)
    {

        //$thisEarnerbadge = EarnerBadge::where('earnerID', '=', Auth::id())->where('earnerbadgeID', '=', $id)->where('status', '=', '1');

        $thisEarnerbadge = EarnerBadge::where('earnerbadgeID', '=', $id)->where('status', '=', '1');
        //ddd($thisEarnerbadge);

        if ($thisEarnerbadge->count()) {

            $user = User::where('id', '=', $thisEarnerbadge->pluck('earnerID'))->first();
            $earnerbadge =  EarnerBadge::where('earnerbadgeID', '=', $id)->where('status', '=', '1')->get();
            $badgeImage = Storage::get($earnerbadge[0]['earnerbadgepath']);


            $thisBadge = EarnerBadge::where('earnerbadgeID', '=', $id)->where('status', '=', '1')->first();
            $backData = unserialize($thisBadge->cachedBadgeReport);

            //ddd($backData);

            /*  $myReport = new CheckBadgeFile(ltrim($earnerbadge[0]['earnerbadgepath'], '/public/'));
            $backData = $myReport->getReportOfBadgeFile();

            $thisBadge = EarnerBadge::where('earnerbadgeID', '=', $id)->where('status', '=', '1')->first();
            $thisBadge->cachedBadgeReport = serialize($backData);
            //ddd(serialize($backData));
            $thisBadge->save(); */

            //ddd(unserialize($thisBadge->cachedBadgeReport));

            $memo = $earnerbadge[0]['description'];

            $assertionURL = 'NA';
            $badgeclassURL = 'NA';
            $issuedOn = 'NA';
            $badgeClass = [
                'name' => 'NA',
                'description' => 'NA',
            ];
            $issuer = [
                'name' => 'NA',
                'url' => 'NA',
                'email' => 'NA',
                'image' => 'NA',
            ];

            if ($backData['summary']) {

                //ddd($backData['checkOB']);

                $assertionURL = $backData['checkOB']['id'];
                $badgeclassURL = $backData['checkOB']['badge'];
                $issuedOn = $backData['checkOB']['issuedOn'];




                if (is_numeric($issuedOn)) {
                    $issuedOn = gmdate("Y-m-d\TH:i:s\Z", $issuedOn);
                } else {
                    $issuedOn = gmdate("Y-m-d\TH:i:s\Z", strtotime($issuedOn));
                }

                /*   try {
                    if (checkdate(date('m', $issuedOn), date('d', $issuedOn), date('Y', $issuedOn))) {
                        $issuedOn = gmdate("Y-m-d\TH:i:s\Z", $issuedOn);
                    }
                } catch (Exception $e) {
                    //ddd('Hello');
                } */


                $badgeClass = $backData['checkOB']['badgeClass'];
                $issuer = $backData['checkOB']['issuer'];

                if (!array_key_exists('image', $backData['checkOB']['issuer'])) {

                    $issuer['image'] = false;
                }

                //ddd($backData['checkOB']);
            }

            $lastcheckingtime = EarnerBadgeCheck::where('earnerbadgeID', '=', $id)->max('created_at');



            // Szalma Éva kérésére ez /badge-ről /info-ra módosult, 20240610
            $earnerBadgeURL = env('APP_URL') . '/earnerbadges/' . $earnerbadge[0]['earnerbadgeID'] . '/info';
            $earnerBadgeURL2 = env('APP_URL') . '/earnerbadges/' . $earnerbadge[0]['earnerbadgeID'] . '/badge';

            $earnerBadgeEmbed = env('APP_URL') . '/earnerbadges/' . $earnerbadge[0]['earnerbadgeID'] . '/badge';


            $earnerBadgeCriteriaURL = false;
            $earnerBadgeEvidenceURL = false;

            if (array_key_exists('evidence', $backData['checkOB'])) {
                $earnerBadgeEvidenceURL = $backData['checkOB']['evidence'];
            }

            if (array_key_exists('criteria', $backData['checkOB']['badgeClass'])) {

                if (is_array($backData['checkOB']['badgeClass']['criteria'])) {

                    try {
                        $earnerBadgeCriteriaURL = $backData['checkOB']['badgeClass']['criteria']['id'];
                    } catch (Exception $e) {
                    }
                } else {

                    $earnerBadgeCriteriaURL  = $backData['checkOB']['badgeClass']['criteria'];
                }
            }

            //ddd($backData['checkOB']);
            //ddd($backData['checkOB']['badgeClass']['id']);


            $qrCode = QrCode::size(500)
                ->format('png')
                ->backgroundColor(255, 255, 255)
                ->generate($assertionURL);

            $recipientValid = false;
            // ddd($thisBadge->cachedBadgeReport);


            try {

                $recipientBlock = json_decode(unserialize($thisBadge->cachedBadgeReport)['OBpart'], true)['recipient'];

                if ($recipientBlock['hashed']) {

                    if (!array_key_exists('salt', $recipientBlock)) {
                        $recipientBlock['salt'] = '';
                    }

                    $identity = "sha256$" . hash('sha256', ($thisBadge->recipientIdentity) . ($recipientBlock['salt']));

                    if ($identity == $recipientBlock['identity']) {
                        $recipientValid = true;
                    }
                } else {
                    return redirect('earnerMain');
                }
            } catch (Exception $e) {

                $recipientBlock = unserialize($thisBadge->cachedBadgeReport)['checkOB']['recipient'];
                if ($recipientBlock['hashed']) {

                    if (!array_key_exists('salt', $recipientBlock)) {
                        $recipientBlock['salt'] = '';
                    }


                    $identity = "sha256$" . hash('sha256', ($thisBadge->recipientIdentity) . ($recipientBlock['salt']));

                    if ($identity == $recipientBlock['identity']) {
                        $recipientValid = true;
                    }
                } else {
                    return redirect('earnerMain');
                }
            }
            //ddd(unserialize($thisBadge->cachedBadgeReport)['checkOB']);
            //ddd(json_decode(unserialize($thisBadge->cachedBadgeReport)['OBpart'], true)['evidence']);





            //ddd(json_decode(unserialize($thisBadge->cachedBadgeReport)['OBpart'], true)['recipient']);

            // ddd(\Request::fingerprint());

            $tags = json_decode($earnerbadge[0]['earnerBadgeTags']);

            /// ddd($tags);


            // log
            $userid = 'NA';
            $username = 'NA';

            try {

                $userid  = Auth::user()->id;
            } catch (Exception $e) {
            }

            try {

                $username = User::where('id', '=', Auth::user()->id)->pluck('name')[0];
            } catch (Exception $e) {
            }

            $subject = [];
            // Log::info("Earner Info Badge Page, " . $userid  . ", " . $username . ", " . $thisBadge->getBadgeNameAttribute() . ", " . $id . ", " . json_encode($subject) . ", " . \Request::ip() . ", " . \Request::userAgent());


            //ddd($thisBadge->dcp_badge_evidence_url);

            //ddd($badgeImage);

            $filetypeOfBadge = '';

            switch ($thisBadge->filetype_of_badge) {
                case 'PNG':
                    $filetypeOfBadge = 'png';
                    break;

                case 'SVG':
                    $filetypeOfBadge = 'svg+xml';
                    break;

                default:
                    # code...
                    break;
            }

            //ddd($badgeClass);

            return view('earnerBadgeInfopage')
                ->with('image', $badgeImage)
                ->with('filetypeOfBadge', $filetypeOfBadge)
                ->with('qrcode', $qrCode)
                ->with('issuedOn', $issuedOn)
                ->with('memo', $memo)
                ->with('earnerBadgeTags', $tags)
                ->with('assertionURL', $assertionURL)
                ->with('earnerBadgeCriteriaURL', $earnerBadgeCriteriaURL)
                ->with('earnerBadgeEvidenceURL', $earnerBadgeEvidenceURL)
                ->with('badgeclassURL', $badgeclassURL)
                ->with('earnerBadgeURL', $earnerBadgeURL)
                ->with('earnerBadgeURL2', $earnerBadgeURL2)
                ->with('earnerBadgeEmbed', $earnerBadgeEmbed)
                ->with('lastcheckingtime', $lastcheckingtime)
                ->with('recipientType', $thisBadge->recipientType)
                ->with('recipientIdentity', $thisBadge->recipientIdentity)
                ->with('recipientValid', $recipientValid)
                ->with('checkedOB', $backData['summary'])
                ->with('thisBadge', $thisBadge)
                ->with('printEarnerBadge', [

                    'user' => $user,
                    'badgeClass' => $badgeClass,
                    'issuer' => $issuer,

                ]);
        }

        return redirect('earnerMain');
    }

    public function earnerBadgeToHTML($id)
    {

        return view('earnerBadgeToHTML');
    }

    public function searchForEarnerBadge(Request $request)
    {
        $searchItems = explode(" ", $request->search);

        // !!
        $foundBadges = EarnerBadge::where('earnerID', '=', Auth::id())->where('status', '=', '1')
            ->where(function ($q) use ($searchItems) {
                collect($searchItems)->each(function ($keyword) use ($q) {
                    $q->orWhere('earnerBadgeTags', 'like', '%' . $keyword . '%');
                });
            })->get();

        //ddd($foundBadges->first()->badge_description);

        $foundBadgesByName = collect();
        $foundBadgesByDescription = collect();
        $foundBadgesByTags = collect();

        $allEarnerBadges = EarnerBadge::where('earnerID', '=', Auth::id())->where('status', '=', '1')->get();

        //ddd($allEarnerBadges->count());
        //ddd($allEarnerBadges->first()->badge_name);



        foreach ($allEarnerBadges as $badge) {

            foreach ($searchItems as $keyword) {

                if (str_contains(strtolower($badge->badge_name), strtolower($keyword))) {
                    $foundBadgesByName->add($badge);
                }

                if (str_contains(strtolower($badge->badge_description), strtolower($keyword))) {
                    $foundBadgesByDescription->add($badge);
                }

                foreach (array_map('strtolower', $badge->badge_tags) as $badgetag) {
                    if (str_contains($badgetag, strtolower($keyword))) {
                        $foundBadgesByTags->add($badge);
                    }
                }
            }




            /*  $result = array_intersect(array_map('strtolower', $badge->badge_tags), array_map('strtolower', $searchItems));
            if (sizeof($result)) {
                $foundBadgesByTags->add($badge);
            } */
        }

        // $foundBadgesByName = $foundBadgesByName->unique()->all();
        // $foundBadgesByDescription = $foundBadgesByDescription->unique()->all();
        // $foundBadgesByTags = $foundBadgesByTags->unique()->all();


        //ddd($foundBadges->merge($foundBadgesByName)->merge($foundBadgesByDescription)->merge($foundBadgesByTags));

        //$foundBadges->merge($foundBadgesByName)->merge($foundBadgesByDescription)->merge($foundBadgesByTags);
        //$foundBadges->merge($foundBadgesByName);
        // ddd($foundBadgesByName->merge($foundBadgesByDescription));
        //$foundBadges = $foundBadges->unique()->all();

        $summaBadges = $foundBadges->merge($foundBadgesByName)->merge($foundBadgesByDescription)->merge($foundBadgesByTags);

        //ddd($summaBadges);

        $backData_array = [];
        foreach ($summaBadges as $badge) {

            $backData = unserialize($badge->cachedBadgeReport);

            $backData_array[] = $backData;
        }

        //ddd($backData_array);

        //ddd($summaBadges->first()->badge_name);

        return view('earnerBadgeSearchResultPage')
            ->with('resultBadges', $summaBadges);
    }
}

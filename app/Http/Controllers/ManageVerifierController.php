<?php

namespace App\Http\Controllers;

use App\DCP\CheckBadgeFile;
use App\Models\Assertion;
use App\Models\Badgeclass;
use App\Models\VerifierBadge;
use App\Models\EarnerBadge;
use App\Models\EarnerBadgeCheck;
use App\Models\Issuer;
use App\Models\User;
use App\Models\VerifierEndorsement;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManageVerifierController extends Controller
{
    public function showVerifierDashboard()
    {

        session()->forget('isVerifierRole');
        session()->forget('earnerBadgeDCPs');


        return view('verifierBadgeboard')
            ->with('numberofverifierbadges', VerifierBadge::where('verifierID', '=', Auth::id())->where('status', '=', '1')->count())
            ->with('verifierbadges', VerifierBadge::where('verifierID', '=', Auth::id())->where('status', '=', '1')->get());
    }

    public function showVerifierBadgePage()
    {

        return view('addVerifierBadge');
    }


    public function doVerifierBadgeRecord(Request $request)
    {
        $isValid = Validator::make($request->all(), [
            'file' => 'required|mimes:png'
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


                if ($backData['summary']) {


                    $filePath2 = $request->file('file')->storeAs('verifierbadges', $fileName, 'public');

                    $verifierbadge = new VerifierBadge;

                    // Ez itt a file leíró adatok alapján állít elő hash-t, nem a tartalom alapján!

                    $fileContent = Storage::get('/public/' . $filePath2);


                    $verifierbadgecontenthash = hash('sha256', $fileContent);

                    $nowTime = time();
                    $salt = hash('sha256', random_bytes(128) . $nowTime);
                    $identity = "sha256$" . hash('sha256', $verifierbadgecontenthash  . ($salt));

                    $verifierbadge->verifierID = Auth::id();
                    $verifierbadge->verifierbadgeID = $identity;
                    $verifierbadge->verifierbadgecontenthash = $verifierbadgecontenthash;
                    $verifierbadge->verifierbadgepath = '/public/' . $filePath2;
                    $verifierbadge->description = $request->verifierbadge_description;

                    $verifierbadge->status = '1';

                    $verifierbadge->save();

                    return back()
                        ->with('success', __('hello.verifierbadge_adding_success'))
                        ->with('id', $backData['checkOB']['id'])
                        ->with('badge', $backData['checkOB']['badge'])
                        ->with('issuedOn', $backData['checkOB']['issuedOn']);
                } else {

                    //ddd($backData);

                    return back()
                        ->withErrors(['This is not a valid open badge!']);
                }
            }
            return back()
                ->withErrors('General error');
        }
    }

    public function deleteVerifierBadge($id)
    {

        //ddd(EarnerBadge::where('earnerID', '=', Auth::id())->where('earnerbadgeID', '=', $id)->where('status', '=', '1')->count());
        $thisVerifierbadge = VerifierBadge::where('verifierID', '=', Auth::id())->where('verifierbadgeID', '=', $id)->where('status', '=', '1');
        if ($thisVerifierbadge->count()) {

            $thisVerifierbadge->update(['status' => '0']);
        }

        return redirect('verifierMain');
    }

    public function lookupEarnerBadges()
    {

        session()->put('isVerifierRole', true);

        return view('lookupEarnerBadges')
            ->with('numberoflookedupearnerbadges', EarnerBadge::where('issearchable', '=', '1')->where('status', '=', '1')->count())
            ->with('earnerbadges', EarnerBadge::where('issearchable', '=', '1')->where('status', '=', '1')->orderBy('created_at', 'DESC')->get());
    }

    public function infoVerifierBadge($id)
    {



        if ($thisVerifierBadge = VerifierBadge::where('verifierbadgeID', '=', $id)->where('status', '=', '1')->first()) {

            //ddd($thisVerifierBadge->badge_class);

            return $thisVerifierBadge->verifierbadgeID;
        }


        return view('verifierBadgeInfopage');
    }

    public function recordVerifierEndorsement(Request $request)
    {

        // ddd(session()->all());
        $verifierID = session()->get('userID');
        $verifierRating = $request->get('verifierRating');
        $verifier_endorsement_message = $request->get('verifier_endorsement_message');
        $actEarnerbadgeID = session()->get('actEarnerbadgeID');
        $thisVerifierEndorsement = new VerifierEndorsement;

        $thisVerifierEndorsement->verifierID = $verifierID;
        $thisVerifierEndorsement->earnerbadgeID = $actEarnerbadgeID;
        $thisVerifierEndorsement->rating = $verifierRating;
        $thisVerifierEndorsement->verifierMessage = htmlspecialchars($verifier_endorsement_message);

        $thisVerifierEndorsement->status = "1";

        $thisVerifierEndorsement->save();

        session()->forget('actEarnerbadgeID');

        return redirect(env('APP_URL') . '/verifierMain');
    }

    public function deleteVerifierEndorsement(Request $request)
    {

        $verifierID = session()->get('userID');
        $earnerbadgeID =  session()->get('actEarnerbadgeID');

        if ($thisVerifierEndorsement = VerifierEndorsement::where('status', '=', '1')
            ->where('verifierID', '=', $verifierID)
            ->where('earnerbadgeID', '=', $earnerbadgeID)->first()
        ) {
            $thisVerifierEndorsement->status = "0";
            $thisVerifierEndorsement->save();
        }

        session()->forget('actEarnerbadgeID');
        return redirect(env('APP_URL') . '/verifierMain');
    }

    public function searchForVET()
    {

        $earnerBadgeDCPs = EarnerBadge::where('issearchable', '=', '1')->where('status', '=', '1')->orderBy('created_at', 'DESC')->get()->filter(function ($query) {
            return $query->is_dcp_badge;
        });

        $assertionTypeNames = [];

        //ddd($earnerBadgeDCPs->first()->get_assertion_type_a_s);

        foreach ($earnerBadgeDCPs as $key => $item) {

            if ($typeNames = $item->get_assertion_type_a_s) {

                foreach ($typeNames as $key => $typeName) {

                    $assertionTypeNames[] = $typeName;
                }
            }
        }

        $typenamesCount = array_count_values($assertionTypeNames);

        //ddd($typenamesCount);

        session()->put('earnerBadgeDCPs', $earnerBadgeDCPs);

        return view('verifierSearchForVET')
            ->with('earnerBadgeDCPs', $earnerBadgeDCPs)
            ->with('typenamesCount', $typenamesCount);
    }

    public function startSearchForVET(Request $request)
    {

        $isError = false;
        $errorMesage[] = 'Error!';


        if (!is_null($typeNames = $request->get('typename'))) {
            //ddd($typenames);
            $earnerBadgeDCPs = session()->get('earnerBadgeDCPs');

            $foundEarnerBadges = [];

            foreach ($earnerBadgeDCPs as $key => $earnerBadge) {
                if ($badgeTypeNames = $earnerBadge->get_assertion_type_a_s) {

                    foreach ($typeNames as $key => $typeName) {

                        foreach ($badgeTypeNames as $key => $badgeTypeName) {

                            if ($badgeTypeName == $typeName) {
                                $foundEarnerBadges[$typeName][] = $earnerBadge;
                            }
                        }
                    }
                }
            }
        } else {
            $errorMesage[] = 'Choose at least one category!';
            return back()
                ->withErrors($errorMesage);
        }

        //ddd($foundEarnerBadges);
        return view('verifierResultPageForVET')
            ->with('foundEarnerBadges', $foundEarnerBadges);
    }

    public function lookupIssuers()
    {

        $issuersID = Issuer::select('issuerID')->get()->flatten(1)->toArray();

        $IssuersAndBadgeclasses = Badgeclass::select(['issuerID', 'badgeclassID'])->where(function ($item) use ($issuersID) {
            $item->whereIn('issuerID', $issuersID);
        })->get()->groupBy('issuerID');


        $countOfBadgeclasses = $IssuersAndBadgeclasses->map(function ($item) {
            return $item->count();
        });

        $countOfAssertions = $IssuersAndBadgeclasses->map(function ($item) {
            return $item->map(function ($item2) {
                return [$item2->badgeclassID, Assertion::where('badgeclassID', '=', $item2->badgeclassID)->count()];
            });
        });

        $countOfAssertions = $IssuersAndBadgeclasses->map(function ($item) {

            return array_sum($item->map(function ($item2) {

                return Assertion::where('badgeclassID', '=', $item2->badgeclassID)->count();
            })->toArray());
        });


        return view('verifierLookupIssuers')
            ->with('countOfBadgeclasses', $countOfBadgeclasses->toArray())
            ->with('countOfAssertions', $countOfAssertions->toArray());
    }

    public function showIssuerBadgeclasses($id)
    {
        if ($issuer = Issuer::where('issuerID', '=', $id)->first()) {
            $issuerBadgeclasses = Badgeclass::where('issuerID', '=', $id)->orderBy('created_at', 'DESC')->get();


            $onlyBadgeclassIDs = $issuerBadgeclasses->map->only(['badgeclassID'])->flatten(1)->toArray();


            $assertionsFromIssuer = Assertion::where(function ($item) use ($onlyBadgeclassIDs) {
                $item->whereIn('badgeclassID', $onlyBadgeclassIDs);
            })->get();



            /*   $assertionsCount = $assertionsFromIssuer->groupBy('badgeclassID')->map(
                function ($item) {
                    return $item->count();
                }
            )->toArray(); */

            $issuersID = Issuer::select('issuerID')->where('issuerID', '=', $id)->get()->flatten(1)->toArray();

            $IssuersAndBadgeclasses = Badgeclass::select(['issuerID', 'badgeclassID'])->where(function ($item) use ($issuersID) {
                $item->whereIn('issuerID', $issuersID);
            })->get()->groupBy('issuerID');


            $countOfAssertions = $IssuersAndBadgeclasses->map(function ($item) {

                return $item->map(function ($item2) {

                    return [$item2->badgeclassID, Assertion::where('badgeclassID', '=', $item2->badgeclassID)->count()];
                })->toArray();
            })->first();

            session()->put('issuerID', $id);

            $tempArray = [];

            foreach ($countOfAssertions as  $item) {
                $tempArray[$item[0]] = $item[1];
            }


            //ddd($tempArray);

            return view('verifierShowIssuerBadgeclasses')
                ->with('issuer', $issuer)
                ->with('issuerBadgeclasses', $issuerBadgeclasses)
                ->with('countOfAssertions', $tempArray);
        }



        return redirect(env('APP_URL') . '/verifierMain');
    }
}

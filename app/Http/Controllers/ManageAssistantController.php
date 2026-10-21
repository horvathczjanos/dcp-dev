<?php

namespace App\Http\Controllers;

use App\Models\Assertion;
use App\Models\Badgeclass;
use App\Models\Badgeimage;
use App\Models\Issuer;
use App\Models\Preassertion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ManageAssistantController extends Controller
{
    public function showIssuingCompanies()
    {
        $issuers = Issuer::all()->filter(function ($value, $key) {
            if (in_array(Auth::id(), json_decode($value['assistantID']))) {

                return true;
            }
        });

        if ($issuers->count()) {

            return view('assistantMain')
                ->with('companies', $issuers);
        }

        return ('<h1>You have not got Issuing Companies!</h1>');
    }

    public function showCompany($id)
    {

        session()->forget('issuerBadgeclassID');
        session()->forget('assertionID');
        session()->forget('sessionAction');

        if (in_array(Auth::id(), json_decode((Issuer::where('issuerID', $id)->first())['assistantID']))) {

            session()->put('issuerID', $id);

            $issuer = Issuer::where('issuerID', $id)->first();
            $customFields = json_decode($issuer->customfield, true);

            return view('assistantCompany')->with('company', Issuer::where('issuerID', $id)->first())
                ->with('customFields', $customFields)
                ->with('badgeclasses', Badgeclass::where('issuerID', $id)->where('status', '=', '1')->orderBy('created_at', 'DESC')->get());
        }

        return redirect('assistantMain');
    }

    public function beginCreateBadgeclass()
    {
        return view('assistantNewBadgeclass');
    }

    public function recordCreateBadgeclass(Request $request)
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
                $filePath2 = $request->file('file')->storeAs('images', $fileName, 'public');

                $badgeimage = new Badgeimage;


                $nowTime = time();
                $salt = hash('sha256', random_bytes(128) . $nowTime);
                $identity = "sha256$" . hash('sha256', ($request->badgeclass_name) . ($salt));

                $badgeclass = new Badgeclass;

                $badgeclass->badgeclassID = $identity;

                $badgeimage->badgeclassID = $identity;
                $badgeimage->outputfilename = 'image.png';
                $badgeimage->filepath = '/public/' . $filePath2;
                $badgeimage->imgauthorname = $request->badgeclass_image_author_name;
                $badgeimage->imgauthoremail = $request->badgeclass_image_author_email;
                $badgeimage->imgauthorurl = $request->badgeclass_image_author_url;
                $badgeimage->imgcaption = $request->badgeclass_image_caption;

                $badgeclass->name = $request->badgeclass_name;
                $badgeclass->description = str_replace(array("\n", "\r"), ' * ', $request->badgeclass_description);
                $badgeclass->criteria = str_replace(array("\n", "\r"), ' * ', $request->badgeclass_criteria);
                $badgeclass->issuerID = session()->get('issuerID');


                $badgeclass->tags = json_encode(array_map('ltrim', explode(';', $request->badgeclass_tags)));

                $badgeclass->type = Storage::get('public/badgeclasscustomfields.json');;

                $badgeclass->status = '1';

                //ddd($request->all());

                $badgeclass->save();
                $badgeimage->save();
                return back()
                    ->with('success', __('hello.ob_upload_success'));
            }

            return back()
                ->withErrors('General error');
        }
    }


    public function assistantShowAssertions()
    {
        //ddd(session()->all());

        $issuerID = session()->get('issuerID');
        $badgeclassIDs = Badgeclass::select('badgeclassID')->where('issuerID', $issuerID)->where('status', '=', '1')->get()->toArray();


        return view('assistantShowAssertions')
            ->with('numberofassertions', Assertion::whereIn('badgeclassID', $badgeclassIDs)->count())
            ->with('assertionlist', Assertion::whereIn('badgeclassID', $badgeclassIDs)->orderBy('issuedOn', 'DESC')->get())
            ->with('issuerID', $issuerID);
    }


    public function showAllPreAssertion()
    {
        $issuerID = session()->get('issuerID');


        if (in_array(Auth::id(), json_decode((Issuer::where('issuerID', $issuerID)->first())['assistantID']))) {

            $preassertions = Preassertion::where('issuerID', '=', $issuerID)->where('status', '=', '1')->get();

            return view('assistantShowPreAssertions')
                ->with('numberofpreassertions', $preassertions->count())
                ->with('preassertionlist', $preassertions);
        }
        return redirect('assistantMain');
    }


    public function editPreAssertion($id)
    {
        $issuerID = session()->get('issuerID');

        if (($thisPreAssertion = Preassertion::where('id', '=', $id)->first()) && (in_array(Auth::id(), json_decode((Issuer::where('issuerID', $issuerID)->first())['assistantID'])))) {

            //ddd(substr($thisPreAssertion['issuedOn'], 0, 14));

            session()->put('sessionAction', 'edit');
            session()->put('editedPreAssertionID', $id);

            return view('assistantEditPreAssertion')
                ->with('thisPreAssertion', $thisPreAssertion)
                ->with('badgeclasslist', Badgeclass::where('issuerID', '=', session()->get('issuerID'))->get());
        }

        return redirect('backtocompany');
    }

    public function beginCreatePreAssertion()
    {

        $issuerID = session()->get('issuerID');


        if (in_array(Auth::id(), json_decode((Issuer::where('issuerID', $issuerID)->first())['assistantID']))) {

            session()->put('sessionAction', 'create');

            return view('assistantNewPreAssertion')
                ->with('badgeclasslist', Badgeclass::where('issuerID', '=', $issuerID)->get());
        }
        return redirect('abc');
    }

    public function recordPreAssertion(Request $request)
    {

        //ddd(session()->all());
        //ddd($request->all());


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

        $preassertion->recipientType = 'email';
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


        $preassertion->assertiontype = json_encode($assertionType, true);

        $preassertion->released = date("Y-m-d\TH:i:s\Z", $nowTime);

        $preassertion->status = '1';

        $preassertion->save();

        //ddd(session()->all());

        session()->forget('sessionAction');

        return redirect('backtoassistantcompany');
    }
}

<?php

namespace App\Http\Controllers;

use App\DCP\CheckBadgeFile;
use App\Mail\UserMail;
use App\Models\Assertion;
use App\Models\Badgeclass;
use App\Models\Badgeimage;
use App\Models\EarnerBadge;
use App\Models\Issuer;
use App\Models\SiteSetting;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ManageAdminController extends Controller
{


    public function showAdminDashboard()
    {

        session()->forget('editedIssuerID');
        return view('adminDashboard');
    }

    public function showEarnerStatistics()
    {

        //ddd(EarnerBadge::first()->badge_name);

        $users = User::join('earner_badges', 'earner_badges.earnerID', '=', 'users.id')
            ->where('earner_badges.status', '=', '1')
            ->orderBy('earner_badges.created_at', 'DESC')
            ->get(['users.name', 'users.email', 'earner_badges.earnerbadgeID', 'earner_badges.cachedBadgeReport', 'earner_badges.created_at',])->toArray();


        $usernames = [];

        foreach ($users as &$user) {
            $thisBadge = EarnerBadge::where('earnerbadgeID', '=', $user['earnerbadgeID'])->where('status', '=', '1')->first();
            $user['badgename'] = $thisBadge->getBadgeNameAttribute();
            $user['earnerBadgeInfoURL'] = env('APP_URL') . "/earnerbadges/" . $user['earnerbadgeID'] . "/info";
            $user['earnerBadgeURL'] = env('APP_URL') . "/earnerbadges/" . $user['earnerbadgeID'] . "/badge";
            $usernames[] = $user['name'];
        }



        $statistics['allissuerNum'] = Issuer::count();

        $statistics['allbadgeclassNum'] = Badgeclass::count();

        $statistics['alluploaderearnerNum'] = sizeof(array_unique($usernames));

        $statistics['alluploadedearnerbadgeNum'] = sizeof($users);

        $statistics['allissuedassertionNum'] = Assertion::count();

        $statistics['allexpiringassertionNum'] = Assertion::where('neverexpires', '=', 'off')->count();

        $statistics['allrevokedassertionNum'] = Assertion::where('revoked', '=', 'on')->count();


        $statistics['earnerNum'] = User::all()->filter(function ($user) {
            if (in_array('earner', $user->roles)) {
                return true;
            }
        })->count();

        $statistics['maxuploadedbyearnerbadgeNum'] = EarnerBadge::where('status', '=', '1')->get()->groupBy('earnerID')->max()->count();



        $statistics['allnotDCPbadgeNum'] = EarnerBadge::where('status', '=', '1')->get()->filter(function ($item) {

            return (!$item->is_dcp_badge);
        })->count();




        return view('earnerBadgeStatistics')
            ->with('users', $users)
            ->with('numberofuploaderearnerusers', sizeof(array_unique($usernames)))
            ->with('numberofbadges', sizeof($users))
            ->with('statistics', $statistics);
    }

    public function manageIssuers()
    {

        return view('adminManageIssuersDashboard');
    }

    public function editIssuer($id)
    {

        if ($issuer = Issuer::where('issuerID', '=', $id)->first()) {

            session()->put('editedIssuerID', $id);


            $customField = json_decode($issuer->customfield, true);

            //ddd($customField['header']);

            return view('adminEditIssuer')
                ->with('issuer', $issuer)
                ->with('customFields', $customField);
        }

        return redirect('adminMain');
    }


    public function showIssuedBadges($id)
    {
        if ($issuer = Issuer::where('issuerID', '=', $id)->first()) {


            $issuerID = $issuer->issuerID;
            $badgeclassIDs = Badgeclass::select('badgeclassID')->where('issuerID', $issuerID)->get()->toArray();



            return view('AdminAllAssertions')
                ->with('numberofassertions', Assertion::whereIn('badgeclassID', $badgeclassIDs)->count())
                ->with('assertionlist', Assertion::whereIn('badgeclassID', $badgeclassIDs)->orderBy('issuedOn', 'DESC')->get())
                ->with('issuer', $issuer);
        }

        return redirect('adminMain');
    }

    public function updateIssuer(Request $request)
    {
        //ddd(session()->all());

        $editedIssuerID = session()->get('editedIssuerID');
        if ($issuer = Issuer::where('issuerID', '=', $editedIssuerID)->first()) {




            //ddd($request->all());
            // ddd(array_keys($request->all()));
            //ddd($issuer);




            $issuer->update(['name' => $request->issuer_name_text]);
            $issuer->update(['url' => $request->issuer_url_text]);
            $issuer->update(['email' => $request->issuer_email_text]);



            if (!User::where('id', '=', $request->issuer_option) and !in_array('issuer', User::where('id', '=', $request->issuer_option)->roles)) {
                //Error
            } else {
                $issuer->adminID = $request->issuer_option;
                $issuer->save();
            }

            $assistants = [];

            if ($request->assistant_option) {
                foreach ($request->assistant_option as $assistant) {
                    if (!User::where('id', '=', $assistant) and !in_array('assistant', User::where('id', '=', $assistant)->roles)) {
                    } else {
                        $assistants[] = $assistant;
                    }
                }
            }
            $issuer->assistantID = $assistants;
            $issuer->save();


            if ($request->file) {
                $fileName = time() . '_' . $request->file->getClientOriginalName();
                $filePath1 = $request->file('file')->storeAs('images', $fileName, 'public');
                $issuer->logofile = $fileName;
            }


            $customField = json_decode($issuer->customfield, true);

            foreach (array_keys($request->all()) as $item) {
                if (strpos($item, 'issuercustom_') !== false) {
                    foreach ($customField['form'] as &$form) {
                        if ($item == $form['id']) {
                            $form['content'] =  $request->$item;
                        }
                    }
                }
            }

            $issuer->update(['customfield' => json_encode($customField)]);
        }


        session()->forget('editedIssuerID');
        return redirect('manageIssuers');
    }


    public function addNewIssuer()
    {
        return view('adminAddNewIssuer');
    }

    public function doNewIssuerRecord(Request $request)
    {
        $isError = false;
        $errorMesage[] = 'Error!';



        //ddd($request->all());

        $isValid = Validator::make($request->all(), [
            //'file' => 'required|mimetypes:image/jpeg,image/png,image/gif'
            'file' => 'required|mimetypes:image/png'
        ]);

        if ($isValid->fails()) {
            return back()
                ->withErrors($isValid);
        }

        if (Issuer::where('name', '=', $request->issuer_name_text)->first()) {
            $errorMesage[] = 'Issuer name ( ' . $request->issuer_name_text . ' ) is in DCP, choose other!';

            $isError = true;
        }

        if (Issuer::where('email', '=', $request->issuer_email_text)->first()) {
            $errorMesage[] = 'Issuer e-mail ( ' . $request->issuer_email_text . ' ) is in DCP, choose other!';

            $isError = true;
        }


        if (Issuer::where('url', '=', $request->issuer_url_text)->first()) {
            $errorMesage[] = 'Issuer URL ( ' . $request->issuer_url_text . ' ) is in DCP, choose other!';

            $isError = true;
        }

        if (!$request->file()) {
            $errorMesage[] = 'File is not valid!!';

            $isError = true;
        }


        if (!User::where('id', '=', $request->issuer_option) and !in_array('issuer', User::where('id', '=', $request->issuer_option)->roles)) {
            $errorMesage[] = 'Admin to issue is not valid!';

            $isError = true;
        }



        if ($isError) {
            return back()
                ->withErrors($errorMesage);
        } else {


            $issuer = new Issuer;


            $issuer->name = $request->issuer_name_text;
            $issuer->email = $request->issuer_email_text;
            $issuer->url = $request->issuer_url_text;

            $fileName = time() . '_' . $request->file->getClientOriginalName();
            $filePath1 = $request->file('file')->storeAs('images', $fileName, 'public');
            $issuer->logofile = $fileName;

            $issuer->adminID = $request->issuer_option;

            $issuer->assistantID = "[]";

            $jsonField = json_decode(Storage::get('public/issuercustomfields.json'));

            /*    foreach (array_keys($request->all()) as $item) {
                if (strpos($item, 'issuercustom_') !== false) {
                    foreach ($jsonField['form'] as &$form) {
                        if ($item == $form['id']) {
                            $form['content'] =  $request->$item;
                        }
                    }
                }
            } */

            $fullNamePartnerOrg = $request->issuercustom_0001;
            $partnerOrg = $request->issuercustom_0002;

            $jsonField->form[0]->content = $fullNamePartnerOrg;
            $jsonField->form[1]->content = $partnerOrg;

            $jsonField = json_encode($jsonField, true);

            //ddd($jsonField);

            $issuer->customfield = $jsonField;


            $nowTime = time();
            $salt = hash('sha256', random_bytes(128) . $nowTime);

            $issuer->issuerID = hash('sha256', $issuer->email . $partnerOrg . $fullNamePartnerOrg . ($salt));

            //ddd($issuer);

            $issuer->save();

            return back()
                ->with('success', __('hello.newissuer_adding_success'));
        }
    }

    public function manageUsers()
    {
        return view('adminManageUsersDashboard');
    }

    public function editUserByAdmin($id)
    {

        if ($user = User::where('id', '=', $id)->first()) {

            session()->put('editedUserID', $id);

            return view('adminEditUser')
                ->with('user', $user);
        }

        return redirect('adminMain');
    }

    public function updateUser(Request $request)
    {

        $editedUserID = session()->get('editedUserID');


        if ($user = User::where('id', '=', $editedUserID)->first()) {



            // ddd($request->all());
            // ddd(array_keys($request->all()));

            $generalRoles = ['issuer', 'assistant', 'earner', 'verifier', 'admin'];

            $userRoles = [];

            foreach ($generalRoles as $role) {

                if (in_array($role, array_keys($request->all()))) {
                    $userRoles[] = $role;
                }
            }

            //ddd($userRole);

            $user->update(['name' => $request->user_name_text]);
            $user->update(['email' => $request->user_email_text]);
            $user->roles = json_decode(json_encode($userRoles, true));
            $user->save();
        }


        session()->forget('editedUserID');
        return redirect('manageUsers');
    }

    public function editUserProfile()
    {


        if ($thisUser = User::where('id', '=', session()->get('userID'))->first()) {


            //ddd($thisUser);

            return view('userEditUserProfile')
                ->with('user', $thisUser);
        }

        return redirect('abc');
    }

    public function recordUserProfile(Request $request)
    {

        $isError = false;
        $errorMesage[] = 'Error!';



        if (strlen($request->user_name_text) < 1) {
            $errorMesage[] = 'Your name is empty!';

            $isError = true;
        }


        if (strlen($request->user_email_text) < 5) {
            $errorMesage[] = 'Your e-mail address is too short!';

            $isError = true;
        }


        if ($isError) {
            return back()
                ->withErrors($errorMesage);
        } else {


            $thisUser = User::where('id', '=', session()->get('userID'))->first();
            $thisUser->name = $request->user_name_text;
            $thisUser->email = $request->user_email_text;
            $thisUser->save();

            return  redirect('abc');
        }
    }

    public function changeUserPassword()
    {
        if ($thisUser = User::where('id', '=', session()->get('userID'))->first()) {


            //ddd($thisUser);

            return view('userChangePassword')
                ->with('user', $thisUser);
        }

        return redirect('abc');
    }

    public function recordUserPassword(Request $request)
    {

        $isError = false;
        $errorMesage[] = 'Error!';

        $minimumLength = 7;

        //ddd($request->all());

        if (preg_match('/[^A-Za-z0-9]/', $request->user_password_text01)) {
            $errorMesage[] = 'New password must contain only English letters and numbers!';

            $isError = true;
        }

        if (strlen($request->user_password_text01) <= $minimumLength) {
            $errorMesage[] = 'New password is too short! Minimum length of password string is: ' . $minimumLength . ' !';

            $isError = true;
        }

        if ($isError) {
            return back()
                ->withErrors($errorMesage);
        } else {

            //ddd(session()->all());

            $thisUser = User::where('id', '=', session()->get('userID'))->first();
            $thisUser->password = $request->user_password_text01;
            $thisUser->save();

            return  redirect('users/editProfile');
        }
    }

    public function addNewUser()
    {

        return view('addNewUser');
    }

    public function doNewUserRecord(Request $request)
    {

        $isError = false;
        $errorMesage[] = 'Error!';


        //ddd($request->all());

        if (User::where('email', '=', $request->user_email_text)->first()) {
            $errorMesage[] = 'E-mail ( ' . $request->user_email_text . ' ) is in DCP, choose other!';

            $isError = true;
        }


        if (User::where('username', '=', $request->user_username_text)->first()) {
            $errorMesage[] = 'User name ( ' . $request->user_username_text . ' ) is in DCP, choose other!';

            $isError = true;
        }


        if ($isError) {
            return back()
                ->withErrors($errorMesage);
        } else {

            $user = new User;
            $user->name = $request->user_name_text;
            $user->email = $request->user_email_text;
            $user->username = $request->user_username_text;
            $user->password = $request->user_password_text;
            $user->roles = json_decode(json_encode([], true));

            $user->registrationtype = 'manual';
            $user->status = '1';

            $user->save();

            return back()
                ->with('success', __('hello.newuser_adding_success'));
        }
    }

    public function bulkUploadUser()
    {
        // Just for a safe state :-)
        $fileContent = Storage::get('/public/' . 'dcp_users_20221026.csv');

        $csvLines = explode("\n", $fileContent);

        $outputTable = "<table><thead><tr>
        <th>Sign</th>
        <th>name</th>
        <th>email</th>
        <th>username</th>
        <th>password</th>
        </tr></thead><tbody>";

        for ($i = 1; $i < 18; $i++) {
            $csvLine =  explode(";", $csvLines[$i]);

            //ddd($csvLine);

            $name = $csvLine[0];
            $email = $csvLine[1];
            $username = $csvLine[2];
            $password = $csvLine[3];

            try {

                // create a new user

                if ($actUser = User::where('email', '=', $email)->first()) {

                    $outputTable .= "<tr>
                    <td></td>
                    <td>" . $actUser->name . "</td>
                    <td>" . $actUser->email . "</td>
                    <td>" . $actUser->username . "</td>
                    <td>" . $actUser->password . "</td>
                    </tr>";
                } else {
                    $sign = '*';
                    $user = new User;
                    $user->name = $name;
                    $user->email = $email;
                    if (User::where('username', '=', $email)->first()) {
                        $sign = 'X';
                    }
                    $user->username = $username;
                    $user->password = $password;
                    $user->roles = json_decode(json_encode(["earner"], true));

                    $user->registrationtype = 'manual';
                    $user->status = '1';

                    $outputTable .= "<tr>
                    <td>" . $sign . "</td>
                <td>" . $user->name . "</td>
                <td>" . $user->email . "</td>
                <td>" . $user->username . "</td>
                <td>" . $user->password . "</td>
                </tr>";

                    //$user->save();
                }
            } catch (Exception $e) {
                ddd($e);
            }
        }

        //ddd('Bulk upload');

        return ($outputTable);
    }

    public function bulkAwardAssertions()
    {

        // $fileContent = Storage::get('/public/' . 'emk_20221004.csv');
        $csvLines = explode("\n", $fileContent);

        $outputTable = "<table><thead><tr>
        <th>Sign</th>
        <th>name</th>
        <th>email</th>
        <th>badgeclassname</th>
        <th>comment</th>
        </tr></thead><tbody>";

        for ($i = 1; $i < 17; $i++) {
            $csvLine =  explode(";", $csvLines[$i]);
            $name = $csvLine[0];
            $email = $csvLine[1];
            $badgeclassID = $csvLine[2];



            /*    if ($actUser = User::where('email', '=', $email)->first()) {

                $sign = "";

                if (in_array('earner', $actUser->roles) && ($thisBadgeclass = Badgeclass::where('badgeclassID', '=', $badgeclassID)->first())) {

*/
            $thisBadgeclass = Badgeclass::where('badgeclassID', '=', $badgeclassID)->first();
            $sign = "";
            try {
                // create a new assertion

                $nowTime = time();

                $salt = hash('sha256', random_bytes(128) . $nowTime);

                $identity = "sha256$" . hash('sha256', ($email) . ($salt));


                $assertion = new Assertion;

                $assertion->assertionID = hash('sha256', $nowTime . random_bytes(128));


                $assertion->badgeclassID = $badgeclassID;

                $assertion->salt = $salt;

                $assertion->identity = $identity;

                $assertion->originalIdentity = $email;

                $assertion->recipientType = 'email';

                date_default_timezone_set('Europe/Budapest');
                $assertion->issuedOn = date("Y-m-d\TH:i:s\Z", ($nowTime - 10000));

                $assertion->assertiontype = json_encode(json_decode($thisBadgeclass->type, true));

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

                ddd($assertion);

                // $this->assertionCont = new ManageAssertionController;
                // $assertionBadgePath = $this->assertionCont->createAssertionBadge($assertionJSON, $outputFilename, $badgeclassImagePath[0]['filepath']);

                // ddd($assertionBadgePath);

                if ($assertionBadgePath) {





                    $outputTable .= "<tr>
                    <td>" . $sign . "</td>
                    <td>" . $name . "</td>
                    <td>" . $email . "</td>
                    <td>" . $thisBadgeclass->name . "</td>
                    <td>" . $assertion->assertionID  . "</td>
                    </tr>";

                    //  $assertion->save();
                }
            } catch (Exception $e) {
                ddd($e);
            }
        }

        /* else {
                    $sign = "x2";
                    $outputTable .= "<tr>
                    <td>" . $sign . "</td>
                    <td>" . $actUser->name . "</td>
                    <td>" . $actUser->email . "</td>
                    <td>" . "" . "</td>
                    <td>" . "User has not got earner role or not such a badgeclass" . "</td>
                    </tr>";
                }
            } else {
                $sign = "x";
                $outputTable .= "<tr>
                <td>" . $sign . "</td>
                <td>" . $name . "</td>
                <td>" . $email . "</td>
                <td>" . "" . "</td>
                <td>" . "E-mail is not in database" . "</td>
                </tr>";
            } 
        } */

        $outputTable .= "</tbody></table>";
        return ($outputTable);
    }

    public function moveAssertionsToEarner()
    {
        $fileContent = Storage::get('/public/' . 'szpk_movebadges_20220921_jelenlét.csv');
        $csvLines = explode("\n", $fileContent);

        $outputTable = "<table><thead><tr>
        <th>Sign</th>
        <th>name</th>
        <th>email</th>
        <th>earnerID</th>
        <th>assertionID</th>
        <th>comment</th>
        </tr></thead><tbody>";

        for ($i = 1; $i < 9; $i++) {
            $csvLine =  explode(";", $csvLines[$i]);
            $name = $csvLine[1];
            $email = $csvLine[2];
            $badgeclassID = $csvLine[3];
            $assertionID = $csvLine[4];

            if ($actUser = User::where('email', '=', $email)->first()) {

                $sign = "";

                if (in_array('earner', $actUser->roles) && ($thisAssertion = Assertion::where('assertionID', '=', $assertionID)->first())) {
                    try {

                        // ddd(substr($thisAssertion->digitalbadgepath, strlen('public/')));
                        $fileThere = Storage::get($thisAssertion->digitalbadgepath);
                        $myReport = new CheckBadgeFile(substr($thisAssertion->digitalbadgepath, strlen('public/')));
                        $backData = $myReport->getReportOfBadgeFile();

                        //ddd($backData);


                        if ($backData['summary']) {

                            //$filePath2 = $request->file('file')->storeAs('earnerbadges', $fileName, 'public');
                            $fileName = time() . '_' . substr($thisAssertion->digitalbadgepath, strlen('public/badges/'));
                            $isFileThere = Storage::put('public/earnerbadges/' . $fileName, $fileThere);
                            $earnerbadge = new EarnerBadge;

                            $fileContent = Storage::get('/public/earnerbadges/' . $fileName);

                            //ddd($fileContent);

                            // Ez itt a file leíró adatok alapján állít elő hash-t, nem a tartalom alapján!
                            $earnerbadgecontenthash = hash('sha256', $fileContent);

                            $nowTime = time();
                            $salt = hash('sha256', random_bytes(128) . $nowTime);
                            $identity = "sha256$" . hash('sha256', $earnerbadgecontenthash  . ($salt));

                            $earnerbadge->earnerID = $actUser->id;
                            $earnerbadge->earnerbadgeID = $identity;
                            $earnerbadge->earnerbadgecontenthash = $earnerbadgecontenthash;
                            $earnerbadge->earnerbadgepath = '/public/earnerbadges/' . $fileName;
                            $earnerbadge->description = "Moved from the Issuer";
                            $earnerbadge->issearchable = '0';

                            $earnerbadge->cachedBadgeReport = serialize($backData);

                            //ddd($backData);

                            $earnerbadge->recipientType = $backData['checkOB']['recipient']['type'];
                            $earnerbadge->recipientIdentity = '';

                            $earnerbadge->earnerBadgeTags = '[]';

                            $earnerbadge->status = '1';

                            //$earnerbadge->save();

                            // Ezt majd legközelebb befejezzük :-)
                            //$this->recordCheckEarnerBadge($identity);


                            $outputTable .= "<tr>
                        <td>" . $sign . "</td>
                        <td>" . $actUser->name . "</td>
                        <td>" . $actUser->email . "</td>
                        <td>" . $actUser->id . "</td>                      
                        <td>" . $assertionID  . "</td>
                        <td>" . $fileName . "</td>
                        </tr>";
                        }
                    } catch (Exception $e) {
                        ddd($e);
                    }
                }
            }
        }

        $outputTable .= "</tbody></table>";
        return ($outputTable);
    }

    public function bulkUploadIssuers()
    {

        //$fileContent = Storage::get('/public/' . 'discovet_20220817_bulkissuer.csv');
        $csvLines = explode("\n", $fileContent);

        $outputTable = "<table><thead><tr>
        <th>Sign</th>
        <th>name</th>
        <th>email</th>   
        <th>issuerID</th>     
        <th>comment</th>
        </tr></thead><tbody>";

        for ($i = 1; $i < 2; $i++) {
            $csvLine =  explode(",", $csvLines[$i]);



            $name = $csvLine[1];
            $email = $csvLine[2];
            $partnerOrg = $csvLine[3];
            $fullNamePartnerOrg = $csvLine[4];
            $issuerName = $csvLine[5];
            $logofile = $csvLine[6];
            $url = $csvLine[7];

            if ($actUser = User::where('email', '=', $email)->first()) {

                $sign = "";

                if (in_array('issuer', $actUser->roles)) {
                    try {
                        $nowTime = time();
                        $salt = hash('sha256', random_bytes(128) . $nowTime);
                        $issuerID = hash('sha256', $email . $partnerOrg . $fullNamePartnerOrg . ($salt));


                        $thisIssuer = new Issuer;
                        $thisIssuer->issuerID = $issuerID;
                        $thisIssuer->adminID = $actUser->id;
                        $thisIssuer->name = $issuerName;
                        $thisIssuer->url = $url;
                        $thisIssuer->email = $email;
                        $thisIssuer->logofile = $logofile;

                        $jsonField = json_decode(Storage::get('public/issuercustomfields.json'));

                        //ddd($jsonField);

                        $jsonField->form[0]->content = $fullNamePartnerOrg;
                        $jsonField->form[1]->content = $partnerOrg;

                        $jsonField = json_encode($jsonField, true);

                        //ddd($jsonField);

                        $thisIssuer->customfield = $jsonField;

                        $thisIssuer->save();
                        $outputTable .= "<tr>
                        <td>" . $sign . "</td>
                        <td>" . $actUser->name . "</td>
                        <td>" . $actUser->email . "</td>                     
                        <td>" . $issuerID . "</td>
                        <td></td>
                        </tr>";
                    } catch (Exception $e) {
                        ddd($e);
                    }
                }
            }
        }

        $outputTable .= "</tbody></table>";
        return ($outputTable);
    }


    public function sendLoginDataByEmail($id)
    {

        if ($thisUser = User::where('id', '=', $id)->first()) {

            $details = [

                'name' => $thisUser->name,
                'subject' => 'Your login data in DCP',
                'username' => $thisUser->username,
                'password' => $thisUser->password,
                'id' => $id,

            ];

            Mail::to($thisUser->email)->send(new UserMail($details));
        }

        return redirect('adminMain');
    }

    public function selfRegistration()
    {
        return view('adminSelfRegistrationUser');
    }

    public function doSelfRegistration(Request $request)
    {
        $isError = false;
        $errorMesage[] = 'Error!';

        $isValid = Validator::make($request->all(), [
            'user_email_text' => 'required|email'
        ]);


        if (strlen($request->user_name_text) < 5) {
            $errorMesage[] = 'Your name ( ' . $request->user_name_text . ' ) is too short, choose other one!';
            $isError = true;
        }

        if ($isValid->fails()) {
            $errorMesage[] = 'Your e-mail address ( ' . $request->user_email_text . ' ) is not valid, choose other one!';
            $isError = true;
        }

        if (User::where('email', '=', $request->user_email_text)->first()) {
            $errorMesage[] = 'Your e-mail address ( ' . $request->user_email_text . ' ) is used in DCP, choose other one!';
            $isError = true;
        }


        if ($isError) {
            return back()
                ->withErrors($errorMesage);
        } else {

            $checkBadgeOne = new CheckBadgeFile("");

            if (User::where('email', '=', $request->user_email_text)->first()) {
                return redirect('abc');
            }

            $preUsername = (explode("@", $request->user_email_text))[0];


            $i = 0;

            do {
                $username = $preUsername . $checkBadgeOne->generateRandomNumber($i);
                $i++;
            } while (User::where('username', '=', $username)->first());



            do {
                $password = $checkBadgeOne->generateRandomString(15);
            } while (User::where('username', '=', $password)->first());

            $newUserData['name'] = $request->user_name_text;
            $newUserData['email'] = $request->user_email_text;
            $newUserData['username'] = $username;
            $newUserData['password'] = $password;


            $thisUser = new User;
            $thisUser->name = $newUserData['name'];
            $thisUser->email = $newUserData['email'];
            $thisUser->username = $newUserData['username'];
            $thisUser->password = $newUserData['password'];
            $thisUser->status = '1';
            $thisUser->registrationtype = 'self';
            $thisUser->roles = json_decode(json_encode(["earner"], true));
            $thisUser->save();

            $this->sendLoginDataByEmail($thisUser->id);


            return view('adminFinalizeSelfRegistration')
                ->with('newUserData', $newUserData);
        }
    }

    public function showSiteSettings()
    {
        return view('adminSiteSettings');
    }

    public function setSiteSettings(Request $request)
    {
        $servicemode = SiteSetting::where('key', 'servicemode')->first();
        $servicemode->value = 'off';
        if ($request->has('servicemode')) {

            $servicemode->value = 'on';
        }
        $servicemode->save();

        return redirect('adminMain');
    }
}

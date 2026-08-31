<?php

use App\Events\TestSignal;
use App\Http\Controllers\BadgeConnectConsumerController;
use App\Http\Controllers\BadgeConnectManifestController;
use App\Http\Controllers\CheckBadgeController;
use App\Http\Controllers\DCRController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RevokeTokenController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\MakeBadgeController;
use App\Http\Controllers\ManageAdminController;
use App\Http\Controllers\ShowDetailsController;
use App\Http\Controllers\ManageAssertionController;
use App\Http\Controllers\ManageAssistantController;
use App\Http\Controllers\ManageEarnerController;
use App\Http\Controllers\ManageIssuerController;
use App\Http\Controllers\ManageMetabadgeRuleController;
use App\Http\Controllers\ManageVerifierController;
use App\Http\Controllers\ManageWGETController;
use App\Http\Controllers\MTItempController;
use App\Http\Controllers\PNGinspector;
use App\Http\Controllers\RomanGaborController;
use App\Models\Assertion;
use App\Models\Stackorder;
use App\Models\Stackrole;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    //return("HELLO :-)");

    return view('abc');
});

Route::get('/abc', function () {
    return redirect('/');
});

//Route::get('test2', fn () => phpinfo());

Route::get('main', function () {

    return view('main');
})->name('main');

Route::get('badges', function () {
    return 'Hello from deep of DCP :-)';
});

Route::get('settings', [SettingsController::class, 'setLanguages']);
Route::post('settings', [SettingsController::class, 'storeLanguages']);

Route::get('checkBadge', [CheckBadgeController::class, 'mainCheckBadge']);
Route::post('checkBadge', [CheckBadgeController::class, 'doCheckBadge']);

Route::get('makeBadge', [MakeBadgeController::class, 'showMakeBadge']);
Route::post('makeBadge', [MakeBadgeController::class, 'createBadge']);

Route::get('organizations/{id}', [ShowDetailsController::class, 'showOrganization']);
Route::get('organizations/{id}/image', [ShowDetailsController::class, 'showImage']);



Route::get('assertions/{id}', [ShowDetailsController::class, 'showAssertion']);
Route::get('assertions/{id}/badge', [ShowDetailsController::class, 'showAssertionBadge']);

Route::get('badgeclasses/{id}', [ShowDetailsController::class, 'showBadgeClass']);
Route::get('badgeclasses/{id}/image', [ShowDetailsController::class, 'showBadgeClassImage']);
Route::get('evidences/{id}', [ShowDetailsController::class, 'showEvidence']);
Route::get('criteria/{id}', [ShowDetailsController::class, 'showCriteria']);

Route::get('earnerbadges/{id}/badge', [ShowDetailsController::class, 'showEarnerBadge']);
Route::get('earnerbadges/{id}/delete', [ManageEarnerController::class, 'deleteEarnerBadge'])->middleware('earner');
Route::get('earnerbadges/{id}/finalDelete', [ManageEarnerController::class, 'finalDeleteEarnerBadge'])->middleware('earner');
Route::get('earnerbadges/{id}/edit', [ManageEarnerController::class, 'editEarnerBadge']);
Route::get('earnerbadges/{id}/print', [ManageEarnerController::class, 'printEarnerBadge']);
Route::get('earnerbadges/{id}/sharebyemail', [ManageEarnerController::class, 'shareByEmail'])->middleware('earner');
Route::post('doShareByEmail', [ManageEarnerController::class, 'doShareByEmail'])->middleware('earner');
Route::get('earnerbadges/{id}/info', [ManageEarnerController::class, 'infoEarnerBadge']);
Route::get('earnerbadges/{id}/check', [ManageEarnerController::class, 'checkEarnerBadge']);
Route::get('earnerbadges/{id}/cert', [ManageEarnerController::class, 'certEarnerBadge']);
Route::get('earnerbadges/{id}/toHTML', [ManageEarnerController::class, 'earnerBadgeToHTML']);
Route::get('earnerbadges/{id}/endorse', [ManageVerifierController::class, 'endorseEarnerBadge'])->middleware('verifier');;

Route::get('verifierbadges/{id}/badge', [ShowDetailsController::class, 'showVerifierBadge']);
Route::get('verifierbadges/{id}/delete', [ManageVerifierController::class, 'deleteVerifierBadge']);
Route::get('verifierbadges/{id}/info', [ManageVerifierController::class, 'infoVerifierBadge']);
Route::post('recordVerifierEndorsement', [ManageVerifierController::class, 'recordVerifierEndorsement'])->middleware('verifier');
Route::post('deleteVerifierEndorsement', [ManageVerifierController::class, 'deleteVerifierEndorsement'])->middleware('verifier');


Route::get('showAssertions', [ManageAssertionController::class, 'showAllAssertions']);
Route::get('createAssertion', [ManageAssertionController::class, 'beginCreateNewAssertion']);
Route::post('doAssertion', [ManageAssertionController::class, 'processCreateNewAssertion']);

Route::get('createPreAssertion', [ManageAssertionController::class, 'beginCreatePreAssertion'])->middleware('issuer');
Route::get('showPreAssertions', [ManageAssertionController::class, 'showAllPreAssertion'])->middleware('issuer');
Route::post('recordPreAssertion', [ManageAssertionController::class, 'recordPreAssertion'])->middleware('issuer');
Route::get('bulkPreAssertionUpload', [ManageAssertionController::class, 'bulkPreAssertionUpload'])->middleware('issuer');
Route::post('bulkPreAssertionUpload', [ManageAssertionController::class, 'doPreAssertionUpload'])->middleware('issuer');
Route::post('bulkPreAssertionUploadXLSX', [ManageAssertionController::class, 'doPreAssertionUploadXLSX'])->middleware('issuer');
Route::get('greencertAssertionUpload', [ManageAssertionController::class, 'greencertAssertionUpload'])->middleware('issuer');
Route::post('greencertPreAssertionUploadXLSX', [ManageAssertionController::class, 'doGreencertPreAssertionUploadXLSX'])->middleware('issuer');
Route::get('preAssertions/{id}/delete', [ManageAssertionController::class, 'deletePreAssertion'])->middleware('issuer');
Route::get('preAssertions/{id}/edit', [ManageAssertionController::class, 'editPreAssertion'])->middleware('issuer');
Route::get('preAssertions/{id}/award', [ManageAssertionController::class, 'awardPreAssertion'])->middleware('issuer');
Route::post('preAssertionsMultipleAward', [ManageAssertionController::class, 'multipleAwardPreAssertion'])->middleware('issuer');

Route::get('error', function () {
    return view('error');
});

/*
Route::get('BMEMTI_logo.png', function () {

    $headers = ['Content-Type: application/png'];

    return Storage::response('public/images/BMEMTI_logo.png', 'image.png', $headers);
});
*/



Route::get('login', function (Request $request) {

    return view('login');
})->name('login')->middleware('guest');

Route::post('login', [LoginController::class, 'doLogin'])->middleware('guest');

/*
Route::get('login', function (Request $request) {


    $username = 'teszt';
    $password = 'elek';


    $user = User::where('username', '=', $username)
        ->where('password', '=', $password);

    $userID = $user->get('id')->toArray()[0];
    $userID = $userID['id'];

    session()->put('userID', $userID);

    Auth::loginUsingId($userID);

    return redirect('abc');
})->middleware('guest');
*/

Route::get('logout', function (Request $request) {

    if (session()->has('userID')) {

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
        //Log::info("Logout, " . $userid  . ", " . $username . ", "  . json_encode($subject) . ", " . \Request::ip() . ", " . \Request::userAgent());




        session()->forget('userID');
        session()->flush();
    }

    Auth::logout();



    session()->put('loginPage', true);

    return redirect('home');
});

Route::get('temp', function () {

    return view('temp');
});

Route::get('home', function () {

    session()->forget('issuerID');



    return view('abc');
});


/*Route::get('testsignal', function () {

    event(new TestSignal("Hello Event!"));
}); */

Route::get('test', function () {

    /*   $x = Stackorder::where('id', 1)->first()->get_all_members_from_stack;

    $z = $x->map(function ($item, $key) {
        return $item->stackmemberID;
    })->combine($x->map(function ($item, $key) {
        return $item->badgeclassID;
    })); */

    //ddd(Stackorder::where('id', 1)->first()->get_all_members_from_stack);

    //ddd(Stackorder::where('id', 1)->first()->get_all_members_badgeclassid_from_stack);


    // ddd($allRoles = Stackorder::where('id', 5)->first()->get_all_members_role_from_stack);
    /*  $stackgroupID = Stackorder::where('id', 1)->first()->stackgroupID;

    $u = Stackrole::where('stackgroupID', '=', $stackgroupID)->get()->map(function ($item, $key) {
        if ($item->relationship == 'partOf') {
            return [$item->part1 => 'part'];
        } else {
            return [$item->part1 => 'meta'];
        }
    });

    ddd($u->map(function ($item, $key) {
        return current($item);
    }));


    ddd($x->map(function ($item, $key) {
        return $item->stackmemberID;
    })->combine($u->unique()));*/

    //$allMembers = Stackorder::where('id', 1)->first()->get_all_members_from_stack;
    //$allBadgeclassID = Stackorder::where('id', 1)->first()->get_all_members_badgeclassid_from_stack;
    //$allRoles = Stackorder::where('id', 1)->first()->get_all_members_role_from_stack;

    //$x = Assertion::where('id', 1048)->first();

    //ddd($x->is_stackmember);

    //ddd($x->are_all_parts_if_stack);
    //ddd($x->is_metabadge_awarded_if_stack);
    //ddd($x->get_role_if_stack);

    //ddd($x->get_meta_badgeclassid_if_stack);




    //$contents = [];

    //$subContent = [];
    //$subContent[] = $x->recipient_last_name;
    //$subContent[] = $x->recipient_first_name;
    //$subContent[] = $x->originalIdentity;
    //$subContent[] = $x->get_meta_badgeclassid_if_stack;
    //$contents[] = $subContent;

    //ddd($contents);

    // $tmpPreAssertionID = (new ManageAssertionController)->enginePreAssertion($contents);

    // $tmpAssertionID = (new ManageAssertionController)->awardPreAssertionEngine($tmpPreAssertionID);
    // ddd($tmpAssertionID);
    //ddd($tmpPreAssertionID);
});


Route::get('assistantMain', [ManageAssistantController::class, 'showIssuingCompanies'])->middleware('assistant');;
Route::get('assistantcompany/{id}', [ManageAssistantController::class, 'showCompany'])->middleware('assistant');
Route::get('assistantCreatePreAssertion', [ManageAssistantController::class, 'beginCreatePreAssertion'])->middleware('assistant');
Route::get('assistantShowPreAssertions', [ManageAssistantController::class, 'showAllPreAssertion'])->middleware('assistant');
Route::post('assistantRecordPreAssertion', [ManageAssistantController::class, 'recordPreAssertion'])->middleware('assistant');

Route::get('assistantPreAssertions/{id}/edit', [ManageAssistantController::class, 'editPreAssertion'])->middleware('assistant');

Route::get('assistantShowAssertions', [ManageAssistantController::class, 'assistantShowAssertions'])->middleware('assistant');

Route::get('issuerMain', [ManageIssuerController::class, 'showIssuingCompanies'])->middleware('issuer');
Route::get('company/{id}', [ManageIssuerController::class, 'showCompany'])->middleware('issuer');
Route::get('issuerbadges/{id}/info', [ManageIssuerController::class, 'infoIssuerBadge']);
Route::get('issuerbadges/{id}/stack', [ManageIssuerController::class, 'stackIssuerBadge'])->middleware('issuer');
Route::post('issuerbadges/{id}/doStack', [ManageIssuerController::class, 'doIssuerBadgeStack'])->middleware('issuer');
Route::get('issuerbadges/{id}/settype', [ManageIssuerController::class, 'setIssuerBadgeType'])->middleware('issuer');
Route::get('issuerbadges/{id}/delete', [ManageIssuerController::class, 'deleteIssuerBadgeType'])->middleware('issuer');
Route::get('issuerbadges/{id}/edit', [ManageIssuerController::class, 'editIssuerBadgeclass'])->middleware('issuer');
Route::get('issuerbadges/{id}/multilangSettings', [ManageIssuerController::class, 'multilangMainScreen'])->middleware('issuer');
Route::get('issuerbadges/{id}/setoriginallang', [ManageIssuerController::class, 'setOriginalLang'])->middleware('issuer');
Route::post('setIssuerBadgeOriginalLang', [ManageIssuerController::class, 'setIssuerBadgeOriginalLang'])->middleware('issuer');
Route::get('issuerbadges/{id}/feedmultilangcontents', [ManageIssuerController::class, 'feedMultilangContents'])->middleware('issuer');
Route::post('recordmultilangfeed', [ManageIssuerController::class, 'recordMultilangFeed'])->middleware('issuer');
Route::post('issuerbadges/{id}/editBadgeclass', [ManageIssuerController::class, 'updateIssuerBadgeclass'])->middleware('issuer');

Route::post('recordBadgeType', [ManageIssuerController::class, 'recordIssuerBadgeType'])->middleware('issuer');
//Route::get('assertions/{id}/filltyperecords',  [ManageAssertionController::class, 'fillTypeRecords'])->middleware('issuer');

Route::get('assertions/{id}/info',  [ManageAssertionController::class, 'showAssertionInfo']);
Route::get('assertions/{id}/send',  [ManageAssertionController::class, 'sendAssertionByEmail'])->middleware('issuer');
Route::get('assertions/{id}/revoke',  [ManageAssertionController::class, 'revokeAssertion'])->middleware('issuer');
Route::get('assertions/{id}/questionRevoke',  [ManageAssertionController::class, 'questionRevoke'])->middleware('issuer');
Route::post('finalRevoke',  [ManageAssertionController::class, 'finalRevoke'])->middleware('issuer');
Route::post('doSendingAssertionByEmail2', [ManageAssertionController::class, 'doSendingAssertionByEmail2'])->middleware('issuer');
Route::get('assertions/{id}/send2',  [ManageAssertionController::class, 'sendAssertionLetterChoosing'])->middleware('issuer');
Route::get('assertions/{id}/send3',  [ManageAssertionController::class, 'sendAssertionByEmail3'])->middleware('issuer');
Route::post('assertionsMultipleNotification', [ManageAssertionController::class, 'multipleNotification'])->middleware('issuer');
Route::get('assertionsMultipleNotification',  [ManageAssertionController::class, 'multipleNotificationback'])->middleware('issuer');
Route::post('doSendingAssertionByMultipleEmail',  [ManageAssertionController::class, 'doSendingAssertionByMultipleEmail'])->middleware('issuer');

Route::get('assertions/{id}/assistantSend2',  [ManageAssertionController::class, 'assistantSendAssertionLetterChoosing'])->middleware('assistant');
Route::post('doAssistantSendingAssertionByEmail2', [ManageAssertionController::class, 'doAssistantSendingAssertionByEmail2'])->middleware('assistant');
Route::get('assertions/{id}/assistantsend3',  [ManageAssertionController::class, 'assistantSendAssertionByEmail3'])->middleware('assistant');
Route::post('assistantAssertionsMultipleNotification', [ManageAssertionController::class, 'assistantMultipleNotification'])->middleware('assistant');
Route::get('assistantAssertionsMultipleNotification',  [ManageAssertionController::class, 'assistantMultipleNotificationback'])->middleware('assistant');
Route::post('doAssistantSendingAssertionByMultipleEmail',  [ManageAssertionController::class, 'doAssistantSendingAssertionByMultipleEmail'])->middleware('assistant');

Route::post('recordAssertionType',  [ManageAssertionController::class, 'recordAssertionType'])->middleware('issuer');

Route::get('backtocompany', function () {
    if (session()->has('issuerID')) {
        return redirect('company/' . session()->get('issuerID'));
    }
    return redirect('home');
})->middleware('issuer');

Route::get('backtoassistantcompany', function () {
    if (session()->has('issuerID')) {
        return redirect('assistantcompany/' . session()->get('issuerID'));
    }
    return redirect('home');
})->middleware('assistant');

Route::get('createBadgeclass', [ManageIssuerController::class, 'showNewBadgeclass'])->middleware('issuer');
Route::post('createBadgeclass', [ManageIssuerController::class, 'doNewBadgeclass'])->middleware('issuer');

Route::get('earnerMain', [ManageEarnerController::class, 'showEarnerDashboard'])->middleware('earner');
Route::post('earnerMain', [ManageEarnerController::class, 'showEarnerDashboard'])->middleware('earner');
Route::get('addEarnerBadge', [ManageEarnerController::class, 'showEarnerBadgePage'])->middleware('earner');
Route::post('addEarnerBadge', [ManageEarnerController::class, 'doEarnerBadgeRecord'])->middleware('earner');
Route::get('addEarnerBadgeByURL', [ManageEarnerController::class, 'showEarnerBadgeAddByURL'])->middleware('earner');
Route::post('addEarnerBadgeByURL', [ManageEarnerController::class, 'doEarnerBadgeRecordByURL'])->middleware('earner');
Route::post('updateEarnerBadge', [ManageEarnerController::class, 'updateEarnerBadgeRecord'])->middleware('earner');
Route::post('earnerSearch', [ManageEarnerController::class, 'searchForEarnerBadge'])->middleware('earner');


Route::get('verifierMain', [ManageVerifierController::class, 'showVerifierDashboard'])->middleware('verifier');
Route::get('addVerifierBadge', [ManageVerifierController::class, 'showVerifierBadgePage'])->middleware('verifier');
Route::post('addVerifierBadge', [ManageVerifierController::class, 'doVerifierBadgeRecord'])->middleware('verifier');
Route::get('lookupEarnerBadges', [ManageVerifierController::class, 'lookupEarnerBadges'])->middleware('verifier');
Route::get('searchForVET', [ManageVerifierController::class, 'searchForVET'])->middleware('verifier');
Route::post('startSearchForVET', [ManageVerifierController::class, 'startSearchForVET'])->middleware('verifier');
Route::get('lookupIssuers', [ManageVerifierController::class, 'lookupIssuers'])->middleware('verifier');
Route::get('issuerBadgeclasses/{id}', [ManageVerifierController::class, 'showIssuerBadgeclasses'])->middleware('verifier');

Route::get('PNGInspector', [PNGinspector::class, 'showPNGinspector'])->middleware('admin');
Route::get('bulkUploadUserAndBadge', [ManageAssertionController::class, 'bulkUploadUserAndBadge'])->middleware('issuer');
Route::get('bulkUploadUser', [ManageAdminController::class, 'bulkUploadUser'])->middleware('admin');
Route::get('bulkAwardAssertions', [ManageAdminController::class, 'bulkAwardAssertions'])->middleware('admin');
Route::get('moveAssertionsToEarner', [ManageAdminController::class, 'moveAssertionsToEarner'])->middleware('admin');
Route::get('bulkUploadIssuers', [ManageAdminController::class, 'bulkUploadIssuers'])->middleware('admin');

Route::get('adminMain', [ManageAdminController::class, 'showAdminDashboard'])->middleware('admin');
Route::get('earnerStatistics', [ManageAdminController::class, 'showEarnerStatistics'])->middleware('admin');
Route::get('manageIssuers', [ManageAdminController::class, 'manageIssuers'])->middleware('admin');
Route::get('issuers/{id}/edit', [ManageAdminController::class, 'editIssuer'])->middleware('admin');
Route::get('issuers/{id}/issuedbadges', [ManageAdminController::class, 'showIssuedBadges'])->middleware('admin');
Route::post('updateIssuer', [ManageAdminController::class, 'updateIssuer'])->middleware('admin');
Route::get('addNewIssuer', [ManageAdminController::class, 'addNewIssuer'])->middleware('admin');
Route::post('addNewIssuer', [ManageAdminController::class, 'doNewIssuerRecord'])->middleware('admin');
Route::get('manageUsers', [ManageAdminController::class, 'manageUsers'])->middleware('admin');
Route::get('addNewUser', [ManageAdminController::class, 'addNewUser'])->middleware('admin');
Route::post('addNewUser', [ManageAdminController::class, 'doNewUserRecord'])->middleware('admin');

Route::get(
    '/admin/earner-statistics/badges',
    [ManageAdminController::class, 'loadEarnerBadges']
)->name('admin.earner-statistics.badges');

Route::get('users/{id}/editbyadmin', [ManageAdminController::class, 'editUserByAdmin'])->middleware('admin');
Route::post('updateUserByAdmin', [ManageAdminController::class, 'updateUser'])->middleware('admin');
Route::get('users/{id}/senddatabyadmin', [ManageAdminController::class, 'sendLoginDataByEmail'])->middleware('admin');

Route::get('users/editProfile', [ManageAdminController::class, 'editUserProfile']);
Route::post('users/editProfile', [ManageAdminController::class, 'recordUserProfile']);
Route::get('users/changePassword', [ManageAdminController::class, 'changeUserPassword']);
Route::post('users/changePassword', [ManageAdminController::class, 'recordUserPassword']);
Route::get('users/selfRegistration', [ManageAdminController::class, 'selfRegistration']);
Route::post('users/selfRegistration', [ManageAdminController::class, 'doSelfRegistration']);

Route::get('changeSiteSettings', [ManageAdminController::class, 'showSiteSettings'])->middleware('admin');
Route::post('admin/setSettings', [ManageAdminController::class, 'setSiteSettings'])->middleware('admin');

Route::get('runIssuerMetabadgeRules', [ManageMetabadgeRuleController::class, 'runIssuerMetabadgeRules'])->middleware('issuer');
Route::get('setIssuerMetabadgeRules', [ManageMetabadgeRuleController::class, 'setIssuerMetabadgeRules'])->middleware('issuer');
Route::get('newIssuerMetabadgeRules', [ManageMetabadgeRuleController::class, 'newIssuerMetabadgeRules'])->middleware('issuer');
Route::post('recordIssuerMetabadgeSettings', [ManageMetabadgeRuleController::class, 'recordIssuerMetabadgeSettings'])->middleware('issuer');
Route::post('addNewIssuerMetabadge', [ManageMetabadgeRuleController::class, 'doNewIssuerMetabadgeRules'])->middleware('issuer');


Route::get('b', function () {
    return ('B');
})->middleware('earner');

Route::get('c', function () {
    return ('C');
})->middleware('verifier');

Route::get('d', function () {
    return ('D');
})->middleware('admin');


// Consumer - read
Route::get('rgabor/{id}', [RomanGaborController::class, 'rgabor']);
Route::get('lc', [RomanGaborController::class, 'leetcode']);
Route::get('test', [RomanGaborController::class, 'test']);
Route::get('test2', [RomanGaborController::class, 'test2']);
Route::get('test3', [RomanGaborController::class, 'test3']);
Route::get('test4', [RomanGaborController::class, 'test4']);
Route::get('test5', [RomanGaborController::class, 'test5']);
Route::get('test6', [RomanGaborController::class, 'test6']);

// Consumer - write
Route::get('test7', [RomanGaborController::class, 'test7']);
Route::post('/badge-connect/hosts/{host}/register-write-client', [BadgeConnectConsumerController::class, 'registerWriteClient']);

Route::get('test8', [RomanGaborController::class, 'test8']);
Route::get('/badge-connect/hosts/{host}/connect-write', [BadgeConnectConsumerController::class, 'connectWrite']);
Route::get('/badge-connect/consumer/write/callback', [BadgeConnectConsumerController::class, 'callbackWrite']);


Route::get('test9', [RomanGaborController::class, 'test9']);
Route::post('/badge-connect/hosts/{host}/export-assertions/{assertion}', [BadgeConnectConsumerController::class, 'exportAssertion']);


Route::get('test10', [RomanGaborController::class, 'test10']);
Route::post('/badge-connect/hosts/{host}/export-profile', [BadgeConnectConsumerController::class, 'exportProfile']);


// Provider - ?? Ezt majd tisztázni kell
Route::post('/badge-connect/hosts/{host}/fetch-manifest', [BadgeConnectConsumerController::class, 'fetchManifest']);
Route::post('/badge-connect/hosts/{host}/register-client', [BadgeConnectConsumerController::class, 'registerClient']);
Route::get('/badge-connect/hosts/{host}/connect', [BadgeConnectConsumerController::class, 'connect']);
Route::get('/badge-connect/consumer/callback', [BadgeConnectConsumerController::class, 'callback']);
Route::get('/badge-connect/hosts', [BadgeConnectConsumerController::class, 'hosts']);
Route::post('/badge-connect/hosts/{host}/import-assertions', [BadgeConnectConsumerController::class, 'importAssertions']);
Route::post('/badge-connect/hosts/{host}/import-profile', [BadgeConnectConsumerController::class, 'importProfile']);


Route::get('readsvg', [RomanGaborController::class, 'readSVG']);
Route::get('readurl', [RomanGaborController::class, 'assertionLink']);
Route::get('converttojson', [RomanGaborController::class, 'convertTojson']);

Route::get('/.well-known/badgeconnect.json', BadgeConnectManifestController::class)
    ->name('badgeconnect.manifest');

Route::middleware('api')->post('/o/register', DCRController::class)->name('badgeconnect.register');
Route::middleware('api')
    ->post('/o/revoke_token', RevokeTokenController::class)
    ->name('badgeconnect.revoke');

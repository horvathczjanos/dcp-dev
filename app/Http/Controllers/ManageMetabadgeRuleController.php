<?php

namespace App\Http\Controllers;

use App\Models\Badgeclass;
use App\Models\Issuer;
use App\Models\IssuerMetabadgeRule;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ManageMetabadgeRuleController extends Controller
{

   public static function changeMetabadgeRuleText($name)
   {

      $vocab = [
         'ThisMuchOrMore' => 'ExactlyMore',
         'ExactlyAsMuch' => 'ExactlyOne',
         'ThisAndThatMuch' => 'AtLeastMore',
         'ThisOrThatMuch' => 'AtLeastOne',
      ];

      if (isset($vocab[$name])) {
         return ($vocab[$name]);
      }

      return ($name);
   }

   public function runIssuerMetabadgeRules()
   {
      $issuerID = session()->get('issuerID');

      $ruleContainer = IssuerMetabadgeRule::where('issuerID', '=', $issuerID)->where('enabled', '=', 1)->get();
      session()->put('ruleContainer', $ruleContainer);
      //ddd($ruleContainer);

      return view('issuerShowMetabadgeRules')
         ->with('issuerID', $issuerID)
         ->with('issuerName', Issuer::where('issuerID', '=', $issuerID)->first()->name)
         ->with('ruleContainer', $ruleContainer);
   }

   public function setIssuerMetabadgeRules()
   {
      $issuerID = session()->get('issuerID');

      $ruleContainer = IssuerMetabadgeRule::where('issuerID', '=', $issuerID)->where('deleted', '=', 0)->get();
      session()->put('ruleContainer', $ruleContainer);
      //ddd($ruleContainer);

      return view('issuerSetMetabadgeRules')
         ->with('issuerID', $issuerID)
         ->with('issuerName', Issuer::where('issuerID', '=', $issuerID)->first()->name)
         ->with('ruleContainer', $ruleContainer);
   }

   public function recordIssuerMetabadgeSettings(Request $request)
   {

      $issuerID = session()->get('issuerID');

      IssuerMetabadgeRule::where('issuerID', '=', $issuerID)->update(['enabled' => 0]);

      //ddd(session()->get('issuerID'));


      //ddd($request->all());

      if ($request->has('chkbx')) {

         foreach ($request->input('chkbx') as $key => $chkbx) {

            try {

               IssuerMetabadgeRule::where('issuerID', '=', $issuerID)->where('id', '=', $chkbx)->update(['enabled' => 1]);
            } catch (Exception $e) {
               ddd($e);
            }
         }
      }

      if ($request->has('chkbx_del')) {

         foreach ($request->input('chkbx_del') as $key => $chkbx_del) {

            try {

               IssuerMetabadgeRule::where('issuerID', '=', $issuerID)->where('id', '=', $chkbx_del)->update(['enabled' => 0, 'deleted' => 1]);
            } catch (Exception $e) {
               ddd($e);
            }
         }
      }

      return redirect('backtocompany');
   }

   public function newIssuerMetabadgeRules()
   {
      $issuerID = session()->get('issuerID');

      $ruleContainer = IssuerMetabadgeRule::where('issuerID', '=', $issuerID)->where('deleted', '=', '0')->get();
      session()->put('ruleContainer', $ruleContainer);




      return view('issuerNewMetabadgeRules')
         ->with('issuerID', $issuerID)
         ->with('issuerName', Issuer::where('issuerID', '=', $issuerID)->first()->name)
         ->with('ruleContainer', $ruleContainer)
         ->with('badgeclasslist', Badgeclass::where('issuerID', '=', $issuerID)->get());
   }


   public function doNewIssuerMetabadgeRules(Request $request)
   {


      $isError = false;
      $errorMesage[] = 'Error!';

      if (!$request->has('metabadgerule_name')) {
         $errorMesage[] = 'You have to add a name for Metabadge rule!';
         $isError = true;
      }


      if (!$request->has('metabadgerule')) {
         $errorMesage[] = 'You have to choose Metabadge rule!';
         $isError = true;
      }

      if (!$request->has('badgeclass')) {
         $errorMesage[] = 'You have to choose at least one Subject badge!';
         $isError = true;
      }

      $quantitytmp = array_filter($request->get('quantity'));

      $quantity = [];


      if (sizeof($quantitytmp) == 0) {
         $errorMesage[] = 'You have to set quantity of Subject badge!';
         $isError = true;
      } else {
         foreach ($quantitytmp as $key => $value) {
            $quantity[] = intval($value);
         }
      }

      if (!$request->has('metabadgeclass')) {
         $errorMesage[] = 'You have to choose a Metabadge!';
         $isError = true;
      }

      if (!$request->has('stopbadgeclass')) {
         $errorMesage[] = 'You have to choose at least one Stopbadge!';
         $isError = true;
      }

      $isValid = Validator::make($request->all(), [
         'metabadgerule_name' => 'required'
      ]);

      if ($isValid->fails()) {
         return back()
            ->withErrors($isValid);
      }

      //ddd($request->all());

      $nowTime = time();
      $id = hash('sha256', random_bytes(128) . $nowTime);

      $rule = '';


      switch ($request->get('metabadgerule')) {
         case '1':
            $rule = 'ThisMuchOrMore';

            break;

         case '2':
            $rule = 'ExactlyAsMuch';
            break;

         case '3':
            $rule = 'ThisAndThatMuch';
            break;

         case '4':
            $rule = 'ThisOrThatMuch';
            break;

         default:
            $errorMesage[] = 'Unknown Metabadge rule!';
            $isError = true;
            break;
      }


      $rulePack = [];

      $rulePack['id'] = $id;
      $rulePack['name'] = $request->get('metabadgerule_name');
      $rulePack['rule'] = $rule;
      $rulePack['badgeMustHaveRule'] = ['badgeCount' => $quantity, 'subjectBadge' => $request->get('badgeclass'), 'metaBadge' => $request->get('metabadgeclass')];
      $rulePack['stopBadges'] = $request->get('stopbadgeclass');


      $originalCriteria = Badgeclass::where('badgeclassID', '=', $rulePack['badgeMustHaveRule']['metaBadge'])->first()->criteria;
      $originalCriteria = preg_replace('/MeTaBaDGeRuLe:[\s\S]+?~~~/', '', $originalCriteria);
      $metabadgeText = ' MeTaBaDGeRuLe: ' . $rule . ' ~~~';
      Badgeclass::where('badgeclassID', '=', $rulePack['badgeMustHaveRule']['metaBadge'])->update(['criteria' => $originalCriteria . $metabadgeText]);

      //ddd(json_encode($rulePack, true));
      // ddd($request->get('metabadgeclass'));

      $newRule = new IssuerMetabadgeRule;

      $newRule->issuerID = session()->get('issuerID');
      $newRule->userID = session()->get('userID');
      $newRule->rulecontent = json_encode($rulePack, true);

      if ($request->has('metabadgerule_description')) {
         $newRule->description = json_encode($request->get('metabadgerule_description'), true);
      }

      $newRule->enabled = '1';
      $newRule->deleted = '0';


      if ($isError) {
         return back()
            ->withErrors($errorMesage);
      } else {

         //ddd($newRule);
         //ddd($rulePack);
         $newRule->save();

         return  redirect('backtocompany');
      }
   }
}

<?php

namespace App\Livewire;

use App\Http\Controllers\RomanGaborController;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class Messagebox extends Component
{


    public $standardMessage = 'Starting content...';
    public $flag = 0;
    public $dryRun = false;


    public function changeText()
    {

        $this->standardMessage = "New message is here!";
    }

    public function engine_realrun()
    {
        $this->dryRun = false;
        $this->engine();
    }


    public function engine_dryrun()
    {
        $this->dryRun = true;
        $this->engine();
    }

    public function engine()
    {

        $this->flag = 1;

        $this->standardMessage = "Start MetaBadge Engine!";

        if (NULL !== (session('ruleContainer'))) {
            $this->standardMessage  .= chr(0x0D) . chr(0x0A) . '...fetching data...';

            // $this->standardMessage  .= chr(0x0D) . chr(0x0A) . 'IssuerID: ' . session('container')['issuerID'];

            // $this->standardMessage  .= chr(0x0D) . chr(0x0A) . 'DryRun: ' . json_encode(session('container')['dryRun']);

            // $this->standardMessage  .= chr(0x0D) . chr(0x0A) . 'Number of rules: ' . sizeof(session('container')['rules']);


            //ddd(session('ruleContainer'));

            foreach (session('ruleContainer') as $key => $rule) {
                $ruleContent = json_decode($rule->rulecontent, true);

                //ddd($ruleContent);


                $this->standardMessage  .= chr(0x0D) . chr(0x0A);
                $this->standardMessage  .= chr(0x0D) . chr(0x0A) . 'Name of rule: ' . $ruleContent['name'];

                $rg = new RomanGaborController;




                $returnMessage = $rg->engineMetabadge($rule->issuerID, $ruleContent, $this->dryRun);

               // dd($returnMessage);

                foreach ($returnMessage as $key => $value) {

                    $originalID = $key;

                    $this->standardMessage  .= chr(0x0D) . chr(0x0A) . "Original ID: " . $originalID;

                    $messageKey = array_keys($value)[0];

                    $this->standardMessage  .= chr(0x0D) . chr(0x0A) . "Result: "  . $messageKey;

                    $messageBody = $value[$messageKey][0];

                    $this->standardMessage  .= chr(0x0D) . chr(0x0A) . $messageBody;

                    if ($messageKey == 'stop') {
                        $affectedAssertionIDs = $value[$messageKey][1];

                        foreach ($affectedAssertionIDs as $key2 => $assertionID) {
                            $this->standardMessage  .= chr(0x0D) . chr(0x0A) . $assertionID;
                        }
                    }
                }
            }
        } else {
            $this->standardMessage  .= chr(0x0D) . chr(0x0A) . '...no data, terminating...';
        }
    }


    public function view_assertions()
    {
        return  redirect('showAssertions');
    }

    public function xengine()
    {

        $this->standardMessage = "Start MetaBadge Engine!";



        $dryRun = false;
        $returnMessage = [];
        $issuerID = 'f4c649072ff246a448513c1df6c530e75ae763fc69341d3ec57b7502de6ed794';
        $rulePack = [];

        $rulePack['id'] = '001';
        $rulePack['name'] = 'Legalább 2 pirospötty a 2-es metabadge-hez.';
        $rulePack['rule'] = 'ThisMuchOrMore';
        $rulePack['badgeMustHaveRule'] = ['badgeCount' => [2], 'subjectBadge' => ['sha256$8fc0283ba1c93cc01fff245afb6277fcd7c310c1935b79e4da5ac9abd81b6f5c'], 'metaBadge' => 'sha256$12dda2f6f5afe7a8abc5e8a55739cfc22c712be998c76c120ceb15cf21448048'];
        $rulePack['stopBadges'] = ['sha256$12dda2f6f5afe7a8abc5e8a55739cfc22c712be998c76c120ceb15cf21448048', 'sha256$4be1a270921693a5c6f0126136433a91a7a5ddaa0ee5996a0ee56bb28b188ec6', 'sha256$2c14f9b9fed17b5943041f7c2cf41fc80bcac77700fa0184deaf7bb23904097d'];

        $rg = new RomanGaborController;

        $returnMessage = $rg->engineMetabadge($issuerID, $rulePack, $dryRun);

        foreach (array_keys($returnMessage) as $key => $value) {
            $this->standardMessage  .= chr(0x0D) . chr(0x0A) . $value;
        }



        foreach ($returnMessage as $key => $value) {

            $originalID = $key;

            $this->standardMessage  .= chr(0x0D) . chr(0x0A) . $originalID;

            $messageKey = array_keys($value)[0];

            $this->standardMessage  .= chr(0x0D) . chr(0x0A) . $messageKey;

            $messageBody = $value[$messageKey][0];

            $this->standardMessage  .= chr(0x0D) . chr(0x0A) . $messageBody;

            $affectedAssertionIDs = $value[$messageKey][1];

            foreach ($affectedAssertionIDs as $key2 => $assertionID) {
                $this->standardMessage  .= chr(0x0D) . chr(0x0A) . $assertionID;
            }


            //ddd($affectedAssertionIDs);
        }

        /*   foreach ($returnMessage as $key => $value) {
            //$this->standardMessage  .= '<br />' . array_keys($value);

            foreach (array_keys($value) as $key2 => $value2) {
                $this->standardMessage  .= '<br />' . $value2;
            }
        } */


        //ddd($returnMessage);

        //$this->standardMessage = array_keys($returnMessage[0])[0];
    }


    public function render()
    {
        return view('livewire.messagebox');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Assertion;
use App\Models\Badgeclass;
use App\Models\Badgeimage;
use App\Models\Issuer;
use App\Models\Stackorder;
use App\Models\Stackmember;
use App\Models\Stackrole;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class ManageIssuerController extends Controller
{
    public function showIssuingCompanies()
    {

        if (Issuer::where('adminID', Auth::id())->get()->toArray()) {


            return view('issuerMain')->with('companies', Issuer::get()->where('adminID', '=', Auth::id()));
        }


        return ('<h1>You have not got Issuing Comapnies!</h1>');
    }

    public function showCompany($id)
    {

        session()->forget('issuerBadgeclassID');
        session()->forget('assertionID');
        session()->forget('sessionAction');
        session()->forget('mailTemplate');
        session()->forget('details');
        session()->forget('controlButtons');
        session()->forget('whatRole');
        session()->forget('editingBadgeclassID');

        $badgeclassIDs = Badgeclass::select('badgeclassID')->where('status', '=', '1')->where('issuerID', $id)->get()->toArray();
        //ddd(Assertion::whereIn('badgeclassID', $badgeclassIDs)->get('badgeclassID'));
        session()->put('issuerID', $id);

        //ddd(session()->all());
        //ddd(Assertion::where('badgeclassID',$badgeclassIDs)->get());

        $issuer = Issuer::where('issuerID', $id)->first();
        $customFields = json_decode($issuer->customfield, true);

        return view('company')->with('company', Issuer::where('issuerID', $id)->first())
            ->with('customFields', $customFields)
            ->with('badgeclasses', Badgeclass::where('issuerID', $id)->where('status', '=', '1')->orderBy('created_at', 'desc')->get())
            //->with('badgeclasses', Badgeclass::where('issuerID', $id)->latest()->take(5)->get())
            ->with('assertions', Assertion::whereIn('badgeclassID', $badgeclassIDs)->orderBy('issuedOn', 'desc')->take(10)->get());
    }


    public function showNewBadgeclass()
    {


        return view('newBadgeclass');
    }

    public function infoIssuerBadge($id)
    {

        $thisBadgeClass = Badgeclass::where('badgeclassID', '=', $id)->where('status', '=', '1')->first();
        if ($thisBadgeClass->count()) {


            $badgeImage = Storage::get(substr((Badgeimage::where('badgeclassID', '=', $id)->first('filepath')->filepath), 1));


            $inStack = Stackorder::where('badgeclassID', '=', $id)->get();

            session()->put('issuerID', $thisBadgeClass->issuerID);

            return view('issuerBadgeInfopage')
                ->with('badgeImage', $badgeImage)
                ->with('badgeName', $thisBadgeClass->badge_name)
                ->with('badgeDescription', $thisBadgeClass->badge_description)
                ->with('badgeclassID', $thisBadgeClass->badgeclassID)
                ->with('thisBadgeClass', $thisBadgeClass)
                ->with('inStack', $inStack);
        }

        return redirect('issuerMain');
    }


    public function stackIssuerBadge($id)
    {

        $thisBadgeClass = Badgeclass::where('badgeclassID', '=', $id)->where('status', '=', '1')->first();

        if ($thisBadgeClass->count()) {


            $badgeImage = Storage::get(substr((Badgeimage::where('badgeclassID', '=', $id)->first('filepath')->filepath), 1));



            $inStack = Stackorder::where('badgeclassID', '=', $id)->get();

            // 20230913-15 közötti DEMO eseményhez a korábbi stack member beállításokat idő szerint kiszűrjük, nem jelennek meg választható opcióként
            $stackMembers = Stackmember::where('issuerID', '=', (session()->get('issuerID')))->where('created_at', '>', '2023-08-30')->get();


            return view('issuerBadgeStackpage')
                ->with('badgeImage', $badgeImage)
                ->with('badgeName', $thisBadgeClass->badge_name)
                ->with('inStack', $inStack)
                ->with('stackMembers', $stackMembers);
        }

        return redirect('issuerMain');
    }

    public function doIssuerBadgeStack(Request $request, $id)
    {

        if (($request->stackmember) == '0') {

            Stackorder::where('badgeclassID', '=', $id)->delete();
            $inStack = Stackorder::where('badgeclassID', '=', $id)->get();
            return back()
                ->with('success', __('hello.ob_dostack_success'))
                ->with('inStack', $inStack);
        }

        if ($validStackMember = Stackmember::where('stackmemberID', '=', ($request->stackmember))
            ->where('issuerID', '=', (session()->get('issuerID')))->first()
        ) {

            Stackorder::where('badgeclassID', '=', $id)->delete();

            $stackOrder = new Stackorder;

            //ddd(Stackrole::where('part1','=', ($request->stackmember))->first()->stackgroupID);

            $stackOrder->badgeclassID = $id;
            $stackOrder->stackgroupID = Stackrole::where('part1', '=', ($request->stackmember))->first()->stackgroupID;
            $stackOrder->stackmemberID = $request->stackmember;
            $stackOrder->issuerID = session()->get('issuerID');
            $stackOrder->status = '1';
            $stackOrder->save();

            $inStack = Stackorder::where('badgeclassID', '=', $id)->get();

            return back()
                ->with('success', __('hello.ob_dostack_success'))
                ->with('inStack', $inStack);
        }
        return back()
            ->withErrors('General error');
    }

    public function doNewBadgeclass(Request $request)
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
    public function setIssuerBadgeType($id)
    {
        $thisBadgeClass = Badgeclass::where('badgeclassID', '=', $id)->where('status', '=', '1')->first();

        if ($thisBadgeClass->count()) {

            session()->put('issuerBadgeclassID', $id);

            $badgeImage = Storage::get(substr((Badgeimage::where('badgeclassID', '=', $id)->first('filepath')->filepath), 1));

            $badgeType = json_decode($thisBadgeClass->type);


            return view('issuerSetBadgeType')
                ->with('badgeImage', $badgeImage)
                ->with('thisIssuerBadge', Badgeclass::where('badgeclassID', '=', $id)->where('status', '=', '1')->first())
                ->with('badgeType', $badgeType);
        }

        return redirect('issuerMain');
    }


    public static function getContentByLang($content, $lang)
    {

        if (is_array($content)) {

            $retvalue = '';

            foreach ($content as $item) {
                if ($item['lang'] == $lang) {

                    return ($item['content']);
                }
            }
            return ($retvalue);
        } else {
            return ($content);
        }
    }

    public static function getContentWithLangs($content)
    {
        if (is_array($content)) {

            $retvalue = '';

            foreach ($content as $item) {

                $retvalue .= '<span class="langcontent" lang="' . $item['lang'] . '" hidden>' . $item['content'] . '</span>';
            }



            return ($retvalue);
        } else {
            return ($content);
        }
    }


    public static function getContentWithLangsTextarea($content, $id, $disabled)
    {

        if (is_array($content)) {
            $retvalue = '';




            foreach ($content as $key => $item) {

                // Ez is rejtély, miért nem lehet az $item-ből kulcs alapján kiolvasni az értéket?
                //ddd($item);
                // Azért, mert asszociatív tömbben nem lehetnek azonos kulcsok. Objektumként kezeljük az adatokat
                // Nem, nem azért. Hanem mert a beolvasó JSON alapszerkezet volt az elvárthoz képest más. 
                // Illetve ha a hiba a tömb feldolgozásakor később jön, a ddd() csak az első lépést fogja meg.

                try {
                    //echo $a = $item['lang'];
                } catch (Exception $e) {

                    //ddd($content);
                    //ddd($item);

                    return ($e);
                }

                // $a = array_keys($item);

                //a = get_object_vars($item);
                $properties = array_keys((array)$item);

                $lang = '';
                $content = '';

                $langProp = $properties[0];
                $lang = ((array)$item)[$langProp];

                $contentProp = $properties[1];
                $content = ((array)$item)[$contentProp];

                //ddd($lang);





                // ddd($lang);
                /*    if ("lang" === $a[0]) {
                    $lang = $item["lang"];
                } else {
                }


                if ("content" === $a[1]) {
                    $content = $item["content"];
                } else {
                } */






                //ddd(gettype($lang));
                //print_r($item);
                //ddd(gettype($item));

                // ddd($item['lang']);

                //$lang = (string)($item['lang']);

                //ddd(((array)$item)['lang']);

                //ddd((array)$item);

                //$aItem = (array)$item;

                //dump($aItem['lang']);

                //$lang = ((array)$item)['lang'];

//                $retvalue .= '<textarea style="resize: none;" name="' . $id .   $item['lang'] . '" class="langcontent form-control" id="' . $id . '"rows="1"' . $disabled . ' lang="' . $item['lang'] . '" hidden >' . $item['content'] . '</textarea>';
                $retvalue .= '<span name="' . $id .   $item['lang'] . '" class="langcontent" id="' . $id . '" lang="' . $item['lang'] . '" hidden >' . $item['content'] . '</span>';
            }
        } else {



//            $retvalue = '<textarea style="resize: none;" name="' . $id . '" class="form-control" id="' . $id . '"rows="1"' . $disabled . '>' . $content . '</textarea>';
            $retvalue = '<span name="' . $id . '" id="' . $id . '" >' . $content . '</span>';
        }
        return ($retvalue);
    }


/*    public static function getContentWithLangsTable($content, $id, $disabled)
    {
        $retvalue = '';

        $header = '';
        $body = '';

        foreach ($content[0] as $key => $item) {
            $header .= $item . ' ';
        }

        foreach ($content[1] as $key => $item) {
            $body .= $item . ' ';
        }

        $retvalue = '<textarea style="resize: none;" name="' . $id . '" class="form-control" id="' . $id . '"rows="3"' . $disabled . '>' . $header . "\n" . $body . '</textarea>';
        //nl2br($disabled ."\n".$body )
        return ($retvalue);
    }*/

    public static function getContentWithLangsTable($content, $id, $disabled)
    {
        $retvalue = '';

        $header = '<table class="table" name="'.$id.'" id="'.$id.'"><tr>';
        $body = '<tr>';
	$footer = '</tr></table>';

        foreach ($content[0] as $key => $item) {
            $header .= '<th scope="col">'. $item . '</th>';
        }
	$header .= '</tr>';
        foreach ($content[1] as $key => $item) {
            $body .= '<td>'. $item . '</td>';
        }
	

//        $retvalue = '<textarea style="resize: none;" name="' . $id . '" class="form-control" id="' . $id . '"rows="3"' . $disabled . '>' . $header . "\n" . $body . '</textarea>';
	$retvalue = $header . $body . $footer;
        //nl2br($disabled ."\n".$body )
        return ($retvalue);
    }

    public function recordIssuerBadgeType(Request $request)
    {
        //ddd(session()->all());
        //ddd($request->all());


        if ($request->get('badgetypeversion') == '1.2') {
            // ZD needs other type of data saving


            $thisBadgeClass = Badgeclass::where('badgeclassID', '=', session()->get('issuerBadgeclassID'))->where('status', '=', '1')->first();

            if ($thisBadgeClass->count()) {
                $badgeType = json_decode($thisBadgeClass->type, true);

                foreach ($badgeType['form'] as &$form) {
                    foreach ($form['fields'] as &$field) {
                        foreach ($request->all() as $inputfield => $value) {

                            if (str_contains($inputfield, $field['id'])) {

                                if (strlen($field['id']) == strlen($inputfield)) {
                                    $field['load'] = $value;
                                } else {
                                    $langcode = str_replace($field['id'], '', $inputfield);

                                    //ddd($field['content']);

                                    foreach ($field['load'] as &$content) {
                                        if ($content['lang'] == $langcode) {

                                            //ddd($value);

                                            $content['content'] = $value;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                //ddd($badgeType);
                $thisBadgeClass->update(['type' => json_encode($badgeType)]);
                session()->forget('issuerBadgeclassID');
                return redirect('company/' . session()->get('issuerID'));
            }
        } else {

            $thisBadgeClass = Badgeclass::where('badgeclassID', '=', session()->get('issuerBadgeclassID'))->where('status', '=', '1')->first();

            if ($thisBadgeClass->count()) {
                $badgeType = json_decode($thisBadgeClass->type, true);

                //ddd('asdFGH');
                //ddd($badgeType);

                $settings = $request->get('badgetype');



                //ddd($settings);
                if (!is_null($settings)) {

                    foreach ($badgeType['form'] as &$form) {
                        $form['selected'] = 'false';
                        foreach ($settings as $newItem) {
                            if ($form['id'] == $newItem) {
                                $form['selected'] = 'true';
                            }
                        }
                        if ($form['name'] == 'General') {
                            $form['selected'] = 'true';
                        }
                    }
                } else {
                    foreach ($badgeType['form'] as &$form) {
                        $form['selected'] = 'false';
                        if ($form['name'] == 'General') {
                            $form['selected'] = 'true';
                        }
                    }
                }


                foreach ($badgeType['form'] as &$form) {
                    foreach ($form['fields'] as &$field) {
                        foreach ($request->all() as $inputfield => $value) {

                            if ($field['id'] == $inputfield) {

                                $field['content'] = $value;
                            }
                        }
                    }
                }


                $thisBadgeClass->update(['type' => json_encode($badgeType)]);
                // ddd($badgeType);

                $relatedAssertions = Assertion::where('badgeclassID', '=', session()->get('issuerBadgeclassID'))->get();
                foreach ($relatedAssertions as $assertion) {
                    $assertionType = json_decode($assertion->assertiontype, true);
                    //ddd($assertionType);

                    if (!is_null($assertionType)) {

                        // ddd($assertionType);
                        $selectedItems = array_column($badgeType['form'], 'selected');
                        //ddd($selectedItems);

                        $index = 0;
                        foreach ($assertionType['form'] as &$form) {
                            //ddd(array_search($form['id'], $badgeType['form']));
                            //$form['selected']=$badgeType['form'][$form]['selected'];
                            $form['selected'] = $selectedItems[$index];
                            $index++;
                        }
                        $assertion->update(['assertiontype' => json_encode($assertionType)]);
                    } else {
                        $assertion->update(['assertiontype' => json_encode($badgeType)]);
                    }
                }

                session()->forget('issuerBadgeclassID');
                return redirect('company/' . session()->get('issuerID'));
            }
        }
        return redirect('issuerMain');
    }

    public function deleteIssuerBadgeType($id)
    {


        //ddd(Badgeclass::where('id', '=', $id)->first());

        if ($thisBadgeClass = Badgeclass::where('badgeclassID', '=', $id)->where('status', '=', '1')->where('issuerID', '=', session()->get('issuerID'))->first()) {

            $thisBadgeClass->status = '0';


            $thisBadgeClass->save();
        }


        return redirect('backtocompany');
    }


    public function editIssuerBadgeclass($id)
    {

        if ($thisBadgeClass = Badgeclass::where('badgeclassID', '=', $id)->where('status', '=', '1')->where('issuerID', '=', session()->get('issuerID'))->first()) {

            if ($thisBadgeImage = BadgeImage::where('badgeclassID', '=', $id)->first()) {
                session()->put('editingBadgeclassID', $id);
                return view('issuerEditBadgeclass')
                    ->with('thisBadgeClass', $thisBadgeClass)
                    ->with('thisBadgeImage', $thisBadgeImage);
            }
        }


        return redirect('backtocompany');
    }

    public function updateIssuerBadgeclass(Request $request)
    {

        $isError = false;
        $errorMesage[] = 'Error!';



        if ($request->has('file')) {
            $isValid = Validator::make($request->all(), [
                //'file' => 'required|mimetypes:image/jpeg,image/png,image/gif'
                'file' => 'required|mimetypes:image/png'
            ]);

            if ($isValid->fails()) {
                return back()
                    ->withErrors($isValid);
            }
        }




        if ($isError) {
            return back()
                ->withErrors($errorMesage);
        } else {

            $thisBadgeClass = Badgeclass::where('badgeclassID', '=', session()->get('editingBadgeclassID'))->first();
            $thisBadgeImage = BadgeImage::where('badgeclassID', '=', session()->get('editingBadgeclassID'))->first();

            if (!is_null($request->file)) {
                $fileName = time() . '_' . $request->file->getClientOriginalName();
                $filePath1 = $request->file('file')->storeAs('uploads', $fileName, 'public');
                $filePath2 = $request->file('file')->storeAs('images', $fileName, 'public');
                $thisBadgeImage->outputfilename = 'image.png';
                $thisBadgeImage->filepath = '/public/' . $filePath2;
            }



            $thisBadgeImage->imgauthorname = $request->badgeclass_image_author_name;
            $thisBadgeImage->imgauthoremail = $request->badgeclass_image_author_email;
            $thisBadgeImage->imgauthorurl = $request->badgeclass_image_author_url;
            $thisBadgeImage->imgcaption = $request->badgeclass_image_caption;

            $thisBadgeClass->name = $request->badgeclass_name;
            $thisBadgeClass->description = str_replace(array("\n", "\r"), ' * ', $request->badgeclass_description);
            $thisBadgeClass->criteria = str_replace(array("\n", "\r"), ' * ', $request->badgeclass_criteria);


            $thisBadgeClass->tags = json_encode(array_map('ltrim', explode(';', $request->badgeclass_tags)));

            $thisBadgeClass->save();
            $thisBadgeImage->save();
        }


        return redirect('backtocompany');
    }

    public function multilangMainScreen($id)
    {
        if ($thisBadgeClass = Badgeclass::where('badgeclassID', '=', $id)->where('status', '=', '1')->where('issuerID', '=', session()->get('issuerID'))->first()) {

            $badgeImage = Storage::get(substr((Badgeimage::where('badgeclassID', '=', $id)->first('filepath')->filepath), 1));

            return view('issuerMultilangSettings')
                ->with('thisBadgeClass', $thisBadgeClass)
                ->with('badgeImage', $badgeImage);
        }

        return redirect('backtocompany');
    }

    public function setOriginalLang($id)
    {
        if ($thisBadgeClass = Badgeclass::where('badgeclassID', '=', $id)->where('status', '=', '1')->where('issuerID', '=', session()->get('issuerID'))->first()) {

            $badgeImage = Storage::get(substr((Badgeimage::where('badgeclassID', '=', $id)->first('filepath')->filepath), 1));


            $languages = json_decode(env('LANGUAGES'), true);

            session()->put('editingBadgeclassID', $id);


            return view('issuerOriginalLangSetting')
                ->with('thisBadgeClass', $thisBadgeClass)
                ->with('badgeImage', $badgeImage)
                ->with('languages', $languages);

            //ddd($languages['hu']);
        }

        return redirect('backtocompany');
    }


    public function setIssuerBadgeOriginalLang(Request $request)
    {


        if (array_key_exists('language', $request->all()) && array_key_exists($request->get('language'), json_decode(env('LANGUAGES'), true))) {

            $thisBadgeClass = Badgeclass::where('badgeclassID', '=', session()->get('editingBadgeclassID'))->first();

            if (is_null($thisBadgeClass->originalLang)) {
                $thisBadgeClass->multilangPack = Storage::get('public/badgeclassmultilang.json');
            }

            $thisBadgeClass->originalLang = $request->get('language');

            $thisBadgeClass->save();

            return redirect('backtocompany');
        } else {

            $errorMesage[] = 'Error!';
            $errorMesage[] = 'Select a valid language!';
            return back()
                ->withErrors($errorMesage);
        }
    }

    public function feedMultilangContents($id)
    {
        if ($thisBadgeClass = Badgeclass::where('badgeclassID', '=', $id)->where('status', '=', '1')->where('issuerID', '=', session()->get('issuerID'))->first()) {

            $thisBadgeImage = Badgeimage::where('badgeclassID', '=', $id)->first();
            $badgeImage = Storage::get(substr((Badgeimage::where('badgeclassID', '=', $id)->first('filepath')->filepath), 1));

            $languages = json_decode(env('LANGUAGES'), true);



            session()->put('editingBadgeclassID', $id);

            $multilangPack = json_decode($thisBadgeClass->multilangPack, true);

            $selectedLanguages = [];

            foreach ($multilangPack['form'] as $form) {

                if ($form['selected'] == 'true') {
                    $selectedLanguages[] = $form['id'];
                }
            }

            //ddd(in_array('hu', $selectedLanguages));
            //ddd($selectedLanguages);

            //ddd($multilangPack);

            $tagtext = '';
            $tags = json_decode($thisBadgeClass->tags);
            foreach ($tags as $tag) {
                $tagtext .= $tag . '; ';
            }
            $tagtext = rtrim($tagtext, '; ');

            return view('issuerMultilangFeederPage')
                ->with('thisBadgeClass', $thisBadgeClass)
                ->with('thisBadgeImage', $thisBadgeImage)
                ->with('badgeImage', $badgeImage)
                ->with('languages', $languages)
                ->with('selectedLanguages', $selectedLanguages)
                ->with('tagtext', $tagtext)
                ->with('multilangPack', $multilangPack);
        }

        return redirect('backtocompany');
    }

    public function recordMultilangFeed(Request $request)
    {
        //ddd($request->all());
        //ddd(session()->all());

        //ddd($request->get('language'));

        if ($thisBadgeClass = Badgeclass::where('badgeclassID', '=', session()->get('editingBadgeclassID'))->where('status', '=', '1')->where('issuerID', '=', session()->get('issuerID'))->first()) {

            $multilangPack = json_decode($thisBadgeClass->multilangPack, true);

            foreach ($multilangPack['form'] as $key1 => $pack) {
                $multilangPack['form'][$key1]['selected'] = 'false';
            }

            //ddd($multilangPack);
            if (!is_null($request->get('language'))) {

                foreach ($request->get('language') as $lang) {

                    //ddd($lang);
                    foreach ($multilangPack['form'] as $key1 => $pack) {
                        //$multilangPack['form'][$key1]['selected'] = 'false';
                        //ddd($pack['id'] == $lang);

                        if ($pack['id'] == $lang) {
                            $multilangPack['form'][$key1]['selected'] = 'true';
                            foreach ($pack['fields'] as $key2 => $field) {
                                if (strpos($field['id'], '_name') !== false) {
                                    $multilangPack['form'][$key1]['fields'][$key2]['content'] = $request->get('badgeclass_name_' . $lang);
                                } elseif (strpos($field['id'], '_description') !== false) {
                                    $multilangPack['form'][$key1]['fields'][$key2]['content'] = $request->get('badgeclass_description_' . $lang);
                                } elseif (strpos($field['id'], '_criteria') !== false) {
                                    $multilangPack['form'][$key1]['fields'][$key2]['content'] = $request->get('badgeclass_criteria_' . $lang);
                                } elseif (strpos($field['id'], '_tags') !== false) {
                                    $multilangPack['form'][$key1]['fields'][$key2]['content'] = $request->get('badgeclass_tags_' . $lang);
                                } elseif (strpos($field['id'], '_imageauthorname') !== false) {
                                    $multilangPack['form'][$key1]['fields'][$key2]['content'] = $request->get('badgeclass_image_author_name_' . $lang);
                                } elseif (strpos($field['id'], '_imagecaption') !== false) {
                                    $multilangPack['form'][$key1]['fields'][$key2]['content'] = $request->get('badgeclass_image_caption_' . $lang);
                                }
                            }
                        }
                    }
                }
            }

            //ddd($multilangPack);
            //ddd(json_encode($multilangPack));
            try {
                $thisBadgeClass->multilangPack = json_encode($multilangPack);
                $thisBadgeClass->save();
            } catch (Exception $e) {
                //ddd($e->getMessage());
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
        }


        return redirect('backtocompany');
    }
}

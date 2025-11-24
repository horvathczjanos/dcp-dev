<?php

namespace App\DCP;

use App\Http\Controllers\ShowDetailsController;
use App\Models\Assertion;
use App\Models\Badgeclass;
use App\Models\Issuer;
use DOMDocument;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\TryCatch;
use Throwable;

class CheckBadgeFile
{


    protected $report = [];
    protected $filePath = '\public\\';

    public function __construct(String $filePath)
    {
        $this->filePath .= $filePath;
    }

    public function isValidEmail($email){



        
    }

    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function generateRandomNumber($length = 10)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    private function pushToReport($key, $value)
    {
        $this->report[$key] = $value;
    }


    private function isPNG($content)
    {

        $png_signature = pack("C8", 137, 80, 78, 71, 13, 10, 26, 10);
        $header = substr($content, 0, 8);

        if ($header != $png_signature) {
            return (false);
        }

        return (true);
    }


    private function isSVG($content)
    {

        $doc = new DOMDocument();
        $doc->loadXML($content);

        $testSVG = $doc->getElementsByTagName("svg");

        if (sizeof($testSVG) == 1) {
            return (true);
        }

        return (false);
    }




    private function checkFileHeader($filePath)
    {



        $contents = Storage::get($filePath);


        if ($this->isPNG($contents)) {
            return ('PNG');
        } elseif ($this->isSVG($contents)) {
            return ('SVG');
        }

        return (false);
    }


    private function showPartsOfPNG($filePath)
    {

        $retArray = [];
        $position = 0;
        $contents = Storage::get($filePath);

        $fileSize = strlen($contents);

        $header = substr($contents, $position, 8);

        $retArray['Magic header'][] = $position;

        // Skip 8 bytes of IHDR image header.
        $position = 8;

        do {
            $chunk = @unpack('Nsize/a4type', substr($contents, $position, 8));

            $retArray[$chunk['type']][] = $position;

            // Skip 12 bytes chunk overhead.
            $position += $chunk['size'] + 12;
        } while ($position < $fileSize);

        //ddd($retArray);
        return ($retArray);
    }




    private function discoverFileParts($filePath, $filetype)
    {

        switch ($filetype) {
            case 'PNG':

                return ($this->showPartsOfPNG($filePath));


                break;


            case 'SVG':

                return ("SVG file");

                break;


            default:
                # code...
                break;
        }

        return (false);
    }


    private function getOBpartsOfPNG($filePath)
    {
        $retContent = false;
        $positionTEXT = 0;
        $contents = Storage::get($filePath);

        if (array_key_exists("iTXt", $this->report['fileParts'])) {


            //ddd(count($this->report['fileParts']['iTXt']));

            for ($i = 0; $i < count($this->report['fileParts']['iTXt']); $i++) {


                $positionTEXT = $this->report['fileParts']['iTXt'][$i];

                $chunk = @unpack('Nsize/a4type', substr($contents, $positionTEXT, 8));
                $chunkSize = $chunk['size'];

                $positionTEXT += 8;

                $chunkData = substr($contents, $positionTEXT, $chunkSize);

                $chunkInParts = explode("\0", $chunkData);

                //ddd($chunkInParts);


                if ($chunkInParts[0] === 'openbadges') {



                    return (end($chunkInParts));
                }
            }
        }

        if (array_key_exists("tEXt", $this->report['fileParts'])) {

            $positionTEXT = $this->report['fileParts']['tEXt'][0];

            $chunk = @unpack('Nsize/a4type', substr($contents, $positionTEXT, 8));
            $chunkSize = $chunk['size'];

            $positionTEXT += 8;

            $chunkData = substr($contents, $positionTEXT, $chunkSize);

            $chunkInParts = explode("\0", $chunkData);


            //ddd($chunkInParts);

            if ($chunkInParts[0] === 'openbadges') {
                return (end($chunkInParts));
            }
        }

        return $retContent;
    }


    private function getOBpartsOfSVG($filePath)
    {

        $retContent = false;
        $xmlContents = Storage::get($filePath);

        $doc = new DOMDocument();
        $doc->loadXML($xmlContents);

        $assertionPart = $doc->getElementsByTagName("assertion");


        //$assertionJSON = json_decode(($assertionPart[0]->childNodes)[0]->textContent, true);

        $assertionTXT = ($assertionPart[0]->childNodes)[0]->textContent;

        //ddd((string)$assertionTXT);
        //ddd(strlen($assertionTXT));

        if (strlen($assertionTXT)) {
            return ($assertionTXT);
        }



        return $retContent;
    }


    private function getOBpart($filePath, $filetype)
    {


        switch ($filetype) {
            case 'PNG':

                return ($this->getOBpartsOfPNG($filePath));


                break;

            case 'SVG':

                return ($this->getOBpartsOfSVG($filePath));

                break;

            default:
                # code...
                break;
        }

        return (false);
    }

    private function checkOB()
    {

        $OBpart = $this->report['OBpart'];

        //ddd($this);

        // There are many types of open badge in point of view of definition file
        // Old moodle implementations, e.g.



        $jsonObject = json_decode($OBpart, true);


        if (json_last_error() !== JSON_ERROR_NONE) {



            ini_set("allow_url_fopen", 1);
            $json = file_get_contents($OBpart);
            $jsonObject = json_decode($json, true);

            //ddd($jsonObject);
        }



        if (!is_null($jsonObject)) {


            //ddd($jsonObject);
            //ddd(array_keys($jsonObject));

            $checkList = [];

            if (array_key_exists('id', $jsonObject)) {

                $checkList['type'] = $jsonObject['type'];
                $checkList['id'] = $jsonObject['id'];

                if (is_array($jsonObject['badge'])) {
                    $checkList['badge'] = $jsonObject['badge']['id'];
                } else {
                    $checkList['badge'] = $jsonObject['badge'];
                }


                try {
                    $checkList['verification'] = $jsonObject['verification'];
                } catch (Throwable $e) {
                    $checkList['verification'] = $jsonObject['verify'];
                }
                $checkList['issuedOn'] = $jsonObject['issuedOn'];

                try {
                    $checkList['expires'] = $jsonObject['expires'];
                } catch (Throwable $e) {
                }


                try {
                    if (is_array($jsonObject['evidence'])) {
                        $checkList['evidence'] = $jsonObject['evidence']['id'];
                    } else {

                        $checkList['evidence'] = $jsonObject['evidence'];
                    }
                } catch (Throwable $e) {
                }


                // Get badgeclass
                if (strpos($checkList['badge'], env('APP_URL') . '/badgeclasses') !== false) {

                    $x = explode("/", $checkList['badge']);
                    $badgeID = end($x);

                    //ddd($badgeID);

                    if (false == ($b = Badgeclass::where('badgeclassID', '=', $badgeID)->first())) {
                        return (false);
                    } else {

                        $this->showDetailsCont = new ShowDetailsController;
                        $result = $this->showDetailsCont->showBadgeClass($badgeID);
                        $json3 = json_decode($result->content(), true);

                        $checkList['badgeClass'] = $json3;
                        //ddd($json3);
                    }
                } else {
                    ini_set("allow_url_fopen", 1);
                    try {
                        if (false === ($data = file_get_contents($checkList['badge']))) {
                            // We can not get data from own app domain! !!!!!!!
                            ddd("BadgeClass check");
                        } else {
                            $json3 = json_decode($data, true);
                            $checkList['badgeClass'] = $json3;
                            //ddd($json3);
                        }
                    } catch (Throwable $e) {
                        return (false);
                    }
                }


                //ddd($checkList);

                //Get issuers's profile
                if (is_array($checkList['badgeClass']['issuer'])) {
                    $checkList['issuer'] = $checkList['badgeClass']['issuer'];
                } else {


                    if (strpos($checkList['badgeClass']['issuer'], env('APP_URL') . '/organizations') !== false) {

                        $x = explode("/", $checkList['badgeClass']['issuer']);
                        $issuerID = end($x);

                        if (false == ($b = Issuer::where('issuerID', '=', $issuerID)->first())) {
                            return (false);
                        } else {

                            $this->showDetailsCont = new ShowDetailsController;
                            $result = $this->showDetailsCont->showOrganization($issuerID);
                            $json3 = json_decode($result->content(), true);

                            $checkList['issuer'] = $json3;
                            //ddd($json3);
                        }
                    } else {

                        ini_set("allow_url_fopen", 1);
                        if (false === ($data = file_get_contents($checkList['badgeClass']['issuer']))) {
                            // We can not get data from own app domain! !!!!!!!
                        } else {
                            $json3 = json_decode($data, true);
                            $checkList['issuer'] = $json3;
                            //ddd($json3);
                        }
                    }
                }

                // Get recipient

                if (strpos($checkList['id'], env('APP_URL') . '/assertion') !== false) {
                    $x = explode("/", $checkList['id']);
                    $assertionID = end($x);


                    if (false == ($a = Assertion::where('assertionID', '=', $assertionID)->first())) {
                        return (false);
                    } else {

                        $checkList['recipient']['hashed'] = true;
                        $checkList['recipient']['type'] = $a->recipientType;
                        $checkList['recipient']['identity'] = $a->identity;
                        $checkList['recipient']['salt'] = $a->salt;
                    }
                } else {
                    ini_set("allow_url_fopen", 1);
                    if (false === ($data = file_get_contents($checkList['id']))) {
                        // We can not get data from own app domain! !!!!!!!
                    } else {
                        $json2 = json_decode($data, true);

                        $checkList['recipient'] = $json2['recipient'];
                    }
                }

                // ddd($checkList);


                // Get assertion
                if (strpos($checkList['id'], env('APP_URL') . '/assertion') !== false) {

                    $x = explode("/", $checkList['id']);
                    $assertionID = end($x);


                    if (false == ($a = Assertion::where('assertionID', '=', $assertionID)->first())) {
                        return (false);
                    } else {
                        //ddd($checkList);

                        //ddd('Hello own server!');

                        $this->showDetailsCont = new ShowDetailsController;

                        $result = $this->showDetailsCont->showAssertion($assertionID);

                        //ddd($result->content());

                        $json2 = json_decode($result->content(), true);

                        //ddd($json2);

                        if ($jsonObject == $json2) {

                            return ($checkList);
                        } else {

                            // There is an issue with issuedOn record, different time format in use !!
                            return ($checkList);
                            //ddd("Assertion problem 1" . serialize($jsonObject) . '<br>' . serialize($json2));
                        }
                    }
                } else {
                    ini_set("allow_url_fopen", 1);
                    if (false === ($data = file_get_contents($checkList['id']))) {
                        // We can not get data from own app domain! !!!!!!!
                    } else {
                        $json2 = json_decode($data, true);


                        // ddd(hash('md5', serialize($json2['recipient'])));
                        //ddd(array_keys($json2));
                        //ddd($checkList);
                        //ddd(array_diff($jsonObject, $json2));
                        //ddd(gettype($json2));

                        if ($jsonObject == $json2) {

                            return ($checkList);
                        } else {

                            //$json2[] = ['Hello' => 5];

                            //ddd($json2);

                            if (array_key_exists('verify', $json2)) {
                                $json2['verification'] = $json2['verify'];
                                unset($json2['verify']);
                            }

                            //ddd($json2);
                            //ddd(array_keys($jsonObject));

                            foreach (array_keys($jsonObject) as $key) {

                                // ddd(array_keys($json2));

                                if (serialize($jsonObject[$key]) != serialize($json2[$key])) {

                                    //ddd(array(serialize($jsonObject[$key]), serialize($json2[$key])));

                                    return false;
                                }
                            }
                            return ($checkList);

                            //ddd($jsonObject.' *** '. $json2);
                            //ddd(array_diff($jsonObject, $json2));
                            ddd("Assertion problem 2" . serialize($jsonObject) . '<br>' . serialize($json2));
                        }
                    }
                }
            }

            //ddd($checkList);

            // This code is for old style openbadges
            /*  if (array_key_exists('uid', $jsonObject) && array_key_exists('id', $jsonObject)) {

                $checkList['id'] = $jsonObject['uid'];
                $checkList['badge'] = $jsonObject['badge'];
                $checkList['verification'] = $jsonObject['verify'];
                $checkList['issuedOn'] = $jsonObject['issuedOn'];





                return ($checkList);
            } */
        }

        return (false);
    }


    public function getReportOfBadgeFile()
    {


        $summaryValue = false;

        $this->pushToReport('filePath', $this->filePath);
        $this->pushToReport('fileType', $this->checkFileHeader($this->filePath));



        if ($this->report['fileType']) {
            $this->pushToReport('fileParts', $this->discoverFileParts($this->filePath, $this->report['fileType']));



            $this->pushToReport('OBpart', $this->getOBpart($this->filePath, $this->report['fileType']));





            //ddd($this->report['OBpart']);

            if ($this->report['OBpart']) {
                $this->pushToReport('checkOB', $this->checkOB());

               


                if ($this->report['checkOB']) {

                   
                    $summaryValue = true;
                }
            }
        }

        $this->pushToReport('summary', $summaryValue);



        return ($this->report);
    }


    function getITXTchunkSAMPLE()
    {

        $type = "iTXt";

        /*   $iTXtText = <<<EOF
        { "@context": "https://w3id.org/openbadges/v2", "type": "Assertion", "id": "https://api.badgr.io/public/assertions/215QIcnaRcGmpnXs8ZSItA", "badge": "https://api.badgr.io/public/badges/U8cuzhs7T3SRJVhDCSFHEA", "image": "https://api.badgr.io/public/assertions/215QIcnaRcGmpnXs8ZSItA/image", "verification": { "type": "HostedBadge" }, "issuedOn": "2021-01-20T11:03:53.487118+00:00", "recipient": { "hashed": true, "type": "email", "identity": "sha256$c46ca9ec60fabb8772651ef14f08416f0f23656915389746cd078c4cd595ce71", "salt": "a5803241288e488e9b8785606dfd10f4" } }
        
        EOF;;
    */
        $iTXtText = <<<EOF
        {
            "@context": "https://w3id.org/openbadges/v2",
            "type": "Assertion",
            "id": "http://badgr2.mti.bme.hu:8080/assertion_01.json",
            "recipient": {
                "hashed": true,
                "type": "email",
                "identity": "sha256\$c46ca9ec60fabb8772651ef14f08416f0f23656915389746cd078c4cd595ce71",
                "salt": "a5803241288e488e9b8785606dfd10f4"
            },
            "image": {
                "id": "http://badgr2.mti.bme.hu:8080/HCzJ_BMEMTI_1mon.png"
            },
            "evidence": "http://badgr2.mti.bme.hu:8080/sample-evidence.html",
            "issuedOn": "2021-02-01T11:03:53.487Z",
            "badge": "http://badgr2.mti.bme.hu:8080/badge_01.json",
            "verification": {
                "type": "HostedBadge"
            }
        }
        EOF;;

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

        $dataparts[] = $iTXtText;

        $data = implode("", $dataparts);

        $crc = pack("N", crc32($type . $data));
        $len = pack("N", strlen($data));

        // Chunk format: length + type + data + CRC.
        // CRC is a CRC-32 computed over the chunk type and chunk data.
        $newchunk = $len . $type . $data . $crc;

        return $newchunk;
    }

    public function setOBchunkToPNG($destFile, $newchunk)
    {

        $fileContent = file_get_contents($destFile);
        $fileSize = strlen($fileContent);

        $result = substr($fileContent, 0, $fileSize - 12)
            . $newchunk
            . substr($fileContent, $fileSize - 12, 12);


        $fileNewName = $destFile . "_" . $this->generateRandomString(16) . ".png";

        file_put_contents($fileNewName, $result);



        $resultHTML = "<br />
        FileName = " . $destFile . "<br />
        Sizre = " . $fileSize . "<br />
        ";

        echo ($resultHTML);

        //echo getInfoFromBadgeFile($fileNewName);
    }
}

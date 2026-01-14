<?php

namespace App\Http\Controllers;

use App\DCP\CheckBadgeFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MakeBadgeController extends Controller
{
    public function showMakeBadge()
    {
        return view('makeBadge');
    }

    public function getAssertionID()
    {
        return (env('APP_URL') . "/assertions/13579");
    }

    public function getRecipient()
    {

        $recipient = [];
        $recipient['hashed'] = $this->getRecipientHashed();
        $recipient['type'] = $this->getRecipientType();
        $recipient['salt'] = $this->getAgentSalt();
        $recipient['identity'] = $this->getRecipientIdentity($recipient['salt']);

        return $recipient;
    }

    public function getRecipientHashed()
    {
        return true;
    }

    public function getRecipientType()
    {
        return 'email';
    }

    public function getOriginalIdentity()
    {

        return "info@mti.bme.hu";
    }

    public function getRecipientIdentity($salt)
    {

        return "sha256$" . hash('sha256', ($this->getOriginalIdentity()) . ($salt));
    }

    public function getAgentSalt()
    {
        return hash('sha256', random_bytes(128));
    }

    public function getAsseretionImage()
    {

        $image = [];
        $image['id'] = env('APP_URL') . "/badgeclasses/008/image";

        return $image;
    }

    public function getAssertionIssuedOn()
    {

        return "2021-09-28T11:11:52.487Z";
    }

    public function getAssertionEvidence()
    {
        return env('APP_URL') . "/evidences/13579";
    }

    public function getAssertionBadgeClass()
    {
        return env('APP_URL') . "/badgeclasses/008";
    }


    public function getAssertionVerificationType()
    {

        $type = [];
        $type['type'] = "HostedBadge";

        return $type;
    }

    public function createBadge(Request $request)
    {

        $chunkType = "iTXt";

        $context = "https://w3id.org/openbadges/v2";
        $type = "Assertion";


        $iTXtText = '{
            "@context": "https://w3id.org/openbadges/v2",
            "type": "Assertion",
            "id": "' . env('APP_URL') . '/assertions/5678",
            "recipient": {
                "hashed": true,
                "type": "email",
                "identity": "sha256$c46ca9ec60fabb8772651ef14f08416f0f23656915389746cd078c4cd595ce71",
                "salt": "a5803241288e488e9b8785606dfd10f4"
            },
            "image": {
                "id": "' . env('APP_URL') . '/badgeclasses/007/image"
            },
            "evidence": "' . env('APP_URL') . 'evidences/5678",
            "issuedOn": "2021-09-01T11:11:52.487Z",
            "badge": "' . env('APP_URL') . '/badgeclasses/007",
            "verification": {
                "type": "HostedBadge"
            }
        }';


        $iTXtText2 = [];

        $iTXtText2['@context'] = $context;
        $iTXtText2['type'] = $type;
        $iTXtText2['id'] = $this->getAssertionID();
        $iTXtText2['recipient'] = $this->getRecipient();
        $iTXtText2['image'] = $this->getAsseretionImage();
        $iTXtText2['evidence'] = $this->getAssertionEvidence();
        $iTXtText2['issuedOn'] = $this->getAssertionIssuedOn();
        $iTXtText2['badge'] = $this->getAssertionBadgeClass();
        $iTXtText2['verification'] = $this->getAssertionVerificationType();


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

        $dataparts[] = json_encode($iTXtText2);

        //ddd(json_encode($iTXtText2));

        $data = implode("", $dataparts);

        $crc = pack("N", crc32($chunkType . $data));
        $len = pack("N", strlen($data));

        // Chunk format: length + type + data + CRC.
        // CRC is a CRC-32 computed over the chunk type and chunk data.
        $newchunk = $len . $chunkType . $data . $crc;


        $fileContent = Storage::get('public/images/discovet_demo.png');
        $fileSize = strlen($fileContent);

        $result = substr($fileContent, 0, $fileSize - 12)
            . $newchunk
            . substr($fileContent, $fileSize - 12, 12);


        $fileNewName = "X" . "_" . "asd" . ".png";

        $headers = ['Content-Type: application/png'];

        $data_to_pass = ['a' => 'test'];

        return response($result, $status = 200)
            ->header('Content-Type', 'application/png')
            ->header('Content-disposition', 'attachment; filename="test.png"');
        /*  return response(view('main')->with(compact('data_to_pass')), 200, [
            'Content-Type' => 'application/xml', // use your required mime type
            'Content-Disposition' => 'attachment; filename="filename.xml"',
        ]); */

        //return ('This is a BADGE!');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MTItempController extends Controller
{

    public function inData(Request $request)
    {


        $inData = json_decode($request->getContent(), true);

        $actTime = gmdate("Y-m-d\TH:i:s\Z", time());

        $id = $inData['id'];
        $temperature = $inData['temperature'];
        $abspressure = $inData['abspressure'];
        $relpressure = $inData['relpressure'];

        $retData = $actTime . ", " . $id . ", " . $temperature . ", " . $abspressure . ", " . $relpressure;

        Storage::disk('local')->append('example.txt', $retData);

        return ($retData);
    }
}

<?php

namespace App\Http\Controllers;

use App\DCP\CheckBadgeFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class CheckBadgeController extends Controller
{


    public function mainCheckBadge()
    {
        return view('checkBadge');
    }

    public function doCheckBadge(Request $request)
    {

        $isValid = Validator::make($request->all(), [
            'file' => 'required|mimes:png, svg'
        ]);

        if ($isValid->fails()) {
            return back()
                ->withErrors($isValid);
        } else {
            if ($request->file()) {
                $fileName = time() . '_' . $request->file->getClientOriginalName();
                $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');

                $myReport = new CheckBadgeFile($filePath);

                $backData = $myReport->getReportOfBadgeFile();

                

                if ($backData['summary']) {
                    return back()
                        ->with('success', __('hello.ob_upload_success'))
                        ->with('id', $backData['checkOB']['id'])
                        ->with('badge', $backData['checkOB']['badge'])
                        ->with('issuedOn', $backData['checkOB']['issuedOn']);
                } else {

                    //ddd($backData);

                    return back()
                        ->withErrors(['This is not a valid open badge!']);
                }


                /*    return back()
                    ->with('success', 'File has been uploaded.')
                    ->with('filepath', $filePath)
                    ->with('file', $fileName);
                    */
            }
        }
    }
}

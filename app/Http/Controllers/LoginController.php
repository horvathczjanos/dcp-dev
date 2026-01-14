<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function doLogin(Request $request)
    {

        $isValid = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($isValid->fails()) {
            return back()
                ->withErrors($isValid);
        } else {

            if ($user = User::where('username', "=", $request->get('username'))->where('password', "=", $request->get('password'))->first()) {

                $userID = $user->id;

                session()->put('userID', $userID);
                Auth::loginUsingId($userID);

                $nowTime = time();

                $user->lastlogin = date("Y-m-d\TH:i:s\Z", $nowTime);
                $user->save();

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
                // Log::info("Login, " . $userid  . ", " . $username . ", "  . json_encode($subject) . ", " . \Request::ip() . ", " . \Request::userAgent());
            }
            return redirect('abc');
        }
    }
}

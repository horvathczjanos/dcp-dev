<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class SettingsController extends Controller
{
    public function setLanguages(Request $request)
    {

        //ddd($request->session()->all());
        //ddd($request->session()->missing('userIDx'));

        if ($request->session()->missing('locale')) {
            $request->session()->put('locale', 'en');
        }

        $locale = $request->session()->get('locale');
        return view('settings', ['locale' => $locale]);
    }

    public function storeLanguages(Request $request)
    {
        $locale = $request->input('language');
        $request->session()->put('locale', $locale);

        return  redirect('abc');
    }
}

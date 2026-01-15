<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    /* protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
                     
           return route('main');
        }
    } */


    protected function redirectTo($request)
    {
        // API / OAuth hívásoknál SOHA ne redirecteljen HTML-re
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        // Webes kéréseknél mehet a login oldal
        return route('login');
    }
}

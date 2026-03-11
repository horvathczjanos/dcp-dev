<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforcePkceOnAuthorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {


        // csak az authorize végpontot szigorítjuk
        if ($request->is('oauth/authorize') && $request->isMethod('GET')) {


            $challenge = $request->query('code_challenge');
            $method    = $request->query('code_challenge_method');


            // OB2.1: code_challenge_method MUST be S256 :contentReference[oaicite:4]{index=4}
            if (!$challenge || $method !== 'S256') {

                return response()->json([
                    'error' => 'invalid_request',
                    'error_description' => 'PKCE required: code_challenge + code_challenge_method=S256',
                ], 400);
            }
            session()->put('pkce_ok', true);
        }

        return $next($request);
    }
}

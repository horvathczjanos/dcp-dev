<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */


/* Route::middleware(['auth:api'])->get('/me', function (Request $request) {
    return [
        'id' => $request->user()->id,
        'email' => $request->user()->email,
    ];
}); */

/* Route::get('/debug-auth', function (Request $r) {
    return [
        'has_auth_header' => $r->hasHeader('Authorization'),
        'auth_header' => $r->header('Authorization') ? 'present' : 'missing',
        'user' => optional($r->user())->id,
    ];
}); */


Route::middleware('auth:api')->get('/debug-auth', function (Request $request) {
    return [
        'has_auth_header' => $request->hasHeader('Authorization'),
        'auth_header' => $request->header('Authorization') ? 'present' : 'missing',
        'user' => [
            'id' => $request->user()->id,
            'email' => $request->user()->email,
        ],
    ];
});

Route::middleware('auth:api')->get('/me', function (Request $request) {
    return response()->json([
        'id' => $request->user()->id,
        'email' => $request->user()->email,
    ]);
});

/*Route::middleware('auth:api')->get('/me', function (Request $request) {
    return [
        'id' => $request->user()->id,
        'email' => $request->user()->email,
    ];
}); */

Route::get('/debug-auth', function () {
    return response()->json([
        'authorization_header' => request()->header('Authorization'),
        'server_var' => $_SERVER['HTTP_AUTHORIZATION'] ?? null,
    ]);
});

Route::middleware(['auth:api', 'scope:badges:read'])->get('/badges', function (Request $request) {
    return response()->json([
        'user_id' => $request->user()->id,
        'badges' => [],
    ]);
});

Route::middleware(['auth:api', 'scope:profile:read'])->get('/profile', function (Request $request) {
    return response()->json([
        'user_id' => $request->user()->id,
        'message' => ['Hello from Profile Scope'],
    ]);
});

Route::prefix('ims/ob/v2p1')
    ->middleware('auth:api')
    ->group(function () {
        Route::get('/profile', function (Request $request) {
            if (! $request->user()->tokenCan('https://purl.imsglobal.org/spec/ob/v2p1/scope/profile.readonly')) {
                return response()->json([
                    'error' => 'insufficient_scope',
                    'message' => 'Missing required scope: profile.readonly',
                ], 403);
            }

            return response()->json([
                '@context' => 'https://purl.imsglobal.org/spec/ob/v2p1/ob_v2p1.jsonld',
                'type' => 'Profile',
                'id' => url('/api/ims/ob/v2p1/profile'),
                'name' => $request->user()->name ?? $request->user()->email,
                'email' => $request->user()->email,
            ]);
        });
    });

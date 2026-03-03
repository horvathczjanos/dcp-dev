<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BadgeConnectManifestController extends Controller
{
    public function __invoke(): JsonResponse
    {


        $base = rtrim(config('app.url'), '/');

        // Biztonság: api_base_path-ot normalizáljuk (nincs záró perjel)
        $apiBasePath = '/' . ltrim(config('badgeconnect.api_base_path'), '/');
        $apiBase = $base . rtrim($apiBasePath, '/');

        // Kötelező elvárás: .../ims/ob/v2p1 végződés

        if (!Str::endsWith($apiBase, '/ims/ob/v2p1')) {

            $apiBase = $base . '/api/ims/ob/v2p1';
        }

        $imagePath = config('badgeconnect.image', '/assets/logo.png');
        $tosPath = config('badgeconnect.terms_url', '/terms');
        $privacyPath = config('badgeconnect.privacy_url', '/privacy');

        $registrationPath = config('badgeconnect.registration_path', '/o/register');
        $revocationPath = config('badgeconnect.revocation_path', '/o/revoke_token');

        $manifest = [
            '@context' => 'https://purl.imsglobal.org/spec/ob/v2p1/ob_v2p1.jsonld',
            'id' => $base . '/.well-known/badgeconnect.json',
            'badgeConnectAPI' => [[
                'name' => config('badgeconnect.name', 'My BadgeConnect Provider'),
                'image' => Str::startsWith($imagePath, 'http')
                    ? $imagePath
                    : $base . '/' . ltrim($imagePath, '/'),

                'apiBase' => $apiBase,
                'version' => 'v2p1',

                'termsOfServiceUrl' => Str::startsWith($tosPath, 'http')
                    ? $tosPath
                    : $base . '/' . ltrim($tosPath, '/'),

                'privacyPolicyUrl' => Str::startsWith($privacyPath, 'http')
                    ? $privacyPath
                    : $base . '/' . ltrim($privacyPath, '/'),

                'scopesOffered' => array_values(config('badgeconnect.scopes_offered', [])),

                // DCR (RFC7591)
                'registrationUrl' => Str::startsWith($registrationPath, 'http')
                    ? $registrationPath
                    : $base . '/' . ltrim($registrationPath, '/'),

                // OAuth endpoints (Passport)
                'authorizationUrl' => $base . '/oauth/authorize',
                'tokenUrl' => $base . '/oauth/token',

                // Token revocation (RFC7009) 
                'tokenRevocationUrl' => Str::startsWith($revocationPath, 'http')
                    ? $revocationPath
                    : $base . '/' . ltrim($revocationPath, '/'),
            ]],
        ];


        return response()
            ->json($manifest, 200, [], JSON_UNESCAPED_SLASHES);
    }
}

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Provider Metadata
    |--------------------------------------------------------------------------
    */

    'provider' => [
        'name' => env('BADGECONNECT_NAME', 'DCP Provider'),
        'image' => env('BADGECONNECT_IMAGE', '/assets/logo.png'),
        'terms_url' => env('BADGECONNECT_TOS_URL', '/terms'),
        'privacy_url' => env('BADGECONNECT_PRIVACY_URL', '/privacy'),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    */

    'api' => [
        // MUST end with /ims/ob/v2p1
        'base_path' => env('BADGECONNECT_API_BASE_PATH', '/api/ims/ob/v2p1'),
        'version' => 'v2p1',
    ],

    /*
    |--------------------------------------------------------------------------
    | OAuth Configuration
    |--------------------------------------------------------------------------
    */

    'oauth' => [
        'registration_path' => env('BADGECONNECT_REGISTRATION_PATH', '/o/register'),
        'authorization_path' => '/oauth/authorize',
        'token_path' => '/oauth/token',
        'revocation_path' => env('BADGECONNECT_REVOCATION_PATH', '/o/revoke_token'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Scopes Offered (OB2.1 URI scopes)
    |--------------------------------------------------------------------------
    */

    'scopes_offered' => [
        'https://purl.imsglobal.org/spec/ob/v2p1/scope/profile.readonly',
        'https://purl.imsglobal.org/spec/ob/v2p1/scope/profile.update',
        'https://purl.imsglobal.org/spec/ob/v2p1/scope/assertion.readonly',
        'https://purl.imsglobal.org/spec/ob/v2p1/scope/assertion.create',
        'offline_access',
    ],

];

<?php


namespace App\Http\Controllers;

use App\Models\BadgeAssertionsV21;
use App\Models\BadgeConnectExportedAssertion;

use App\Models\BadgeConnectAuthorizationSession;
use App\Models\BadgeConnectExportedProfile;
use App\Models\BadgeConnectImportedAssertion;
use App\Models\BadgeConnectImportedProfile;
use App\Models\BadgeConnectRemoteToken;
use Illuminate\Support\Facades\Auth;



use App\Models\BadgeConnectHost;
use App\Models\BadgeConnectRemoteClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class BadgeConnectConsumerController extends Controller
{

    private function makeCodeVerifier(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(64)), '+/', '-_'), '=');
    }

    private function makeCodeChallenge(string $codeVerifier): string
    {
        return rtrim(strtr(
            base64_encode(hash('sha256', $codeVerifier, true)),
            '+/',
            '-_'
        ), '=');
    }


    public function fetchManifest(Request $request, BadgeConnectHost $host)
    {
        try {
            $response = Http::acceptJson()
                ->timeout(20)
                ->get($host->manifest_url);

            if (! $response->successful()) {
                throw new RuntimeException(
                    'Manifest lekérés sikertelen. HTTP státusz: ' . $response->status()
                );
            }

            $manifest = $response->json();

            if (! is_array($manifest)) {
                throw new RuntimeException('A manifest válasz nem érvényes JSON.');
            }

            if (! isset($manifest['badgeConnectAPI'])) {
                throw new RuntimeException('Hiányzik a badgeConnectAPI mező.');
            }

            if (! is_array($manifest['badgeConnectAPI']) || count($manifest['badgeConnectAPI']) === 0) {
                throw new RuntimeException('A badgeConnectAPI mező üres vagy hibás.');
            }

            $api = $manifest['badgeConnectAPI'][0];

            if (! is_array($api)) {
                throw new RuntimeException('A badgeConnectAPI első eleme hibás.');
            }

            $requiredFields = [
                'apiBase',
                'registrationUrl',
                'authorizationUrl',
                'tokenUrl',
            ];

            foreach ($requiredFields as $field) {
                if (empty($api[$field]) || ! is_string($api[$field])) {
                    throw new RuntimeException("Hiányzó vagy hibás manifest mező: {$field}");
                }

                if (! Str::startsWith($api[$field], 'https://')) {
                    throw new RuntimeException("A {$field} mezőnek HTTPS URL-nek kell lennie.");
                }
            }

            if (isset($api['scopesOffered']) && ! is_array($api['scopesOffered'])) {
                throw new RuntimeException('A scopesOffered mezőnek tömbnek kell lennie.');
            }

            $host->update([
                'api_base' => $api['apiBase'],
                'registration_url' => $api['registrationUrl'],
                'authorization_url' => $api['authorizationUrl'],
                'token_url' => $api['tokenUrl'],
                'token_revocation_url' => $api['tokenRevocationUrl'] ?? null,
                'scopes_offered' => $api['scopesOffered'] ?? [],
                'manifest_json' => $manifest,
                'manifest_fetched_at' => now(),
                'last_error' => null,
            ]);

            return back()->with('success', 'Manifest sikeresen lekérve és eltárolva.');
        } catch (Throwable $e) {
            $host->update([
                'last_error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'manifest' => $e->getMessage(),
            ]);
        }
    }

    public function registerClient(Request $request, BadgeConnectHost $host)
    {
        try {
            if (empty($host->registration_url)) {
                throw new RuntimeException('Hiányzik a registration_url. Előbb kérd le a manifestet.');
            }

            if (! Str::startsWith($host->registration_url, 'https://')) {
                throw new RuntimeException('A registration_url csak HTTPS lehet.');
            }

            $requestedScopes = [
                'https://purl.imsglobal.org/spec/ob/v2p1/scope/assertion.readonly',
                'https://purl.imsglobal.org/spec/ob/v2p1/scope/profile.readonly',
                'offline_access',
            ];

            $scopesOffered = $host->scopes_offered ?? [];

            $missingScopes = collect($requestedScopes)
                ->filter(fn($scope) => $scope !== 'offline_access')
                ->diff($scopesOffered)
                ->values();

            if ($missingScopes->isNotEmpty()) {
                throw new RuntimeException(
                    'A Host nem kínálja a szükséges read scope-okat: ' . $missingScopes->implode(', ')
                );
            }

            $scope = implode(' ', $requestedScopes);

            $redirectUri = url('/badge-connect/consumer/callback');

            $payload = [
                'client_name' => 'DCP',
                'client_uri' => config('app.url'),
                'logo_uri' => config('app.url') . '/images/logo.png',
                'tos_uri' => config('app.url') . '/terms',
                'policy_uri' => config('app.url') . '/privacy',
                'software_id' => 'dcp-openbadges-client',
                'software_version' => '1.0.0',
                'redirect_uris' => [
                    $redirectUri,
                ],
                'token_endpoint_auth_method' => 'client_secret_basic',
                'grant_types' => [
                    'authorization_code',
                    'refresh_token',
                ],
                'response_types' => [
                    'code',
                ],
                'scope' => $scope,
            ];

            $response = Http::acceptJson()
                ->asJson()
                ->timeout(20)
                ->post($host->registration_url, $payload);

            if (! $response->successful()) {
                throw new RuntimeException(
                    'Dynamic Client Registration sikertelen. HTTP státusz: '
                        . $response->status()
                        . ' Válasz: '
                        . $response->body()
                );
            }

            $json = $response->json();

            if (! is_array($json)) {
                throw new RuntimeException('A registration response nem érvényes JSON.');
            }

            foreach (['client_id', 'client_secret', 'client_id_issued_at'] as $field) {
                if (empty($json[$field])) {
                    throw new RuntimeException("Hiányzó registration response mező: {$field}");
                }
            }

            BadgeConnectRemoteClient::updateOrCreate(
                [
                    'host_id' => $host->id,
                ],
                [
                    'client_id' => $json['client_id'],
                    'client_secret' => $json['client_secret'],
                    'client_id_issued_at' => $json['client_id_issued_at'] ?? null,
                    'client_secret_expires_at' => $json['client_secret_expires_at'] ?? null,
                    'redirect_uri' => $redirectUri,
                    'scope' => $json['scope'] ?? $scope,
                    'registration_request' => $payload,
                    'registration_response' => $json,
                    'registered_at' => now(),
                    'last_error' => null,
                ]
            );

            return back()->with('success', 'Dynamic Client Registration sikeresen lefutott.');
        } catch (Throwable $e) {
            BadgeConnectRemoteClient::updateOrCreate(
                [
                    'host_id' => $host->id,
                ],
                [
                    'client_id' => 'registration_failed_' . Str::random(16),
                    'client_secret' => null,
                    'redirect_uri' => url('/badge-connect/consumer/callback'),
                    'scope' => '',
                    'last_error' => $e->getMessage(),
                ]
            );

            return back()->withErrors([
                'registration' => $e->getMessage(),
            ]);
        }
    }

    public function connect(BadgeConnectHost $host)
    {
        if (empty($host->authorization_url)) {
            return back()->withErrors([
                'connect' => 'Hiányzik az authorization_url. Előbb kérd le a manifestet.',
            ]);
        }

        if (! $host->remoteClient) {
            return back()->withErrors([
                'connect' => 'Nincs remote client registration. Előbb futtasd az auto-registrationt.',
            ]);
        }

        $client = $host->remoteClient;

        $codeVerifier = $this->makeCodeVerifier();
        $codeChallenge = $this->makeCodeChallenge($codeVerifier);
        $state = Str::random(64);

        BadgeConnectAuthorizationSession::create([
            'user_id' => Auth::id(),
            'host_id' => $host->id,
            'state' => $state,
            'code_verifier' => $codeVerifier,
            'code_challenge' => $codeChallenge,
            'requested_scope' => $client->scope,
            'expires_at' => now()->addMinutes(10),
        ]);

        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => $client->client_id,
            'redirect_uri' => $client->redirect_uri,
            'scope' => $client->scope,
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ], '', '&', PHP_QUERY_RFC3986);

        return redirect()->away($host->authorization_url . '?' . $query);
    }
    public function callback(Request $request)
    {
        /* dd([
            'code' => $request->get('code'),
            'state' => $request->get('state'),
            'error' => $request->get('error'),
            'error_description' => $request->get('error_description'),
        ]);*/

        try {
            if ($request->has('error')) {
                return redirect('/badge-connect/hosts')->withErrors([
                    'oauth' => $request->get('error') . ': ' . $request->get('error_description'),
                ]);
            }

            if (! $request->filled('code') || ! $request->filled('state')) {
                return redirect('/badge-connect/hosts')->withErrors([
                    'oauth' => 'Hiányzik a code vagy state paraméter.',
                ]);
            }

            $authSession = BadgeConnectAuthorizationSession::where('state', $request->get('state'))
                ->where('expires_at', '>', now())
                ->whereNull('used_at')
                ->first();

            if (! $authSession) {
                return redirect('/badge-connect/hosts')->withErrors([
                    'oauth' => 'Érvénytelen, lejárt vagy már felhasznált state.',
                ]);
            }

            if ((int) $authSession->user_id !== (int) Auth::id()) {
                return redirect('/badge-connect/hosts')->withErrors([
                    'oauth' => 'A callback nem ehhez a bejelentkezett felhasználóhoz tartozik.',
                ]);
            }

            $host = $authSession->host()->with('remoteClient')->first();

            if (! $host) {
                return redirect('/badge-connect/hosts')->withErrors([
                    'oauth' => 'Nem található a Host rekord.',
                ]);
            }

            if (! $host->remoteClient) {
                return redirect('/badge-connect/hosts')->withErrors([
                    'oauth' => 'Nem található remote client registration.',
                ]);
            }

            if (empty($host->token_url)) {
                return redirect('/badge-connect/hosts')->withErrors([
                    'oauth' => 'Hiányzik a Host token_url értéke.',
                ]);
            }

            $client = $host->remoteClient;

            $tokenResponse = Http::asForm()
                ->acceptJson()
                ->withBasicAuth($client->client_id, $client->client_secret)
                ->timeout(20)
                ->post($host->token_url, [
                    'grant_type' => 'authorization_code',
                    'code' => $request->get('code'),
                    'redirect_uri' => $client->redirect_uri,
                    'code_verifier' => $authSession->code_verifier,
                ]);

            if (! $tokenResponse->successful()) {
                return redirect('/badge-connect/hosts')->withErrors([
                    'oauth' => 'Token lekérés sikertelen. HTTP státusz: '
                        . $tokenResponse->status()
                        . ' Válasz: '
                        . $tokenResponse->body(),
                ]);
            }

            $tokenJson = $tokenResponse->json();

            if (! is_array($tokenJson)) {
                return redirect('/badge-connect/hosts')->withErrors([
                    'oauth' => 'A token válasz nem érvényes JSON.',
                ]);
            }

            if (empty($tokenJson['access_token'])) {
                return redirect('/badge-connect/hosts')->withErrors([
                    'oauth' => 'A token válaszból hiányzik az access_token.',
                ]);
            }

            $expiresAt = null;

            if (! empty($tokenJson['expires_in'])) {
                $expiresAt = now()->addSeconds((int) $tokenJson['expires_in']);
            }

            BadgeConnectRemoteToken::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'host_id' => $host->id,
                ],
                [
                    'access_token' => $tokenJson['access_token'],
                    'refresh_token' => $tokenJson['refresh_token'] ?? null,
                    'token_type' => $tokenJson['token_type'] ?? null,
                    'expires_in' => $tokenJson['expires_in'] ?? null,
                    'expires_at' => $expiresAt,
                    'scope' => $tokenJson['scope'] ?? $client->scope,
                    'token_response' => $tokenJson,
                ]
            );

            $authSession->update([
                'used_at' => now(),
            ]);

            return redirect('/badge-connect/hosts')->with(
                'success',
                'OAuth token sikeresen lekérve és eltárolva.'
            );
        } catch (Throwable $e) {
            return redirect('/badge-connect/hosts')->withErrors([
                'oauth' => $e->getMessage(),
            ]);
        }
    }

    public function hosts()
    {
        $hosts = BadgeConnectHost::with(['remoteClient'])->get();

        return view('hosts', compact('hosts'));
    }

    public function importAssertions(BadgeConnectHost $host)
    {
        if (empty($host->api_base)) {
            return back()->withErrors([
                'assertions' => 'Hiányzik az api_base. Előbb kérd le a manifestet.',
            ]);
        }

        $token = BadgeConnectRemoteToken::where('user_id', Auth::id())
            ->where('host_id', $host->id)
            ->first();

        if (! $token) {
            return back()->withErrors([
                'assertions' => 'Nincs mentett access token. Előbb futtasd az OAuth kapcsolódást.',
            ]);
        }

        $response = Http::withToken($token->access_token)
            ->acceptJson()
            ->timeout(20)
            ->get(rtrim($host->api_base, '/') . '/assertions', [
                'limit' => 100,
                'offset' => 0,
            ]);

        if (! $response->successful()) {
            return back()->withErrors([
                'assertions' => 'Assertion lekérés sikertelen. HTTP státusz: '
                    . $response->status()
                    . ' Válasz: '
                    . $response->body(),
            ]);
        }

        $json = $response->json();

        if (! is_array($json)) {
            return back()->withErrors([
                'assertions' => 'Az assertion válasz nem érvényes JSON.',
            ]);
        }

        $imported = 0;

        foreach ($json['assertions'] ?? [] as $assertion) {
            $externalId = $assertion['id'] ?? null;

            BadgeConnectImportedAssertion::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'host_id' => $host->id,
                    'external_id' => $externalId,
                ],
                [
                    'assertion_json' => $assertion,
                    'signed_assertion' => null,
                    'imported_at' => now(),
                ]
            );

            $imported++;
        }

        foreach ($json['signedAssertions'] ?? [] as $signedAssertion) {
            BadgeConnectImportedAssertion::create([
                'user_id' => Auth::id(),
                'host_id' => $host->id,
                'external_id' => null,
                'assertion_json' => null,
                'signed_assertion' => $signedAssertion,
                'imported_at' => now(),
            ]);

            $imported++;
        }

        return back()->with(
            'success',
            "Assertion import sikeres. Importált elemek száma: {$imported}"
        );
    }

    public function importProfile(BadgeConnectHost $host)
    {
        if (empty($host->api_base)) {
            return back()->withErrors([
                'profile' => 'Hiányzik az api_base. Előbb kérd le a manifestet.',
            ]);
        }

        $token = BadgeConnectRemoteToken::where('user_id', Auth::id())
            ->where('host_id', $host->id)
            ->first();

        if (! $token) {
            return back()->withErrors([
                'profile' => 'Nincs mentett access token. Előbb futtasd az OAuth kapcsolódást.',
            ]);
        }

        $response = Http::withToken($token->access_token)
            ->acceptJson()
            ->timeout(20)
            ->get(rtrim($host->api_base, '/') . '/profile');

        if (! $response->successful()) {
            return back()->withErrors([
                'profile' => 'Profile lekérés sikertelen. HTTP státusz: '
                    . $response->status()
                    . ' Válasz: '
                    . $response->body(),
            ]);
        }

        $json = $response->json();

        if (! is_array($json)) {
            return back()->withErrors([
                'profile' => 'A profile válasz nem érvényes JSON.',
            ]);
        }

        $profile = $json['profile'] ?? $json;

        BadgeConnectImportedProfile::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'host_id' => $host->id,
            ],
            [
                'profile_json' => $profile,
                'response_json' => $json,
                'imported_at' => now(),
            ]
        );

        return back()->with('success', 'Profile import sikeres.');
    }

    public function registerWriteClient(Request $request, BadgeConnectHost $host)
    {
        try {
            if (empty($host->registration_url)) {
                throw new RuntimeException('Hiányzik a registration_url. Előbb kérd le a manifestet.');
            }

            if (! Str::startsWith($host->registration_url, 'https://')) {
                throw new RuntimeException('A registration_url csak HTTPS lehet.');
            }

            $requestedScopes = [
                'https://purl.imsglobal.org/spec/ob/v2p1/scope/assertion.create',
                'https://purl.imsglobal.org/spec/ob/v2p1/scope/profile.update',
                'offline_access',
            ];

            $scopesOffered = $host->scopes_offered ?? [];

            $missingScopes = collect($requestedScopes)
                ->filter(fn($scope) => $scope !== 'offline_access')
                ->diff($scopesOffered)
                ->values();

            if ($missingScopes->isNotEmpty()) {
                throw new RuntimeException(
                    'A Host nem kínálja a szükséges write scope-okat: ' . $missingScopes->implode(', ')
                );
            }

            $scope = implode(' ', $requestedScopes);

            $redirectUri = url('/badge-connect/consumer/write/callback');

            $payload = [
                'client_name' => 'DCP Consumer Write',
                'client_uri' => config('app.url'),
                'logo_uri' => config('app.url') . '/images/logo.png',
                'tos_uri' => config('app.url') . '/terms',
                'policy_uri' => config('app.url') . '/privacy',
                'software_id' => 'dcp-openbadges-consumer-write',
                'software_version' => '1.0.0',
                'redirect_uris' => [
                    $redirectUri,
                ],
                'token_endpoint_auth_method' => 'client_secret_basic',
                'grant_types' => [
                    'authorization_code',
                    'refresh_token',
                ],
                'response_types' => [
                    'code',
                ],
                'scope' => $scope,
            ];

            $response = Http::acceptJson()
                ->asJson()
                ->timeout(20)
                ->post($host->registration_url, $payload);

            if (! $response->successful()) {
                throw new RuntimeException(
                    'Consumer Write registration sikertelen. HTTP státusz: '
                        . $response->status()
                        . ' Válasz: '
                        . $response->body()
                );
            }

            $json = $response->json();

            if (! is_array($json)) {
                throw new RuntimeException('A registration response nem érvényes JSON.');
            }

            if (empty($json['client_id'])) {
                throw new RuntimeException('Hiányzó registration response mező: client_id');
            }

            BadgeConnectRemoteClient::updateOrCreate(
                [
                    'host_id' => $host->id,
                    'mode' => 'consumer_write',
                ],
                [
                    'client_id' => $json['client_id'],
                    'client_secret' => $json['client_secret'] ?? null,
                    'client_id_issued_at' => $json['client_id_issued_at'] ?? null,
                    'client_secret_expires_at' => $json['client_secret_expires_at'] ?? null,
                    'redirect_uri' => $redirectUri,
                    'scope' => $json['scope'] ?? $scope,
                    'registration_request' => $payload,
                    'registration_response' => $json,
                    'registered_at' => now(),
                    'last_error' => null,
                ]
            );

            return back()->with('success', 'Consumer Write Dynamic Client Registration sikeresen lefutott.');
        } catch (Throwable $e) {
            return back()->withErrors([
                'registration_write' => $e->getMessage(),
            ]);
        }
    }


    public function connectWrite(BadgeConnectHost $host)
    {
        if (empty($host->authorization_url)) {
            return back()->withErrors([
                'connect_write' => 'Hiányzik az authorization_url. Előbb kérd le a manifestet.',
            ]);
        }

        $client = BadgeConnectRemoteClient::where('host_id', $host->id)
            ->where('mode', 'consumer_write')
            ->first();

        if (! $client) {
            return back()->withErrors([
                'connect_write' => 'Nincs consumer_write remote client. Előbb futtasd a Write auto-registrationt.',
            ]);
        }

        $codeVerifier = $this->makeCodeVerifier();
        $codeChallenge = $this->makeCodeChallenge($codeVerifier);
        $state = Str::random(64);

        BadgeConnectAuthorizationSession::create([
            'user_id' => Auth::id(),
            'host_id' => $host->id,
            'state' => $state,
            'code_verifier' => $codeVerifier,
            'code_challenge' => $codeChallenge,
            'requested_scope' => $client->scope,
            'expires_at' => now()->addMinutes(10),
        ]);

        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => $client->client_id,
            'redirect_uri' => $client->redirect_uri,
            'scope' => $client->scope,
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ], '', '&', PHP_QUERY_RFC3986);

        return redirect()->away($host->authorization_url . '?' . $query);
    }

    public function callbackWrite(Request $request)
    {
        try {
            if ($request->has('error')) {
                return redirect('/badge-connect/hosts')->withErrors([
                    'oauth_write' => $request->get('error') . ': ' . $request->get('error_description'),
                ]);
            }

            if (! $request->filled('code') || ! $request->filled('state')) {
                return redirect('/badge-connect/hosts')->withErrors([
                    'oauth_write' => 'Hiányzik a code vagy state paraméter.',
                ]);
            }

            $authSession = BadgeConnectAuthorizationSession::where('state', $request->get('state'))
                ->where('expires_at', '>', now())
                ->whereNull('used_at')
                ->first();

            if (! $authSession) {
                return redirect('/badge-connect/hosts')->withErrors([
                    'oauth_write' => 'Érvénytelen, lejárt vagy már felhasznált state.',
                ]);
            }

            if ((int) $authSession->user_id !== (int) Auth::id()) {
                return redirect('/badge-connect/hosts')->withErrors([
                    'oauth_write' => 'A callback nem ehhez a bejelentkezett felhasználóhoz tartozik.',
                ]);
            }

            $host = $authSession->host;

            $client = BadgeConnectRemoteClient::where('host_id', $host->id)
                ->where('mode', 'consumer_write')
                ->first();

            if (! $client) {
                return redirect('/badge-connect/hosts')->withErrors([
                    'oauth_write' => 'Nem található consumer_write remote client.',
                ]);
            }

            $tokenResponse = Http::asForm()
                ->acceptJson()
                ->withBasicAuth($client->client_id, $client->client_secret)
                ->timeout(20)
                ->post($host->token_url, [
                    'grant_type' => 'authorization_code',
                    'code' => $request->get('code'),
                    'redirect_uri' => $client->redirect_uri,
                    'code_verifier' => $authSession->code_verifier,
                ]);

            if (! $tokenResponse->successful()) {
                return redirect('/badge-connect/hosts')->withErrors([
                    'oauth_write' => 'Write token lekérés sikertelen. HTTP státusz: '
                        . $tokenResponse->status()
                        . ' Válasz: '
                        . $tokenResponse->body(),
                ]);
            }

            $tokenJson = $tokenResponse->json();

            if (empty($tokenJson['access_token'])) {
                return redirect('/badge-connect/hosts')->withErrors([
                    'oauth_write' => 'A write token válaszból hiányzik az access_token.',
                ]);
            }

            BadgeConnectRemoteToken::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'host_id' => $host->id,
                    'mode' => 'consumer_write',
                ],
                [
                    'access_token' => $tokenJson['access_token'],
                    'refresh_token' => $tokenJson['refresh_token'] ?? null,
                    'token_type' => $tokenJson['token_type'] ?? null,
                    'expires_in' => $tokenJson['expires_in'] ?? null,
                    'expires_at' => ! empty($tokenJson['expires_in'])
                        ? now()->addSeconds((int) $tokenJson['expires_in'])
                        : null,
                    'scope' => $tokenJson['scope'] ?? $client->scope,
                    'token_response' => $tokenJson,
                ]
            );

            $authSession->update([
                'used_at' => now(),
            ]);

            return redirect('/badge-connect/hosts')->with(
                'success',
                'Consumer Write OAuth token sikeresen lekérve.'
            );
        } catch (Throwable $e) {
            return redirect('/badge-connect/hosts')->withErrors([
                'oauth_write' => $e->getMessage(),
            ]);
        }
    }

    public function exportAssertion(BadgeConnectHost $host, BadgeAssertionsV21 $assertion)
    {
        if (empty($host->api_base)) {
            return back()->withErrors([
                'export' => 'Hiányzik az api_base. Előbb kérd le a manifestet.',
            ]);
        }

        $token = BadgeConnectRemoteToken::where('user_id', Auth::id())
            ->where('host_id', $host->id)
            ->where('mode', 'consumer_write')
            ->first();

        if (! $token) {
            return back()->withErrors([
                'export' => 'Nincs consumer_write access token. Előbb futtasd a Consumer Write OAuth kapcsolódást.',
            ]);
        }

        if ((int) $assertion->user_id !== (int) Auth::id()) {
            return back()->withErrors([
                'export' => 'Ez az assertion nem a bejelentkezett felhasználóhoz tartozik.',
            ]);
        }

        $assertionPayload = $assertion->payload;

        if (! is_array($assertionPayload)) {
            return back()->withErrors([
                'export' => 'Az assertion payload nem érvényes JSON objektum.',
            ]);
        }

        $requestPayload = [
            'assertion' => $assertionPayload,
        ];

        $response = Http::withToken($token->access_token)
            ->acceptJson()
            ->asJson()
            ->timeout(20)
            ->post(rtrim($host->api_base, '/') . '/assertions', $requestPayload);

        $responseJson = $response->json();

        BadgeConnectExportedAssertion::create([
            'user_id' => Auth::id(),
            'host_id' => $host->id,
            'assertion_id' => $assertion->id,
            'mode' => 'consumer_write',
            'request_json' => $requestPayload,
            'response_json' => is_array($responseJson) ? $responseJson : null,
            'http_status' => $response->status(),
            'success' => $response->successful(),
            'last_error' => $response->successful() ? null : $response->body(),
            'exported_at' => now(),
        ]);

        if (! $response->successful()) {
            return back()->withErrors([
                'export' => 'Assertion export sikertelen. HTTP státusz: '
                    . $response->status()
                    . ' Válasz: '
                    . $response->body(),
            ]);
        }

        return back()->with('success', 'Assertion sikeresen elküldve a külső Hostnak.');
    }


    public function exportProfile(BadgeConnectHost $host)
    {
        if (empty($host->api_base)) {
            return back()->withErrors([
                'profile_export' => 'Hiányzik az api_base. Előbb kérd le a manifestet.',
            ]);
        }

        $token = BadgeConnectRemoteToken::where('user_id', Auth::id())
            ->where('host_id', $host->id)
            ->where('mode', 'consumer_write')
            ->first();

        if (! $token) {
            return back()->withErrors([
                'profile_export' => 'Nincs consumer_write access token.',
            ]);
        }

        $user = Auth::user();

        $profilePayload = [
            '@context' => 'https://purl.imsglobal.org/spec/ob/v2p1/ob_v2p1.jsonld',
            'type' => 'Profile',
            'id' => url('/api/ims/ob/v2p1/profile/' . $user->id),
            'name' => $user->name ?? 'DCP User',
            'email' => $user->email,
        ];

        $requestPayload = [
            'profile' => $profilePayload,
        ];

        $response = Http::withToken($token->access_token)
            ->acceptJson()
            ->asJson()
            ->timeout(20)
            ->post(rtrim($host->api_base, '/') . '/profile', $requestPayload);

        $responseJson = $response->json();

        BadgeConnectExportedProfile::create([
            'user_id' => Auth::id(),
            'host_id' => $host->id,
            'request_json' => $requestPayload,
            'response_json' => is_array($responseJson) ? $responseJson : null,
            'http_status' => $response->status(),
            'success' => $response->successful(),
            'last_error' => $response->successful() ? null : $response->body(),
            'exported_at' => now(),
        ]);

        if (! $response->successful()) {
            return back()->withErrors([
                'profile_export' => 'Profile export sikertelen. HTTP státusz: '
                    . $response->status()
                    . ' Válasz: '
                    . $response->body(),
            ]);
        }

        return back()->with('success', 'Profile sikeresen elküldve a külső Hostnak.');
    }
}

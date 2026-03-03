<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Passport\Passport;
use Laravel\Passport\Client as PassportClient;

class DCRController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        // Badge Connect OB2.1: ezek kötelező mezők a regisztrációs kérésben. :contentReference[oaicite:2]{index=2}
        $data = $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'client_uri' => ['required', 'url', 'max:2048'],
            'logo_uri' => ['required', 'url', 'max:2048'],
            'tos_uri' => ['required', 'url', 'max:2048'],
            'policy_uri' => ['required', 'url', 'max:2048'],
            'software_id' => ['required', 'string', 'max:255'],
            'software_version' => ['required', 'string', 'max:64'],
            'redirect_uris' => ['required', 'array', 'min:1'],
            'redirect_uris.*' => ['required', 'url', 'max:2048'],

            // OB2.1: ebben a specifikációban csak client_secret_basic engedélyezett. :contentReference[oaicite:3]{index=3}
            'token_endpoint_auth_method' => ['sometimes', 'string', Rule::in(['client_secret_basic'])],

            // opcionális, de gyakori
            'grant_types' => ['sometimes', 'array'],
            'response_types' => ['sometimes', 'array'],
            'scope' => ['sometimes', 'string', 'max:4096'], // space separated scopes
        ]);

        // Ha nincs megadva, legyen a specifikáció szerinti alapértelmezett
        $tokenAuthMethod = $data['token_endpoint_auth_method'] ?? 'client_secret_basic';

        // Scope validálás (ajánlott): a kért scope-ok legyenek a manifest scopesOffered részhalmaza.
        // (Ha most még nem akarsz scope-ot ellenőrizni, ezt a blokkot ki is veheted.)
        if (!empty($data['scope'])) {
            $requested = preg_split('/\s+/', trim($data['scope'])) ?: [];
            $offered = config('badgeconnect.scopes_offered', []);
            $invalid = array_values(array_diff($requested, $offered));

            if (count($invalid) > 0) {
                return response()->json([
                    'error' => 'invalid_client_metadata',
                    'error_description' => 'Invalid scope(s) requested: ' . implode(', ', $invalid),
                ], 400);
            }
        }

        // Kliens secret generálás (Passport kompatibilis)
        $plainSecret = Str::random(48);

        // Ha hash-elt secretet használsz Passportban, itt tároljuk hash-elve, de a válaszban a plain secret megy ki.
        $secretForDb = (method_exists(Passport::class, 'hashesClientSecrets') && Passport::hashClientSecrets())
            ? Hash::make($plainSecret)
            : $plainSecret;

        // Passport oauth_clients.redirect egy string (több redirect URI-t tipikusan vesszővel tárolnak)
        $redirect = implode(',', $data['redirect_uris']);

        $issuedAt = time();
        $secretExpiresAt = 0; // OB2.1: lehet 0 (nem jár le). :contentReference[oaicite:4]{index=4}

        $client = DB::transaction(function () use ($data, $secretForDb, $redirect) {
            $client = new PassportClient();
            $client->user_id = null;                    // DCR-nél tipikusan nincs user-hez kötve
            $client->name = $data['client_name'];
            $client->secret = $secretForDb;
            $client->provider = null;
            $client->redirect = $redirect;
            $client->personal_access_client = false;
            $client->password_client = false;
            $client->revoked = false;
            $client->save();

            return $client;
        });

        // Válasz a Badge Connect OB2.1 mintája alapján: kötelező mezők + visszatükrözött metadata. :contentReference[oaicite:5]{index=5}
        $response = [
            'client_id' => (string) $client->id,
            'client_secret' => $plainSecret,
            'client_id_issued_at' => $issuedAt,
            'client_secret_expires_at' => $secretExpiresAt,

            // Echo back (a spec példái is így adják vissza)
            'client_name' => $data['client_name'],
            'client_uri' => $data['client_uri'],
            'logo_uri' => $data['logo_uri'],
            'tos_uri' => $data['tos_uri'],
            'policy_uri' => $data['policy_uri'],
            'software_id' => $data['software_id'],
            'software_version' => $data['software_version'],
            'redirect_uris' => $data['redirect_uris'],

            // Kért / default OAuth meta
            'token_endpoint_auth_method' => $tokenAuthMethod,
            'grant_types' => $data['grant_types'] ?? ['authorization_code', 'refresh_token'],
            'response_types' => $data['response_types'] ?? ['code'],
            'scope' => $data['scope'] ?? '',
        ];

        return response()->json($response, 201, [
            'Cache-Control' => 'no-store',
        ]);
    }
}

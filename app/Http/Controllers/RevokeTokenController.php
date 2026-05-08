<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Client;
use Laravel\Passport\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;

class RevokeTokenController extends Controller
{
    public function __invoke(
        Request $request,
        TokenRepository $tokenRepository,
        RefreshTokenRepository $refreshTokenRepository
    ): JsonResponse {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'token_type_hint' => ['nullable', 'in:access_token,refresh_token'],
        ]);

        // RFC7009 / Badge Connect szerint a kliens hitelesítse magát.
        // Itt Basic Auth-ból vesszük ki a kliens adatokat.
        $clientId = $request->getUser();       // Basic auth user = client_id
        $clientSecret = $request->getPassword();

        if (! $clientId || ! $clientSecret) {
            return response()->json([
                'error' => 'invalid_client',
                'error_description' => 'Client authentication failed.',
            ], 401);
        }

        /** @var Client|null $client */
        $client = Client::query()->find($clientId);

        if (! $client || $client->revoked) {
            return response()->json([
                'error' => 'invalid_client',
                'error_description' => 'Client authentication failed.',
            ], 401);
        }

        // Plain-text secret módban
        if ($client->secret !== $clientSecret) {
            return response()->json([
                'error' => 'invalid_client',
                'error_description' => 'Client authentication failed.',
            ], 401);
        }

        $token = $data['token'];
        $hint = $data['token_type_hint'] ?? null;

        // 1) Ha access tokennek tűnik vagy access_token hint érkezett
        if ($hint === 'access_token' || $this->looksLikeAccessTokenId($token)) {
            $accessToken = DB::table('oauth_access_tokens')->where('id', $token)->first();

            if ($accessToken) {
                // Opcionális: győződj meg róla, hogy a token ehhez a klienshez tartozik
                if ((string) $accessToken->client_id === (string) $clientId) {
                    $tokenRepository->revokeAccessToken($token);
                    $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token);
                }

                // RFC7009 szerint siker esetén 200, body nélkül is lehet
                return response()->json([], 200);
            }
        }

        // 2) Próbáld refresh tokenként kezelni
        $refreshToken = DB::table('oauth_refresh_tokens')->where('id', $token)->first();

        if ($refreshToken) {
            // A refresh token access tokenhez tartozik, azon keresztül nézzük a kliens ownershipet
            $accessToken = DB::table('oauth_access_tokens')->where('id', $refreshToken->access_token_id)->first();

            if ($accessToken && (string) $accessToken->client_id === (string) $clientId) {
                DB::table('oauth_refresh_tokens')
                    ->where('id', $token)
                    ->update(['revoked' => 1]);
            }

            // RFC7009: ha ismeretlen / már visszavont token jön, akkor is 200 lehet
            return response()->json([], 200);
        }

        // RFC7009 szerint ismeretlen tokenre is 200 OK adható.
        return response()->json([], 200);
    }

    private function looksLikeAccessTokenId(string $token): bool
    {
        // Passport access token id gyakran hosszú hex string
        return (bool) preg_match('/^[A-Za-z0-9\-_.]+$/', $token);
    }
}

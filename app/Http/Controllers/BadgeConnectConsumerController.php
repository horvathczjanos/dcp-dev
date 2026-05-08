<?php


namespace App\Http\Controllers;

use App\Models\BadgeConnectHost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class BadgeConnectConsumerController extends Controller
{
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
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BadgeAssertionsV21;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Assertion_v21 extends Controller
{
    private const SCOPE_READ = 'https://purl.imsglobal.org/spec/ob/v2p1/scope/assertion.readonly';
    private const SCOPE_CREATE = 'https://purl.imsglobal.org/spec/ob/v2p1/scope/assertion.create';

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user || ! $user->tokenCan(self::SCOPE_READ)) {
            return response()->json([
                'error' => 'insufficient_scope',
                'message' => 'Missing required scope: assertion.readonly',
            ], 403);
        }

        $limit = min(max((int) $request->query('limit', 20), 1), 100);
        $offset = max((int) $request->query('offset', 0), 0);

        $query = BadgeAssertionsV21::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id');

        if ($request->filled('since')) {
            $query->where('updated_at', '>=', $request->query('since'));
        }

        $items = $query->skip($offset)->take($limit)->get();

        return response()->json([
            '@context' => 'https://purl.imsglobal.org/spec/ob/v2p1/ob_v2p1.jsonld',
            'type' => 'AssertionPage',
            'id' => url('/ims/ob/v2p1/assertions'),
            'assertions' => $items->pluck('payload')->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user || ! $user->tokenCan(self::SCOPE_CREATE)) {
            return response()->json([
                'error' => 'insufficient_scope',
                'message' => 'Missing required scope: assertion.create',
            ], 403);
        }

        $data = $request->validate([
            'id' => ['nullable', 'string', 'max:255'],
            'recipient' => ['required', 'array'],
            'badge' => ['required', 'array'],
            'issuer' => ['required', 'array'],
            'image' => ['nullable', 'string', 'max:2048'],
            'evidence' => ['nullable', 'array'],
            'issuedOn' => ['nullable', 'date'],
            'expires' => ['nullable', 'date'],
        ]);

        $externalId = $data['id'] ?? 'urn:uuid:' . (string) Str::uuid();

        $payload = array_merge([
            '@context' => 'https://purl.imsglobal.org/spec/ob/v2p1/ob_v2p1.jsonld',
            'type' => 'Assertion',
            'id' => $externalId,
        ], $data);

        $assertion = BadgeAssertionsV21::create([
            'user_id' => $user->id,
            'external_id' => $externalId,
            'recipient' => $data['recipient'],
            'issued_on' => $data['issuedOn'] ?? now(),
            'expires_at' => $data['expires'] ?? null,
            'revoked' => false,
            'image' => $data['image'] ?? null,
            'evidence' => $data['evidence'] ?? null,
            'payload' => $payload,
        ]);

        return response()->json($assertion->payload, 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $user = $request->user();

        if (! $user || ! $user->tokenCan(self::SCOPE_READ)) {
            return response()->json([
                'error' => 'insufficient_scope',
                'message' => 'Missing required scope: assertion.readonly',
            ], 403);
        }

        $assertion = BadgeAssertionsV21::query()
            ->where('user_id', $user->id)
            ->where(function ($query) use ($id) {
                $query->where('id', $id)
                    ->orWhere('external_id', $id);
            })
            ->first();

        if (! $assertion) {
            return response()->json([
                'error' => 'not_found',
                'message' => 'Assertion not found.',
            ], 404);
        }

        return response()->json($assertion->payload);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Proposal;
use App\Models\Tender;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Global search — searches Tender, Proposal, Client.
     *
     * Security:
     *  - Results respect user permissions (view_tenders, view_proposals, view_clients)
     *  - NO financial values returned (no estimated_value, bid_value)
     *  - Returns JSON for AJAX dropdown or full view for /search page
     */
    public function index(Request $request): JsonResponse
    {
        $query = trim($request->get('q', ''));

        if (strlen($query) < 2) {
            return response()->json(['results' => [], 'query' => $query]);
        }

        $results = [];
        $user    = auth()->user();

        // ---- Tenders ----
        if ($user->can('view_tenders')) {
            Tender::with('client')
                ->where(fn($q) =>
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('code', 'like', "%{$query}%")
                )
                ->limit(5)
                ->get(['id', 'code', 'title', 'status', 'client_id'])
                ->each(function ($t) use (&$results) {
                    $results[] = [
                        'type'   => 'Tender',
                        'id'     => $t->id,
                        'code'   => $t->code,
                        'title'  => $t->title,
                        'client' => $t->client?->name,
                        'status' => $t->status->label(),
                        'badge'  => $t->status->badgeClass(),
                        'url'    => route('tenders.show', $t->id),
                    ];
                });
        }

        // ---- Proposals ----
        if ($user->can('view_proposals')) {
            Proposal::with('tender.client')
                ->where(fn($q) =>
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('code', 'like', "%{$query}%")
                )
                ->limit(5)
                ->get(['id', 'code', 'title', 'status', 'tender_id'])
                ->each(function ($p) use (&$results) {
                    $results[] = [
                        'type'   => 'Proposal',
                        'id'     => $p->id,
                        'code'   => $p->code,
                        'title'  => $p->title,
                        'client' => $p->tender?->client?->name,
                        'status' => $p->status->label(),
                        'badge'  => $p->status->badgeClass(),
                        'url'    => route('proposals.show', $p->id),
                    ];
                });
        }

        // ---- Clients ----
        if ($user->can('view_clients')) {
            Client::where(fn($q) =>
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('code', 'like', "%{$query}%")
                )
                ->limit(5)
                ->get(['id', 'code', 'name', 'status'])
                ->each(function ($c) use (&$results) {
                    $results[] = [
                        'type'   => 'Klien',
                        'id'     => $c->id,
                        'code'   => $c->code,
                        'title'  => $c->name,
                        'client' => null,
                        'status' => $c->status->label(),
                        'badge'  => $c->status->badgeClass(),
                        'url'    => route('clients.show', $c->id),
                    ];
                });
        }

        return response()->json([
            'query'   => $query,
            'results' => $results,
        ]);
    }
}

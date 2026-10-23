<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Búsqueda de recursos para documentar la Candelaria: resúmenes de Wikipedia y archivos de
 * Wikimedia Commons. Wikimedia rechaza (403) las peticiones sin un User-Agent que identifique la app.
 */
class ResourceSearchController extends Controller
{
    private const USER_AGENT = 'NanPuno/1.0 (contacto@nanpuno.pe)';

    /** Archivos de Wikimedia Commons sobre el tema */
    public function searchImages(Request $request)
    {
        $query = $request->validate(['query' => 'required|string|min:3'])['query'];

        return response()->json([
            'success' => true,
            'data' => ['wikimedia' => $this->searchWikimedia($query)],
        ]);
    }

    /** Resumen del artículo de Wikipedia en español */
    public function searchWikipedia(Request $request)
    {
        $query = $request->validate(['query' => 'required|string|min:3'])['query'];

        try {
            $response = $this->wikimedia()->get('https://es.wikipedia.org/api/rest_v1/page/summary/'.rawurlencode($query));
        } catch (ConnectionException) {
            return response()->json(['success' => false, 'message' => 'No se pudo consultar Wikipedia en este momento.'], 503);
        }

        if (! $response->successful()) {
            return response()->json(['success' => false, 'message' => 'No se encontró información en Wikipedia'], 404);
        }

        $data = $response->json();

        return response()->json([
            'success' => true,
            'data' => [
                'title' => $data['title'] ?? '',
                'extract' => $data['extract'] ?? '',
                'description' => $data['description'] ?? '',
                'thumbnail' => $data['thumbnail']['source'] ?? null,
                'url' => $data['content_urls']['desktop']['page'] ?? '',
            ],
        ]);
    }

    private function searchWikimedia(string $query): array
    {
        try {
            $response = $this->wikimedia()->get('https://commons.wikimedia.org/w/api.php', [
                'action' => 'query',
                'format' => 'json',
                'list' => 'search',
                'srsearch' => $query.' Puno Candelaria',
                'srnamespace' => 6, // Archivos
                'srlimit' => 10,
            ]);
        } catch (ConnectionException) {
            return [];
        }

        return collect($response->json('query.search', []))
            ->map(fn (array $item) => [
                'title' => $item['title'],
                'snippet' => strip_tags($item['snippet'] ?? ''),
                'source' => 'Wikimedia Commons',
                'page_url' => 'https://commons.wikimedia.org/wiki/'.rawurlencode(str_replace(' ', '_', $item['title'])),
            ])
            ->all();
    }

    private function wikimedia(): PendingRequest
    {
        return Http::withUserAgent(self::USER_AGENT)->acceptJson()->timeout(8);
    }
}

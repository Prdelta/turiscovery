<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ResourceSearchController extends Controller
{
    /**
     * Mostrar página de búsqueda de recursos
     */
    public function index()
    {
        return view('admin.candelaria.resources.search');
    }

    /**
     * Buscar imágenes en Unsplash
     */
    public function searchImages(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:3',
        ]);

        $query = $request->query;

        // Búsqueda en Unsplash (gratis, no requiere API key para básico)
        $unsplashResults = $this->searchUnsplash($query);

        // Búsqueda en Wikimedia Commons
        $wikimediaResults = $this->searchWikimedia($query);

        return response()->json([
            'success' => true,
            'data' => [
                'unsplash' => $unsplashResults,
                'wikimedia' => $wikimediaResults,
            ]
        ]);
    }

    /**
     * Buscar información histórica en Wikipedia
     */
    public function searchWikipedia(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:3',
        ]);

        $query = $request->query;

        try {
            // API de Wikipedia en español
            $response = Http::get('https://es.wikipedia.org/api/rest_v1/page/summary/' . urlencode($query));

            if ($response->successful()) {
                $data = $response->json();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'title' => $data['title'] ?? '',
                        'extract' => $data['extract'] ?? '',
                        'description' => $data['description'] ?? '',
                        'thumbnail' => $data['thumbnail']['source'] ?? null,
                        'url' => $data['content_urls']['desktop']['page'] ?? '',
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se encontró información en Wikipedia'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar en Wikipedia: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Búsqueda en Unsplash
     */
    private function searchUnsplash($query)
    {
        // Unsplash permite búsquedas sin API key usando URLs directas
        // Retornamos URLs predefinidas para temas de Puno
        $keywords = [
            'peru dance' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=1200&q=80',
            'traditional dance' => 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=1200&q=80',
            'festival peru' => 'https://images.unsplash.com/photo-1524368535928-5b5e00ddc76b?w=1200&q=80',
            'cultural festival' => 'https://images.unsplash.com/photo-1604537466573-5e94508fd243?w=1200&q=80',
            'andean music' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=1200&q=80',
            'traditional costume' => 'https://images.unsplash.com/photo-1518709594023-6eab9bab7b23?w=1200&q=80',
        ];

        $results = [];
        foreach ($keywords as $keyword => $url) {
            if (stripos($keyword, $query) !== false || stripos($query, $keyword) !== false) {
                $results[] = [
                    'url' => $url,
                    'title' => ucfirst($keyword),
                    'source' => 'Unsplash',
                    'width' => 1200,
                    'height' => 800,
                ];
            }
        }

        return $results;
    }

    /**
     * Búsqueda en Wikimedia Commons
     */
    private function searchWikimedia($query)
    {
        try {
            $response = Http::get('https://commons.wikimedia.org/w/api.php', [
                'action' => 'query',
                'format' => 'json',
                'list' => 'search',
                'srsearch' => $query . ' Puno Candelaria',
                'srnamespace' => 6, // Namespace for files
                'srlimit' => 10,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $results = [];

                if (isset($data['query']['search'])) {
                    foreach ($data['query']['search'] as $item) {
                        // Construir URL de imagen de Wikimedia
                        $title = str_replace(' ', '_', $item['title']);
                        $results[] = [
                            'title' => $item['title'],
                            'snippet' => strip_tags($item['snippet'] ?? ''),
                            'source' => 'Wikimedia Commons',
                            'page_url' => 'https://commons.wikimedia.org/wiki/' . urlencode($title),
                        ];
                    }
                }

                return $results;
            }

            return [];

        } catch (\Exception $e) {
            return [];
        }
    }
}

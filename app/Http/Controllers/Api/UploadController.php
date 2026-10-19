<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    /**
     * Carpetas permitidas en el disco público
     */
    protected array $folders = [
        'locales',
        'eventos',
        'experiencias',
        'promociones',
        'candelaria',
        'candelaria/gallery',
        'candelaria/danzas',
    ];

    /**
     * Subir una imagen y devolver su URL pública
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
            'folder' => 'required|string|in:' . implode(',', $this->folders),
        ]);

        $path = $request->file('image')->store($validated['folder'], 'public');

        return response()->json([
            'success' => true,
            'data' => [
                // URL según el host de la petición: funciona detrás del proxy de Vite
                // en desarrollo y con el dominio de la API en producción
                'url' => $request->getSchemeAndHttpHost() . '/storage/' . $path,
                'path' => $path,
            ],
        ], 201);
    }
}

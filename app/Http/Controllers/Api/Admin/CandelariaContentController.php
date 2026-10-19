<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CandelariaDanza;
use App\Models\CandelariaGallery;

/**
 * Listados completos (incluye inactivos) para el panel de administración
 */
class CandelariaContentController extends Controller
{
    public function gallery()
    {
        return response()->json([
            'success' => true,
            'data' => CandelariaGallery::orderByDesc('year')->orderBy('order')->get(),
        ]);
    }

    public function danzas()
    {
        return response()->json([
            'success' => true,
            'data' => CandelariaDanza::orderBy('order')->orderBy('name')->get(),
        ]);
    }
}

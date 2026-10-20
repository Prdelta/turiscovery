<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Avisos dentro de la app del usuario autenticado (reservas nuevas, confirmaciones, recordatorios).
 */
class NotificationController extends Controller
{
    /** Últimos avisos y cuántos faltan leer */
    public function index(Request $request)
    {
        $user = $request->user();
        $limit = min(max($request->integer('limit', 20), 1), 50);

        return response()->json([
            'success' => true,
            'data' => [
                'unread' => $user->unreadNotifications()->count(),
                'items' => $user->notifications()
                    ->latest()
                    ->limit($limit)
                    ->get()
                    ->map(fn ($n) => [
                        'id' => $n->id,
                        'data' => $n->data,
                        'read_at' => $n->read_at,
                        'created_at' => $n->created_at,
                    ]),
            ],
        ]);
    }

    public function markRead(Request $request, string $id)
    {
        // Solo los avisos propios: otro id da 404
        $notification = $request->user()->notifications()->whereKey($id)->firstOrFail();
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Cargar conteos
        $favoritesCount = $user->favorites()->count();
        $reviewsCount = $user->reviews()->count();

        // Cargar favoritos recientes (últimos 2) con relaciones
        $recentFavorites = $user->favorites()
            ->with('favoritable') // Carga polimórfica
            ->latest()
            ->take(2)
            ->get();

        // Cargar reseñas recientes (última reseña)
        $recentReviews = $user->reviews()
            ->with('reviewable') // Carga polimórfica
            ->latest()
            ->take(1)
            ->get();

        return view('user.index', compact(
            'favoritesCount',
            'reviewsCount',
            'recentFavorites',
            'recentReviews'
        ));
    }

    public function favorites()
    {
        $user = auth()->user();

        // Cargar favoritos con paginación y relaciones polimórficas
        $favorites = $user->favorites()
            ->with('favoritable')
            ->latest()
            ->paginate(12); // 12 items por página

        return view('user.favorites', compact('favorites'));
    }

    public function reviews()
    {
        $user = auth()->user();

        // Cargar reseñas con paginación y relaciones polimórficas
        $reviews = $user->reviews()
            ->with('reviewable')
            ->latest()
            ->paginate(10); // 10 items por página

        return view('user.reviews', compact('reviews'));
    }

    public function edit()
    {
        return view('user.edit');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'password' => 'nullable|string|min:8|confirmed',
            'preferences' => 'nullable|array',
        ]);

        $user->name = $validated['name'];
        $user->phone = $validated['phone'];
        $user->bio = $validated['bio'];
        $user->preferences = $request->input('preferences', []); // Handle array directly

        if ($request->filled('password')) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return redirect()->back()->with('success', 'Perfil actualizado correctamente');
    }
}

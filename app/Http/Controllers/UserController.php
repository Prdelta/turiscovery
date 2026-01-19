<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index');
    }

    public function favorites()
    {
        return view('user.favorites');
    }

    public function reviews()
    {
        return view('user.reviews');
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

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

    public function update()
    {
        // Placeholder for update logic
        return redirect()->back()->with('success', 'Perfil actualizado correctamente');
    }
}

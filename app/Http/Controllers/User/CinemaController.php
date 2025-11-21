<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cinema;
use Illuminate\Http\Request;

class CinemaController extends Controller
{
    public function index()
    {
        $cinemas = Cinema::withCount('theaters')
            ->orderBy('city')
            ->orderBy('name')
            ->get()
            ->groupBy('city');

        return view('user.cinemas.index', compact('cinemas'));
    }

    public function show(Cinema $cinema)
    {
        $cinema->load(['theaters']);

        return view('user.cinemas.show', compact('cinema'));
    }
}



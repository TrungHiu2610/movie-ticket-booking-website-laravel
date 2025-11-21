<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use App\Models\Movie;
use App\Models\Theater;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredMovies = Movie::where('status', 'now_showing')
            ->with('genres')
            ->latest()
            ->take(3)
            ->get();

        $nowShowingMovies = Movie::where('status', 'now_showing')
            ->with('genres')
            ->latest()
            ->take(8)
            ->get();

        $comingSoonMovies = Movie::where('status', 'coming_soon')
            ->with('genres')
            ->orderBy('release_date')
            ->take(8)
            ->get();

        $totalMovies = Movie::count();
        $totalCinemas = Cinema::count();
        $totalTheaters = Theater::count();
        $totalUsers = User::where('role_id', 2)->count();

        return view('home', compact(
            'featuredMovies',
            'nowShowingMovies',
            'comingSoonMovies',
            'totalMovies',
            'totalCinemas',
            'totalTheaters',
            'totalUsers'
        ));
    }
}

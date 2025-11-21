<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StaffMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && (Auth::user()->role->name === 'staff' || Auth::user()->role->name === 'admin')) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized - Staff access only'], 403);
    }
}

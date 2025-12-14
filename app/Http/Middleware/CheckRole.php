<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Kalau belum login, lempar ke halaman login Filament
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return redirect('/'); // Bisa ganti redirect('/') kalau mau
        }


        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureNotAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            return redirect()->route('welcome')->with('error', 'Silakan logout dari akun Admin terlebih dahulu untuk mengakses chat sebagai Pemegang SIM.');
        }

        return $next($request);
    }
}   
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventDemoMutation
{
    /**
     * Handle an incoming request.
     * Mencegah mutasi data (POST, PUT, PATCH, DELETE) jika user masuk dalam mode demo pengamat.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session('is_demo') && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            // Pengecualian rute yang tetap boleh diakses (keluar / ganti peran demo)
            if ($request->routeIs('logout') || $request->routeIs('login.demo')) {
                return $next($request);
            }

            $message = 'Mode Demo (Pengamat): Perubahan data dinonaktifkan untuk menjaga kestabilan data contoh.';

            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'error' => $message,
                ], 403);
            }

            return back()->with('error', $message);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLocationSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('table_number')) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan scan QR Code meja Anda terlebih dahulu.'
                ], 403);
            }

            return redirect()->route('home')->with('error', 'Silakan scan QR Code meja Anda terlebih dahulu.');
        }

        return $next($request);
    }
}

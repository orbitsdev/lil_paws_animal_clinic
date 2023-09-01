<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ClinicMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user()->load('veterinarian.clinic');

        if ($user->hasAnyRole(['Admin'])) {
            return $next($request);
        }
        
        if ($user->hasAnyRole(['Vet']) && (!$user->veterinarian || !$user->veterinarian->clinic)) {
            abort(403, 'Unauthorized action.');
        }
        
        return $next($request);

    }
}

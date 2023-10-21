<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ClinicRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {   
        abort(403, 'Unauthorized action.');
        $user = Auth::user();

        // Check if the user has the 'Veterenarian' role and does not have a clinic
        if ($user->hasAnyRole(['Veterenarian']) && (!$user->clinic)) {
            abort(403, 'Unauthorized action.');
        }
    
        // Check if the user has a clinic and the clinic status is accepted
        if ($user->clinic && $user->clinic->status === 'accepted') {
            return redirect('clinic');
        }
    
        return $next($request);

    }
}

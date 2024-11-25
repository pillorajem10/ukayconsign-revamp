<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckIfUserIs43
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the authenticated user is ID 43
        if (auth()->check() && auth()->id() == 43) {
            // Redirect to PosSaleController route
            return redirect()->route('posSale.index');
        }

        // Otherwise, continue with the original request
        return $next($request);
    }
}


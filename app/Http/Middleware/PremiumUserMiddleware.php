<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PremiumUserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Share premium status with all views
        view()->share('isPremium', auth()->check() && auth()->user()->is_premium);

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;

class TestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $testing = env('TESTING');
        if ($testing == 'yes') {
            return $next($request);
        }

        return redirect()->route('index');
    }
}

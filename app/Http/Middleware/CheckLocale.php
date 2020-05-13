<?php

namespace App\Http\Middleware;

use App;
use Closure;

class CheckLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        App::setLocale(\Route::current()->parameter('locale'));
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Auth,Closure;

class Admin
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
        if(!Auth::id() || Auth::id() != 1)
           return redirect('/');

        return $next($request);
    }
}

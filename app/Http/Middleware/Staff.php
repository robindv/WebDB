<?php

namespace App\Http\Middleware;

use Auth,Closure;

class Staff
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
        if(!Auth::id() || Auth::user()->is_student())
           return redirect('/');

        return $next($request);
    }
}

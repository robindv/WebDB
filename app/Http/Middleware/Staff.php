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
        if(!Auth::id())
            return redirect('/');

        if(!Auth::user()->is_assistant && !Auth::user()->is_teacher)
           return redirect('/');

        return $next($request);
    }
}

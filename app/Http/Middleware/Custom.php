<?php

namespace App\Http\Middleware;
use Closure;
class Custom
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
	   // throw new \Exception("всё плохо");
        return $next($request);
    }
}
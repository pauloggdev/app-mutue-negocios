<?php

namespace App\Http\Middleware;

use Closure;

class HasPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $ability)
    {
        if (auth()->user()->hasPermission($ability)) {
            return $next($request);
        }
        return redirect()->route('semPermissao.index');
    }
}

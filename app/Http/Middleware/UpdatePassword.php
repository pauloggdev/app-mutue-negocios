<?php

namespace App\Http\Middleware;

use Closure;

class UpdatePassword
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
        if (auth()->check() && auth()->user()->status_senha_id == 2) {
            return $next($request);
        }
        
        return redirect('empresa/home');
    }
}

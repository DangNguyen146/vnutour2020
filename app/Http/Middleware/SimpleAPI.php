<?php

namespace App\Http\Middleware;

use Closure;

class SimpleAPI
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
        if ($request->token == "hoighoihjk*^@(YUJABFJAGWHGUHV!^%$*!@&^$!@^$(!($^($^(@&HGHJAFJA")
            return $next($request);
        
        return abort('403'); 
    }
}

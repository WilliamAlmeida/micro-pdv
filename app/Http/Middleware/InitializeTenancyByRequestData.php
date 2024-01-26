<?php

namespace App\Http\Middleware;

use Closure;
use Stancl\Tenancy\Middleware\InitializeTenancyByRequestData as BaseMiddleware;

class InitializeTenancyByRequestData extends BaseMiddleware
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
        if ($request->method() !== 'OPTIONS') {
            $tenant = $this->getPayload($request);
            if($tenant) return $this->initializeTenancy($request, $next, $tenant);
        }

        return $next($request);
    }
}

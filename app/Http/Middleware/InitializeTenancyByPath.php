<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Empresas;
use Illuminate\Http\Request;
use Stancl\Tenancy\Resolvers\PathTenantResolver;
use Stancl\Tenancy\Exceptions\RouteIsMissingTenantParameterException;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedByPathException;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath as BaseMiddleware;

class InitializeTenancyByPath extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var Route $route */
        $route = $request->route();

        // Only initialize tenancy if tenant is the first parameter
        // We don't want to initialize tenancy if the tenant is
        // simply injected into some route controller action.
        if ($route->parameterNames()[0] === PathTenantResolver::$tenantParameterName) {
            $id = $route->parameter(PathTenantResolver::$tenantParameterName);
            $tenant = Empresas::find($id);
            $route->forgetParameter(PathTenantResolver::$tenantParameterName);

            if($tenant) {
                tenancy()->initialize($tenant);
            }else{
                throw new TenantCouldNotBeIdentifiedByPathException($id);
            }
        } else {
            throw new RouteIsMissingTenantParameterException;
        }

        return $next($request);
    }
}

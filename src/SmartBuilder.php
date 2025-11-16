<?php

namespace Tetthys\Smart;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class SmartBuilder
{
    public static function build(mixed $smartServiceClass)
    {
        /**
         * There are three typs of route dependencies:
         * 
         * 1. ['user' => 'user']
         *    - This means that the constructor parameter 'user' should be resolved from the route parameter 'user'.
         * 
         * 2. ['post' => ['postId', fn($postId) => Post::find($postId)]]
         *   - This means that the constructor parameter 'post' should be resolved from the route
         * 
         * 3. ['customer' => 'auth']
         *   - This means that the constructor parameter 'customer' should be resolved from the authenticated user.
         */

        // boot the smart service class for some classes that need callbacks to be registered
        $smartServiceClass::boot();

        // first get the route dependencies
        $routeDependencies = $smartServiceClass::getRouteDependencies();

        // then build a new array with resolved values
        $resolvedDependencies = [];

        foreach ($routeDependencies as $constructorParamName => $routeParamName) {
            // special case for 'auth' to get the authenticated user
            if ($routeParamName === 'auth') {
                $resolvedDependencies[$constructorParamName] = Auth::user();
            } elseif (is_array($routeParamName)) {
                [$routeParamName, $resolverClosure] = $routeParamName;
                $resolvedDependencies[$constructorParamName] = $resolverClosure(Route::parameter($routeParamName));
            } else {
                $resolvedDependencies[$constructorParamName] = Route::parameter($routeParamName);
            }
        }
        return App::makeWith($smartServiceClass, $resolvedDependencies);
    }
}

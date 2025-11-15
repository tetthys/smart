<?php

namespace Tetthys\Smart;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\App;

class SmartBuilder
{
    public static function build(mixed $smartServiceClass)
    {
        // key is parameter name which is correspoing to constructor parameter, value is route parameter
        $routeDependencies = $smartServiceClass::getRouteDependencies();

        // then build a new array with resolved values
        $resolvedDependencies = [];
        foreach ($routeDependencies as $constructorParamName => $routeParamName) {
            $resolvedDependencies[$constructorParamName] = Route::parameter($routeParamName);
        }

        return App::makeWith($smartServiceClass, $resolvedDependencies);
    }
}

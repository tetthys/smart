<?php

namespace Tetthys\Smart;

abstract class SmartService
{
    protected static array $routeDependencies = [];

    public static function getRouteDependencies(): array
    {
        return static::$routeDependencies;
    }

    public static function setRouteDependencies(array $dependencies): void
    {
        static::$routeDependencies = $dependencies;
    }
}

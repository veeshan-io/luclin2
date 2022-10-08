<?php

namespace Luclin2\Foundation;

trait NamedInstancableTrait
{
    protected static string $_defaultInstanceName = '_';
    protected static array $_instances = [];

    public static function instance(string $name = null, array $arguments = []) {
        !$name && $name = static::$_defaultInstanceName;
        if (!isset(static::$_instances[$name])) {
            static::$_instances[$name] = new static(...$arguments);
        }

        return static::$_instances[$name];
    }
}
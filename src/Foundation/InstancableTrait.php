<?php

namespace Luclin2\Foundation;

trait InstancableTrait
{
    protected static array $instances = [];

    public static function instance($name = '_', ...$arguments) {
        return static::$instances[$name] ??
            (static::$instances[$name] = new static(...$arguments));
    }

    public static function instances(): iterable {
        return static::$instances;
    }
}
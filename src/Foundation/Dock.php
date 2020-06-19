<?php

namespace Luclin2\Foundation;

class Dock
{
    private static array $data = [];

    public static function __callStatic(string $name, array $arguments)
    {
        $key = array_shift($arguments);
        if (!$arguments) {
            return static::$data[$name][$key] ?? null;
        }

        if ($arguments[0] === null) {
            unset(static::$data[$name][$key]);
            return null;
        }

        return static::$data[$name][$key] = $arguments[0];
    }
}
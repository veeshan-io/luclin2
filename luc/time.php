<?php

namespace luc;

use Closure;

class time
{
    public static string $now = 'now';
    public static ?Closure $creator = null;

    public static function shift(string $time = 'now'): void
    {
        static::$now = $time;
    }

    public static function now($refresh = false)
    {
        static $now = null;
        (!$now || $refresh) && ($now = static::create(static::$now));
        return $now;
    }

    public static function create($time)
    {
        if (static::$creator) {
            $creator = static::$creator;
            return $creator($time);
        }
        return new \DateTimeImmutable($time);
    }
}
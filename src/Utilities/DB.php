<?php

namespace Luclin2\Utilities;

use Luclin2\Foundation;

class DB
{
    public static function toProperty(string $field): string {
        return lcfirst(str_replace('_', '', ucwords($field, '_')));
    }

    public static function toField(string $property): string {
        static $regex = '/(?<!^)((?<![[:upper:]])[[:upper:]]|[[:upper:]](?![[:upper:]]))/';
        return strtolower(preg_replace($regex, '_$1', $property));
    }
}

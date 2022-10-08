<?php

namespace Luclin2\Utilities;

use Luclin2\Foundation;

class Pack
{
    public static function encode(array $data): string {
        return \msgpack_serialize($data);
    }

    public static function decode(string $binary): array {
        return \msgpack_unserialize($binary);
    }
}

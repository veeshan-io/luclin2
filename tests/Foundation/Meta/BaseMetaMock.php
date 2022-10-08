<?php

use Luclin2\Foundation\Meta;

class BaseMetaMock extends Meta
{
    protected static function _defaults(): array
    {
        return [
            'id'  => null,
            'createdAt'  => null,
            'updatedAt'  => null,
            'deletedAt'  => null,
        ];
    }
}
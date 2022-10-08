<?php

namespace Luclin2\Foundation;

trait InstancableTrait
{
    public static function instance(...$arguments) {
        return new static(...$arguments);
    }
}

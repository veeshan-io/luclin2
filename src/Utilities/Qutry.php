<?php

namespace Luclin2\Utilities;

use Luclin2\Foundation;

class Qutry
{
    use Foundation\InstancableTrait;

    private array $storage = [];

    public function __set(string $name, $value)
    {

    }

    public function __get(string $name)
    {

    }

    public function __call(string $name, array $arguments)
    {
        $key = array_shift($arguments);
        if (!$arguments) {
            return $this->storage[$name][$key] ?? null;
        }

        if ($arguments[0] === null) {
            unset($this->storage[$name][$key]);
            return null;
        }

        return $this->storage[$name][$key] = $arguments[0];
    }
}
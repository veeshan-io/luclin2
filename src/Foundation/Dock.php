<?php

namespace Luclin2\Foundation;

class Dock
{
    use InstancableTrait;

    private array $storage = [];

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
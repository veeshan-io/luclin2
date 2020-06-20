<?php

namespace Luclin2\Foundation;

class CaseClass
{
    public string $symbol;

    private $value = null;
    private $func;

    public function __construct(string $symbol, $value = null,
        ?callable $func = null)
    {
        $this->symbol   = $symbol;
        $value  && $this->value = $value;
        $func   && $this->func  = $func;
    }

    public function is(string $symbol): bool {
        return $this->symbol == $symbol;
    }

    public function __invoke(...$params) {
        if ($this->func) {
            $func = $this->func;
            return $func($this->value, ...$params);
        }
        return $this->value;
    }

    public function __call(string $method, array $arguments)
    {
        $implicit = new Implicit($this->symbol);
        return $implicit($method, $this, $arguments);
    }

    public function __toString()
    {
        $name = ":$this->symbol";
        if ($this->value === null) {
            return $name;
        }
        $value = $this();
        return $name."($value)";
    }

}
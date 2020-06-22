<?php

namespace Luclin2\Foundation;

class CaseClass
{
    public string $type;

    private $value  = null;
    private $func   = null;

    public function __construct($type, $value = null,
        ?callable $func = null)
    {
        if ($type instanceof self) {
            $this->type     = $type->type;
            $this->value    = $value === null ? $type() : $value;
            $this->func     = $type->func;
        } else {
            $this->type   = $type;
            $value !== null && $this->value = $value;
            $func  && $this->func  = $func;
        }
    }

    public static function by(string $str): self {
        $start  = 1;
        $end    = strpos($str, '(');
        if ($end) {
            $type   = substr($str, $start, $end - $start);
            $value  = substr($str, $end + 1, -1) ?: null;

            if ($value) {
                $funcs = Implicit::method($type, '_restore');
                $value = take($funcs, $value);
            }
        } else {
            $type   = substr($str, $start);
            $value  = null;
        }

        return new self($type, $value);
    }

    public function fun(): ?callable {
        return $this->func;
    }

    public function __invoke(...$params) {
        $value = $this->value;
        $value instanceof self && $value = $value();
        if ($this->func) {
            $func   = $this->func;
            return $func($value, ...$params);
        }
        return $value;
    }

    public function __call(string $method, array $arguments)
    {
        return Implicit::call($method, $this, $arguments);
    }

    public function __get(string $method): ?callable {
        $funcs = Implicit::method($this->type, $method);
        if (!$funcs) return null;
        return fn(...$params) => take($funcs, $this, $params);
    }

    public function __toString()
    {
        $name = ":$this->type";
        if ($this->value === null) {
            return $name;
        }
        $value = $this();
        return $name."($value)";
    }

}
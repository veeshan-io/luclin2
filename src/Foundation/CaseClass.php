<?php

namespace Luclin2\Foundation;

class CaseClass
{
    public string $type;

    private $value = null;
    private $func;

    public function __construct($type, $value = null,
        ?callable $func = null)
    {
        if ($type instanceof self) {
            $this->type     = $type->type;
            $this->value    = $type();
            $this->func     = $type->func;
        } else {
            $this->type   = $type;
            $value  && $this->value = $value;
            $func   && $this->func  = $func;
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

    public function is(string $type): bool {
        return $this->type == $type;
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
        $implicit = new Implicit($this->type);
        return $implicit($method, $this, $arguments);
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
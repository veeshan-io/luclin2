<?php

namespace Luclin2\Fun;

class Currying
{
    public $func;
    public array $params;

    public function __construct(callable $func, array $params)
    {
        $this->func     = $func;
        $this->params   = $params;
    }

    public function into(...$params): self {
        array_push($this->params, ...$params);
        return $this;
    }

    public function __invoke() {
        $func = $this->func;
        return $func(...$this->params);
    }
}
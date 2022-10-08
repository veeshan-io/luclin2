<?php

namespace Luclin2\Fun;

class Functor
{
    private $func;
    private $handler;

    public function __construct(callable $func, callable $handler)
    {
        $this->func     = $func;
        $this->handler  = $handler;
    }

    public function __invoke($value, ...$params) {
        $func    = $this->func;
        $handler = $this->handler;
        foreach ($handler($value) as $key => $case) {
            yield $key => $func($case, ...$params);
        }
    }
}

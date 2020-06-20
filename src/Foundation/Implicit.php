<?php

namespace Luclin2\Foundation;

class Implicit
{
    private string $symbol;

    public function __construct(string $symbol)
    {
        $this->symbol = $symbol;
    }

    public function __call(string $method, array $funcs): self
    {
        Dock::instance('implicit')->{$this->symbol}($method, $funcs);
        return $this;
    }

    public function __invoke(string $method, $case, array $params) {
        $dock   = Dock::instance('implicit');
        $funcs  = $dock->{$this->symbol}($method);
        if ($funcs) return take($funcs, $case, $params);
        elseif ($funcs = $dock->{$this->symbol}('_')) return take($funcs, $case, $params);

        throw new \OutOfBoundsException('bound case method is not exist');
    }
}
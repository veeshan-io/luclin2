<?php

namespace Luclin2\Foundation;

class Implicit
{
    private string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function __call(string $method, array $funcs): self
    {
        // functor 优化，目前版本仅对第一个 $func 做特殊处理
        if ($funcs[0] instanceof Functor) {
            $functor  = $funcs[0];
            $funcs[0] = fn(...$params) => $functor(...$params);
        }
        Dock::instance('implicit')->{$this->type}($method, $funcs);
        return $this;
    }

    public function __invoke(string $method, $case, array $params) {
        $dock   = Dock::instance('implicit');
        $funcs  = $dock->{$this->type}($method);
        if ($funcs) return take($funcs, $case, $params);
        elseif ($funcs = $dock->{$this->type}('_')) return take($funcs, $case, $params);

        throw new \OutOfBoundsException('bound case method is not exist');
    }

    public static function method(string $type, string $method): ?array {
        return Dock::instance('implicit')->$type($method);
    }
}
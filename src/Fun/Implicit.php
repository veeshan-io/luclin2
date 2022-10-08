<?php

namespace Luclin2\Fun;

use Luclin2\Utilities\Dock;

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

    public static function call(string $method, $case, array $params) {
        $dock   = Dock::instance('implicit');
        $funcs  = $dock->{$case->type}($method);
        if ($funcs) return take($funcs, $case, $params);
        elseif ($funcs = $dock->{$case->type}('_')) return take($funcs, $case, [$method, ...$params]);

        throw new \OutOfBoundsException("Bound case method :{$case->type}->$method() is not exist");
    }

    public static function method(string $type, string $method): ?array {
        return Dock::instance('implicit')->$type($method);
    }
}
<?php

namespace Luclin2\Foundation;

/**
 * 当前不支持静态方法 macro
 * 静态方法注入建议使用直接调用原则
 */
trait MacroableTrait
{
    protected static array $macros = [];
    protected static array $mixins = [];

    public static function macro(string $name, callable $macro): void
    {
        static::$macros[$name] = $macro;
    }

    public static function mixin(object $mixin): void
    {
        static::$mixins[] = $mixin;
    }

    public static function hasMacro(string $name): bool
    {
        return isset(static::$macros[$name]);
    }

    public function __call(string $method, array $params)
    {
        if (static::hasMacro($method)) {
            $result = $this->macros[$method] instanceof \Closure ?
                $this->macros[$method]->call($this, ...$params) :
                $this->macros[$method](...$params);
        } else {
            $selectedMixin = null;
            foreach (static::$mixins as $mixin) {
                if (method_exists($mixin, $method)) {
                    $selectedMixin = $mixin;
                    break;
                }
            }
            if (!$selectedMixin) {
                throw new \BadMethodCallException("Method ".static::class."::$method does not exist.");
            }
            $closure = $selectedMixin->$method(...);
            $result = $closure->call($this, ...$params);
        }
        return $result;
    }
}

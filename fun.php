<?php

use Luclin2\Foundation;

/**
 * Create CaseClass object
 *
 * @param string | Foundation\CaseClass $type
 * @param mixed $value
 * @param callable $func
 * @return Foundation\CaseClass
 */
function casing($type, $value = null,
    callable $func = null): Foundation\CaseClass
{
    if (is_string($type) && $type[0] == ':') return Foundation\CaseClass::by($type);
    return new Foundation\CaseClass($type, $value, $func);
}

/**
 * 获取raw case的case type
 *
 * @param mixed $var
 * @return string|null
 */
function casetype($var): ?string
{
    if (is_string($var))    return "string";
    if (is_numeric($var))   return "numeric";
    if (is_bool($var))      return "boolean";
    if (is_null($var))      return "null";
    if (is_iterable($var))  return "iterable";
    if (is_resource($var))  return "resource";
    if (is_object($var))    return "instance";
    return null;
}

/**
 * Create CaseClass object by raw type
 *
 * @param mixed $value
 * @return Foundation\CaseClass
 */
function raw($value): Foundation\CaseClass {
    return new Foundation\CaseClass(casetype($value), $value);
}

/**
 * Create Match object
 *
 * @param iterable $context
 * @return Foundation\Match
 */
function match(iterable $context = []): Foundation\Match {
    return new Foundation\Match($context);
}

/**
 * 对一个目标连续执行多个团外
 *
 * @param iterable $funcs
 * @param mixed $value
 * @param array $params
 * @return void
 */
function take(?iterable $funcs, $value, array $params = []) {
    if (!$funcs) return $value;

    foreach ($funcs as $func) {
        $value = $func($value, ...$params);
    }
    return $value;
}

/**
 * 隐式注入Case method
 *
 * @param string $type
 * @return Foundation\Implicit
 */
function implicit(string $type): Foundation\Implicit {
    return new Foundation\Implicit($type);
}

/**
 * 构造一个迭代器将一个iterable中的每个单元作为指定case处理
 *
 * @param string $type
 * @param iterable $items
 * @param callable $func
 * @return iterable
 */
function thought(string $type, callable $func = null): callable {
    return (function($items) use ($type, $func) {
        $result = [];
        $items instanceof Foundation\CaseClass && $items = $items();
        foreach ($items as $key => $item) {
            $result = yield $key => casing($type, $item, $func);
        }
        return $result;
    });
}

/**
 * 构造Functor
 *
 * @param callable $func 处理函数
 * @param callable $handler 解析方法
 * @return Foundation\Functor
 */
function functor(callable $func, callable $handler = null): Foundation\Functor {
    return new Foundation\Functor($func, $handler);
}

/**
 * 收取迭代器的结果
 *
 * @param iterable $iterator
 * @return array
 */
function result(iterable $iterator, bool $autoUnpack = true): array {
    $result = [];
    foreach ($iterator as $key => $item) {
        $result[$key] = $autoUnpack ?
            ($item instanceof Foundation\CaseClass ? $item() : $item) : $item;
    }
    return $result;
}

/**
 * Currying function
 *
 * @param callable $func
 * @param mixed[] ...$params
 * @return Foundation\Currying
 */
function into(callable $func, ...$params): Foundation\Currying {
    return new Foundation\Currying($func, $params);
}

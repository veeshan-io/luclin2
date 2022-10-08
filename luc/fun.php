<?php

namespace luc;

use Luclin2\Fun;

/**
 * Create CaseClass object
 *
 * @param string | Fun\CaseClass $type
 * @param mixed $value
 * @param callable $func
 * @return Fun\CaseClass
 */
function casing($type, $value = null,
    callable $func = null): Fun\CaseClass
{
    if (is_string($type) && $type[0] == ':') return Fun\CaseClass::by($type);
    return new Fun\CaseClass($type, $value, $func);
}

/**
 * 获取raw case的case type
 *
 * @param mixed $var
 * @return string|null|boolean
 */
function casetype($var, $other = null)
{
    if ($other !== null) {
        return casetype($var) === casetype($other);
    }

    if ($var instanceof Fun\CaseClass)
        return $var->type;
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
 * @return Fun\CaseClass
 */
function raw($value): Fun\CaseClass {
    $type = casetype($value);

    // dd($value);
    if ($value instanceof Fun\CaseClass) {
        $func  = $value->fun();
        $value = $value();
    } else {
        $func  = null;
    }
    return new Fun\CaseClass($type, $value, $func);
}

/**
 * Create Match object
 * @see php8已经提供了 match 关键字了，可能需要作废。
 *
 * @param iterable $context
 * @return Fun\Match
 */
// function match(iterable $context = []): Fun\Match {
//     return new Fun\Match($context);
// }

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
 * @return Fun\Implicit
 */
function implicit(string $type): Fun\Implicit {
    return new Fun\Implicit($type);
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
        $items instanceof Fun\CaseClass && $items = $items();
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
 * @return Fun\Functor
 */
function functor(callable $func, callable $handler = null): Fun\Functor {
    return new Fun\Functor($func, $handler);
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
            ($item instanceof Fun\CaseClass ? $item() : $item) : $item;
    }
    return $result;
}

/**
 * Currying function
 *
 * @param callable $func
 * @param mixed[] ...$params
 * @return Fun\Currying
 */
function into(callable $func, ...$params): Fun\Currying {
    return new Fun\Currying($func, $params);
}

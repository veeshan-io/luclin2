<?php

use Luclin2\Foundation;

/**
 * Create CaseClass object
 *
 * @param string $symbol
 * @param mixed $value
 * @param callable $func
 * @return Foundation\CaseClass
 */
function casing(string $symbol, $value = null,
    callable $func = null): Foundation\CaseClass
{
    return new Foundation\CaseClass($symbol, $value, $func);
}

/**
 * Create CaseClass object by raw type
 *
 * @param mixed $value
 * @return Foundation\CaseClass
 */
function raw($value): Foundation\CaseClass {
    return new Foundation\CaseClass(\luc\type($value), $value);
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
function take(iterable $funcs, $value, array $params) {
    foreach ($funcs as $func) {
        $value = $func($value, ...$params);
    }
    return $value;
}

/**
 * 隐式注入Case method
 *
 * @param string $symbol
 * @return Foundation\Implicit
 */
function implicit(string $symbol): Foundation\Implicit {
    return new Foundation\Implicit($symbol);
}

/**
 * 构造一个迭代器将一个iterable中的每个单元作为指定case处理
 *
 * @param string $symbol
 * @param iterable $items
 * @param callable $func
 * @return iterable
 */
function thought(string $symbol, callable $func = null): callable {
    return (function($items) use ($symbol, $func) {
        $result = [];
        $items instanceof Foundation\CaseClass && $items = $items();
        foreach ($items as $key => $item) {
            $result = yield $key => casing($symbol, $item, $func);
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
function looking(callable $func, ...$params): Foundation\Currying {
    return new Foundation\Currying();
}

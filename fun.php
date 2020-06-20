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

function implicit(string $symbol): Foundation\Implicit {
    return new Foundation\Implicit($symbol);
}

// 把一个迭代器构造为case wrap迭代器
function thought(string $symbol, iterable $items) {

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


function functor() {

}
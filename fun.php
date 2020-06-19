<?php

use Luclin2\Foundation;

/**
 * Create Case Class
 *
 * @param string $symbol
 * @param [type] $value
 * @param callable $func
 * @return Foundation\CaseClass
 */
function casing(string $symbol, $value = null,
    callable $func = null): Foundation\CaseClass
{
    return new Foundation\CaseClass($symbol, $value, $func);
}

function match(callable $func = null): callable {
    return fn() => 1;
}

function take(iterable $funcs, $value, ...$params) {
    foreach ($funcs as $func) {
        $value = $func($value, ...$params);
    }
    return $value;
}

function implicit() {

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
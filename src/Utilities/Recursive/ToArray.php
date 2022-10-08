<?php

namespace Luclin2\Utilities\Recursive;

class ToArray
{
    protected $filter;


    public function __construct(
        protected iterable $root,
        protected bool $nullable = false,
        callable $filter = null,)
    {
        $this->filter = $filter;
    }

    public function __invoke(): array {
        return $this->_toArray($this->root);
    }

    public function _toArray(iterable $traversable): array {
        $result = [];
        foreach ($traversable as $key => $value) {
            if (is_object($value) && \method_exists($value, 'toArray')) {
                $value = $value->toArray();
            } elseif (is_iterable($value)) {
                $value = $this->_toArray($value);
            }

            // 过滤器
            $this->filter && ($filter = $this->filter) && $value = $filter($value);

            ($this->nullable || $value !== null) && $result[$key] = $value;
        }
        return $result;
    }
}
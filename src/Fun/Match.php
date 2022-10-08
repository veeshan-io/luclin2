<?php

namespace Luclin2\Fun;

class Match
{
    private iterable $context;
    private array $cases = [];

    public function __construct(iterable $context = [])
    {
        $this->context  = $context;
    }

    public function __call(string $type, array $funcs): self
    {
        $this->cases[$type] = $funcs;
        return $this;
    }

    public function __invoke($case, ...$params) {
        $params[] = $this->context;

        $type = casetype($case) ?: 'unknown';
        if ($funcs = $this->cases[$type] ?? null) return take($funcs, $case, $params);
        elseif ($funcs = $this->cases['_'] ?? null) return take($funcs, $case, $params);

        throw new \UnexpectedValueException("Case $type match failed");
    }
}
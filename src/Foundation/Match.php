<?php

namespace Luclin2\Foundation;

class Match
{
    private iterable $context;
    private array $cases = [];
    private array $close = [];

    public function __construct(iterable $context = [])
    {
        $this->context  = $context;
    }

    public function __call(string $type, array $funcs): self
    {
        if ($type != '_') {
            $this->cases[$type] = $funcs;
        } else {
            $this->close = $funcs;
        }
        return $this;
    }

    public function __invoke($case, ...$params) {
        $params[] = $this->context;

        $run = function(callable $test) use ($case, $params) {
            foreach ($this->cases as $type => $funcs) {
                if ($test($type)) return take($funcs, $case, $params);
            }

            if ($this->close) return take($this->close, $case, $params);

            throw new \UnexpectedValueException("case match failed");
        };

        if ($case instanceof CaseClass) {
            $test = fn($type) => $case->is($type);
        } else {
            $type = casetype($case) ?: 'nothing';
            $test = fn($type) => $type == $type;
        }
        return $run($test);
    }
}
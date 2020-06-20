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

    public function __call(string $symbol, array $funcs): self
    {
        if ($symbol != '_') {
            $this->cases[$symbol] = $funcs;
        } else {
            $this->close = $funcs;
        }
        return $this;
    }

    public function __invoke($case, ...$params) {
        $params[] = $this->context;

        $run = function(callable $test) use ($case, $params) {
            foreach ($this->cases as $symbol => $funcs) {
                if ($test($symbol)) return take($funcs, $case, $params);
            }

            if ($this->close) return take($this->close, $case, $params);

            throw new \UnexpectedValueException("case match failed");
        };

        if ($case instanceof CaseClass) {
            $test = fn($symbol) => $case->is($symbol);
        } else {
            $type = \luc\type($case);
            $test = fn($symbol) => $type == $symbol;
        }
        return $run($test);
    }
}
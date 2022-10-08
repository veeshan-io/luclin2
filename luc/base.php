<?php

namespace luc;

function debug(?bool $value = null): bool {
    static $status = false;
    if ($value !== null) {
        $status = $value;
    }
    return $status;
}

function padding(string $template, array $vars): ?string {
    return preg_replace_callback(
        '/(\{\{)([A-Za-z0-9_\@\$\.\-\~\#\&]+)(\}\})/',
        function($matches) use ($vars) {
            return $vars[$matches[2]] ?? $matches[0];
        }, $template);
}

function du(...$arguments): void {
    var_dump(...$arguments);
}

function fetch(object $object, string $field): mixed {
    $fetcher = function() use ($field) {
        return $this->$field;
    };
    return $fetcher->call($object);
}

function mapping(array $source, array $index): array {
    foreach ($index as $from => $to) {
        if (!isset($soucre[$from])) {
            continue;
        }
        $source[$to] = $source[$from];
        unset($source[$from]);
    }
    return $source;
}

// function du() {
//         (new static(func_get_args()))
//             ->push($this)
//             ->each(function ($item) {
//                 VarDumper::dump($item);
//             });

//         return $this;

//         if (null === self::$handler) {
//             $cloner = new VarCloner();
//             $cloner->addCasters(ReflectionCaster::UNSET_CLOSURE_FILE_INFO);

//             if (isset($_SERVER['VAR_DUMPER_FORMAT'])) {
//                 $dumper = 'html' === $_SERVER['VAR_DUMPER_FORMAT'] ? new HtmlDumper() : new CliDumper();
//             } else {
//                 $dumper = \in_array(\PHP_SAPI, ['cli', 'phpdbg']) ? new CliDumper() : new HtmlDumper();
//             }

//             $dumper = new ContextualizedDumper($dumper, [new SourceContextProvider()]);

//             self::$handler = function ($var) use ($cloner, $dumper) {
//                 $dumper->dump($cloner->cloneVar($var));
//             };
//         }

//         return (self::$handler)($var);
// }
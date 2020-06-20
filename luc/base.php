<?php

namespace luc;

function padding(string $template, array $vars): ?string {
    return preg_replace_callback(
        '/(\{\{)([A-Za-z0-9_\@\$\.\-\~\#\&]+)(\}\})/',
        function($matches) use ($vars) {
            return $vars[$matches[2]] ?? $matches[0];
        }, $template);
}

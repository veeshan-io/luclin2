<?php

namespace luc;

function type($var): ?string
{
    if (is_string($var))    return "string";
    if (is_numeric($var))   return "numeric";
    if (is_bool($var))      return "boolean";
    if (is_null($var))      return "null";
    if (is_iterable($var))  return "iterable";
    if (is_resource($var))  return "resource";
    if (is_object($var))    return "instance";
    return null;
}

function padding(string $template, array $vars): ?string {
    return preg_replace_callback(
        '/(\{\{)([A-Za-z0-9_\@\$\.\-\~\#\&]+)(\}\})/',
        function($matches) use ($vars) {
            return $vars[$matches[2]] ?? $matches[0];
        }, $template);
}

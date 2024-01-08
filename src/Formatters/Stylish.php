<?php

namespace Differ\Differ\Formatters\Stylish;

use function Differ\Differ\Formatters\toString;

function formatStylish($data, $replacer = ' ', $spacesCount = 4)
{
    $iter = function ($currentTree, $depth) use (&$iter, $spacesCount, $replacer) {

        $indentSize = $depth * $spacesCount;
        $bracketIndent = str_repeat($replacer, $indentSize - $spacesCount);
        $currentIndent = str_repeat($replacer, $indentSize);
        $lines = array_reduce(
            array_keys($currentTree),
            function ($acc, $key) use ($currentTree, $currentIndent, $depth, $iter) {


                $indentWithFlag = (isset($currentTree[$key]['flag']))
                    ? substr_replace($currentIndent, $currentTree[$key]['flag'], -2, 1)
                    : $currentIndent;
                $name = (isset($currentTree[$key]['name'])) ? $currentTree[$key]['name'] : $key;
                if (!isset($currentTree[$key]) || !is_array($currentTree[$key])) {
                    $value = toString($currentTree[$key]);
                } elseif (!isset($currentTree[$key]['flag'])) {
                    $value = $iter($currentTree[$key], $depth + 1);
                } else {
                    if (is_array($currentTree[$key]['value'])) {
                        $value = $iter($currentTree[$key]['value'], $depth + 1);
                    } else {
                        $value = toString($currentTree[$key]['value']);
                    }
                }

                $acc .= "{$indentWithFlag}{$name}: {$value}\n";
                return $acc;
            },
            ''
        );
        return "{\n{$lines}{$bracketIndent}}";
    };
    $result = $iter($data, 1);
    return $result;
}

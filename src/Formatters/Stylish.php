<?php

namespace Differ\Differ\Formatters\Stylish;

function toString(mixed $value): string
{
    $string = trim(var_export($value, true), "'");
    if ($string === 'NULL') {
        return 'null';
    } else {
        return $string;
    }
}

function formatStylish(array $data, string $replacer = ' ', int $spacesCount = 4): string
{

    $iter = function ($currentTree, $depth) use (&$iter, $spacesCount, $replacer) {
        $indentSize = $depth * $spacesCount;
        $bracketIndent = str_repeat($replacer, $indentSize - $spacesCount);
        $currentIndent = str_repeat($replacer, $indentSize);
        $signIndent = str_repeat($replacer, $indentSize - 2);

        $lines = array_map(
            function ($key) use ($currentTree, $currentIndent, $signIndent, $depth, $iter) {
                $item = $currentTree[$key];

                $flag = $item['flag'] ?? null;

                if (!$flag) {
                    if (is_array($item)) {
                        return "{$currentIndent}{$key}: {$iter($item, $depth + 1)}\n";
                    } else {
                        $value = toString($item);
                        return "{$currentIndent}{$key}: {$value}\n";
                    }
                }

                $children = $item['children'] ?? null;
                $name = $item['name'] ?? null;

                if ($flag === 'updated') {
                    if ($children) {
                        return "{$signIndent}  {$name}: {$iter($children, $depth + 1)}\n";
                    } else {
                        if (is_array($item['valueBefore'])) {
                            $valueBefore = $iter($item['valueBefore'], $depth + 1);
                        } else {
                            $valueBefore = toString($item['valueBefore']);
                        }

                        if (is_array($item['valueAfter'])) {
                            $valueAfter = $iter($item['valueAfter'], $depth + 1);
                        } else {
                            $valueAfter = toString($item['valueAfter']);
                        }

                        return "{$signIndent}- {$name}: {$valueBefore}\n{$signIndent}+ {$name}: {$valueAfter}\n";
                    }
                }

                if ($flag === 'removed') {
                    if (is_array($item['value'])) {
                        $value = $iter($item['value'], $depth + 1);
                    } else {
                        $value = toString($item['value']);
                    }
                    return "{$signIndent}- {$name}: {$value}\n";
                }

                if ($flag === 'added') {
                    if (is_array($item['value'])) {
                        $value = $iter($item['value'], $depth + 1);
                    } else {
                        $value = toString($item['value']);
                    }
                    return "{$signIndent}+ {$name}: {$value}\n";
                }

                if ($flag === 'equal') {
                    if (is_array($item['value'])) {
                        $value = $iter($item['value'], $depth + 1);
                    } else {
                        $value = toString($item['value']);
                    }
                    return "{$signIndent}  {$name}: {$value}\n";
                }
            },
            array_keys($currentTree),
        );
        $string = implode($lines);
        return "{\n{$string}{$bracketIndent}}";
    };

    $result = $iter($data, 1);
    return $result;
}

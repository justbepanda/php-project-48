<?php

namespace Differ\Differ\Formatters\Plain;

use function Functional\flatten;

function normalizeValue(mixed $text): string|false
{
    $str = json_encode($text);
    //замена кавычек
    if (isset($str[0]) && isset($str[1]) && $str[0] === '"' && $str[-1] === '"') {
        $str = "'" . substr($str, 1, -1) . "'";
    }
    return $str;
}

function formatPlain(array $data): string
{
    $iter = function ($data, $currentPath = '') use (&$iter) {
        return array_reduce(array_keys($data), function ($acc, $key) use ($iter, $data, $currentPath) {

            if ($currentPath !== '') {
                $currentPath .= '.';
            }
            $node = $data[$key];
            $flag = $node['flag'] ?? null;
            $children = $node['children'] ?? null;
            $name = ($node['name']) ?: $key;
            $name = $currentPath . $name;

            if ($flag === 'updated') {
                if ($children) {
                    $acc[] = $iter($node['children'], $name);
                } else {
                    if (is_array($node['valueBefore'])) {
                        $valueBefore = '[complex value]';
                    } else {
                        $valueBefore = normalizeValue($node['valueBefore']);
                    }

                    if (is_array($node['valueAfter'])) {
                        $valueAfter = '[complex value]';
                    } else {
                        $valueAfter = normalizeValue($node['valueAfter']);
                    }

                    $acc[] = "Property '{$name}' was updated. From {$valueBefore} to {$valueAfter}";
                }
            }
            if ($flag === 'added') {
                if (is_array($node['value'])) {
                    $value = '[complex value]';
                } else {
                    $value = normalizeValue($node['value']);
                }
                $acc[] = "Property '{$name}' was added with value: {$value}";
            }

            if ($flag === 'removed') {
                $acc[] = "Property '{$name}' was removed";
            }

            return $acc;
        }, []);
    };
    $result = flatten($iter($data));
    return implode("\n", $result);
}

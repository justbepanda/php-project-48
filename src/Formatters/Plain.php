<?php

namespace Differ\Differ\Formatters\Plain;

use function Functional\flatten;

function normalizeValue(mixed $text): string|false
{
    $str = json_encode($text);
    //замена кавычек
    if (isset($str[0]) && isset($str[1]) && $str[0] === '"' && $str[-1] === '"') {
        return "'" . substr($str, 1, -1) . "'";
    }
    return $str;
}

function formatPlain(array $data): string
{
    $iter = function ($data, $currentPath = '') use (&$iter) {

        return array_map(function ($key) use ($iter, $data, $currentPath) {

            if ($currentPath === '') {
                $newPath = '';
            } else {
                $newPath = $currentPath . '.';
            }
            $node = $data[$key];
            $flag = $node['flag'] ?? null;
            $children = $node['children'] ?? null;
            $name = $node['name'] ? $node['name'] : $key;
            $property = $newPath . $name;

            if ($flag === 'updated') {
                if ($children) {
                    return $iter($node['children'], $property);
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

                    return "Property '{$property}' was updated. From {$valueBefore} to {$valueAfter}";
                }
            }
            if ($flag === 'added') {
                if (is_array($node['value'])) {
                    $value = '[complex value]';
                } else {
                    $value = normalizeValue($node['value']);
                }
                return "Property '{$property}' was added with value: {$value}";
            }

            if ($flag === 'removed') {
                return "Property '{$property}' was removed";
            }
        }, array_keys($data));
    };
    $result = flatten($iter($data));
    $filteredResult = array_filter($result, fn($item) => $item !== null);

    return implode("\n", $filteredResult);
}

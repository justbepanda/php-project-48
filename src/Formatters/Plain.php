<?php

namespace Differ\Differ\Formatters\Plain;

function replaceQuotes($text)
{
    return str_replace('"', '\'', preg_replace('/((^|\s)"(\w))/um', '\2\'\3', $text));
}

function formatPlain($data, $currentPath = '')
{
    $result = array_reduce(array_keys($data), function ($acc, $key) use ($data, $currentPath) {
        $node = $data[$key];
        if ($currentPath !== '') {
            $currentPath .= '.';
        }
        $name = $currentPath . $node['name'];

        if (isset($node['value'][0]['name'])) {
            $acc .= formatPlain($node['value'], $name);
            return $acc;
        }

        if (isset($node['flag']) && $node['flag'] === '+') {
            if (isset($data[$key - 1]['name']) && ($data[$key - 1]['name'] === $data[$key]['name'])) {
                $newValue = (is_array($node['value'])) ? '[complex value]' : replaceQuotes(json_encode($node['value']));
                $oldValue = (is_array($data[$key - 1]['value']))
                    ? '[complex value]'
                    : replaceQuotes(json_encode($data[$key - 1]['value']));
                $acc .= "Property '{$name}' was updated. From {$oldValue} to {$newValue}\n";
            } else {
                $value = (is_array($node['value'])) ? '[complex value]' : replaceQuotes(json_encode($node['value']));
                $acc .= "Property '{$name}' was added with value: {$value}\n";
            }
        } elseif (isset($node['flag']) && $node['flag'] === '-') {
            if (!(isset($data[$key + 1]['name']) && ($data[$key + 1]['name'] === $data[$key]['name']))) {
                $acc .= "Property '{$name}' was removed\n";
            }
        }
        return $acc;
    }, '');
    return $result;
}

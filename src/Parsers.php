<?php

namespace Differ\Differ\Parsers;

// phpcs:disable
$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} else {
    require_once $autoloadPath2;
}
// phpcs:enable

use Symfony\Component\Yaml\Yaml;

function getJsonDiff($data1, $data2): string
{
    $data1 = json_decode($data1, true);
    $data2 = json_decode($data2, true);

    return parseDiff($data1, $data2);
}

function formatData($key, $value, $sign = ' '): string
{
    $value = var_export($value, true);
    return "  $sign $key: $value\n";
}

function getYamlDiff($data1, $data2): string
{
    $data1 = (array)Yaml::parse($data1, Yaml::PARSE_OBJECT_FOR_MAP);
    $data2 = (array)Yaml::parse($data2, Yaml::PARSE_OBJECT_FOR_MAP);
    return parseDiff($data1, $data2);
}

function parseDiff($data1, $data2): string
{
    $keys = array_unique([...array_keys($data1), ...array_keys($data2)]);
    sort($keys);

    $result = array_reduce($keys, function ($acc, $key) use ($data1, $data2) {
        if (!array_key_exists($key, $data1)) {
            $acc .= formatData($key, $data2[$key], '+');
        } elseif (!array_key_exists($key, $data2)) {
            $acc .= formatData($key, $data1[$key], '-');
        } elseif ($data1[$key] === $data2[$key]) {
            $acc .= formatData($key, $data1[$key]);
        } else {
            $acc .= formatData($key, $data1[$key], '-');
            $acc .= formatData($key, $data2[$key], '+');
        }

        return $acc;
    }, '');
    return "{\n$result}\n";
}

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

function parseJson(string $data): array
{
    return json_decode($data, true);
}

function parseYaml(string $data): array
{
    return objectToArray(Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP));
}

function objectToArray(object $data): array
{
    $data = (array)$data;
    $result = array_reduce(array_keys($data), function ($acc, $key) use ($data) {
        $value = $data[$key];
        if (is_object($value)) {
            $acc[$key] = objectToArray($value);
        } else {
            $acc[$key] = $value;
        }
        return $acc;
    }, []);
    return $result;
}

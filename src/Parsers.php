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


function parseJson($data)
{

    return json_decode($data, true);
}

function parseYaml($data)
{
    return objectToArray(Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP));
}

function objectToArray($data): array
{
    $result = [];
    foreach ($data as $key => $value) {
        $result[$key] = (is_array($value) || is_object($value)) ? objectToArray($value) : $value;
    }
    return $result;
}

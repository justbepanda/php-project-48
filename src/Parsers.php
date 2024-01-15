<?php

namespace Differ\Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseFile(string $pathToFile): array|false
{
    $content = (string)file_get_contents($pathToFile);
    $extension = pathinfo($pathToFile, PATHINFO_EXTENSION);
    switch ($extension) {
        case 'json':
            $parsedData = parseJson($content);
            break;
        case 'yml':
        case 'yaml':
            $parsedData = parseYaml($content);
            break;
        default:
            return false;
    }
    return $parsedData;
}

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
    $arrData = (array)$data;
    $values = array_map(function ($key) use ($arrData) {
        $value = $arrData[$key];
        if (is_object($value)) {
            return objectToArray($value);
        } else {
            return $value;
        }
    }, array_keys($arrData));
    return array_combine(array_keys($arrData), $values);
}

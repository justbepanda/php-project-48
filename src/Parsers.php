<?php

namespace Differ\Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseFile(string $pathToFile): array|false
{
    $content = (string) file_get_contents($pathToFile);
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

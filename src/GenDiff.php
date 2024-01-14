<?php

namespace Differ\Differ;

// phpcs:disable
$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} else {
    require_once $autoloadPath2;
}

// phpcs:enable

use function Differ\Differ\Parsers\parseJson;
use function Differ\Differ\Parsers\parseYaml;
use function Differ\Differ\Formatters\formatData;

function genDiff(string $pathToFile1, string $pathToFile2, string $formatter = "stylish"): bool|string
{
    // Получить содержимое файлов
    $content1 = file_get_contents($pathToFile1);
    $content2 = file_get_contents($pathToFile2);

    if (!$content1 || !$content2) {
        return false;
    }

    // Получить расширения файлов
    $extension1 = pathinfo($pathToFile1, PATHINFO_EXTENSION);
    $extension2 = pathinfo($pathToFile2, PATHINFO_EXTENSION);

    // Проверить, что расширения файлов совпадают
    if ($extension1 !== $extension2) {
        return false;
    }

    // В зависимости от расширения файла, получить содержание в нужном парсере
    switch ($extension1) {
        case 'json':
            $parsedData1 = parseJson($content1);
            $parsedData2 = parseJson($content2);
            break;
        case 'yml':
        case 'yaml':
            $parsedData1 = parseYaml($content1);
            $parsedData2 = parseYaml($content2);
            break;
        default:
            return false;
    }

    $diff = compareData($parsedData1, $parsedData2);

    $formattedData = formatData($diff, $formatter);

    return $formattedData;
}

function formatRemoved(array $data, array $keys): array
{
    return array_map(fn($key) => [
        "name" => $key,
        "flag" => "removed",
        "value" => $data[$key],
    ], $keys);
}

function formatAdded(array $data, array $keys): array
{
    return array_map(fn($key) => [
        "name" => $key,
        "flag" => "added",
        "value" => $data[$key],
    ], $keys);
}

function formatEqual(array $data, array $keys): array
{
    return array_map(fn($key) => [
        "name" => $key,
        "flag" => "equal",
        "value" => $data[$key],
    ], $keys);
}

function formatUpdated(array $data1, array $data2, array $keys): array
{
    return array_map(function ($key) use ($data1, $data2) {
        if (is_array($data1[$key]) && is_array($data2[$key])) {
            return [
                "name" => $key,
                "flag" => "updated",
                "children" => compareData($data1[$key], $data2[$key]),
            ];
        } else {
            return [
                "name" => $key,
                "flag" => "updated",
                "valueBefore" => $data1[$key],
                "valueAfter" => $data2[$key],
            ];
        }
    }, $keys);
}

function compareData(array $data1, array $data2): array
{
    $removedKeys = array_filter(array_keys($data1), fn($key) => !array_key_exists($key, $data2));
    $addedKeys = array_filter(array_keys($data2), fn($key) => !array_key_exists($key, $data1));
    $updatedKeys = array_filter(array_keys($data1), fn($key) => array_key_exists($key, $data1) &&
        array_key_exists($key, $data2) && $data1[$key] !== $data2[$key]);
    $equalKeys = array_filter(array_keys($data1), fn($key) => array_key_exists($key, $data1) &&
        array_key_exists($key, $data2) && $data1[$key] === $data2[$key]);

    $removedData = formatRemoved($data1, $removedKeys);
    $addedData = formatAdded($data2, $addedKeys);
    $updatedData = formatUpdated($data1, $data2, $updatedKeys);
    $equalData = formatEqual($data1, $equalKeys);

    $comparedData = [...$removedData, ...$addedData, ...$updatedData, ...$equalData];

    usort($comparedData, function ($item1, $item2) {
        return $item1['name'] <=> $item2['name'];
    });

    return $comparedData;
}

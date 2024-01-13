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

function genDiff($pathToFile1, $pathToFile2, $formatter = "stylish")
{
    // Получить содержимое файлов
    $content1 = file_get_contents($pathToFile1);
    $content2 = file_get_contents($pathToFile2);

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


function compareData($data1, $data2)
{
    $removedDataKeys = array_filter(array_keys($data1), fn($key) => !array_key_exists($key, $data2));
    $addedDataKeys = array_filter(array_keys($data2), fn($key) => !array_key_exists($key, $data1));
    $updatedDataKeys = array_filter(array_keys($data1), fn($key) =>
        array_key_exists($key, $data1) && array_key_exists($key, $data2) && $data1[$key] !== $data2[$key]);
    $equalDataKeys = array_filter(array_keys($data1), fn($key) =>
        array_key_exists($key, $data1) && array_key_exists($key, $data2) && $data1[$key] === $data2[$key]);

    $formatRemovedData = array_map(fn($key) => [
        "name" => $key,
        "flag" => "removed",
        "value" => $data1[$key],
    ], $removedDataKeys);

    $formatAddedData = array_map(fn($key) => [
        "name" => $key,
        "flag" => "added",
        "value" => $data2[$key],
    ], $addedDataKeys);

    $formatEqualData = array_map(fn($key) => [
        "name" => $key,
        "flag" => "equal",
        "value" => $data1[$key],
    ], $equalDataKeys);

    $updatedDataKeys = array_map(function ($key) use ($data1, $data2) {
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
    }, $updatedDataKeys);

    $comparedData = [...$formatRemovedData, ...$formatAddedData, ...$formatEqualData, ...$updatedDataKeys];

    usort($comparedData, function ($item1, $item2) {
        return $item1['name'] <=> $item2['name'];
    });

    return $comparedData;
}

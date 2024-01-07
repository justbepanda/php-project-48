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

    $diff = getDiff($parsedData1, $parsedData2);

    $formattedData = formatData($diff, $formatter);

    return $formattedData;
}

function getDiff($data1, $data2)
{
    $keys = array_unique([...array_keys($data1), ...array_keys($data2)]);
    sort($keys);

    $result = array_reduce($keys, function ($acc, $key) use ($data1, $data2) {
        $flag = ' ';
        if (!array_key_exists($key, $data1)) {
            $flag = '+';
            $value = $data2[$key];
        } elseif (!array_key_exists($key, $data2)) {
            $flag = '-';
            $value = $data1[$key];
        } elseif (is_array($data1[$key]) && is_array($data2[$key])) {
            $value = getDiff($data1[$key], $data2[$key]);
        } elseif ($data1[$key] === $data2[$key]) {
            $value = $data1[$key];
        } else {
            $acc[] = ['name' => $key, 'flag' => '-', 'value' => $data1[$key]];
            $flag = '+';
            $value = $data2[$key];
        }
        $acc[] = ['name' => $key, 'flag' => $flag, 'value' => $value];
        return $acc;
    }, []);
    return $result;
}

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

use function Differ\Differ\Parsers\getJsonDiff;
use function Differ\Differ\Parsers\getYamlDiff;

function genDiff($pathToFile1, $pathToFile2): string
{
    $format = match (true) {
        str_ends_with($pathToFile1, '.json') && str_ends_with($pathToFile2, '.json') => 'json',
        str_ends_with($pathToFile1, '.yml') && str_ends_with($pathToFile2, '.yml') ||
        str_ends_with($pathToFile1, '.yaml') && str_ends_with($pathToFile2, '.yaml') => 'yaml',
        default => false
    };
    if (!$format) {
        return false;
    }

    // получаем содержимое файлов
    $data1 = file_get_contents($pathToFile1);
    $data2 = file_get_contents($pathToFile2);

    // сравниваем содержимое
    $result = match ($format) {
        'json' => getJsonDiff($data1, $data2),
        'yaml' => getYamlDiff($data1, $data2)
    };

    return $result;
}

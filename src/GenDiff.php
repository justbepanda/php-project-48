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

    // В зависимости от форматтера, форматировать данные
    switch ($formatter) {
        case 'stylish':
            $formattedData = formatDataStylish($diff);
            break;
        default:
            return "Неизвестный форматтер: $formatter";
    }

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

function toString($value)
{
    $result = trim(var_export($value, true), "'");
    if ($result === 'NULL') {
        $result = 'null';
    }
    return $result;
}

function formatDataStylish($diff, $replacer = ' ', $spacesCount = 4)
{
    $iter = function ($currentTree, $depth) use (&$iter, $spacesCount, $replacer) {
        $indentSize = $depth * $spacesCount;
        $bracketIndent = str_repeat($replacer, $indentSize - $spacesCount);
        $currentIndent = str_repeat($replacer, $indentSize);
        $lines = array_reduce(
            array_keys($currentTree),
            function ($acc, $key) use ($currentTree, $currentIndent, $depth, $iter) {

                $indentWithFlag = (isset($currentTree[$key]['flag']))
                    ? substr_replace($currentIndent, $currentTree[$key]['flag'], -2, 1)
                    : $currentIndent;
                $name = (isset($currentTree[$key]['name'])) ? $currentTree[$key]['name'] : $key;

                if (!isset($currentTree[$key]) || !is_array($currentTree[$key])) {
                    $value = toString($currentTree[$key]);
                } elseif (!isset($currentTree[$key]['flag'])) {
                    $value = $iter($currentTree[$key], $depth + 1);
                } else {
                    if (is_array($currentTree[$key]['value'])) {
                        $value = $iter($currentTree[$key]['value'], $depth + 1);
                    } else {
                        $value = toString($currentTree[$key]['value']);
                    }
                }

                if ($value !== '') {
                    $value = " $value";
                }

                $acc .= "{$indentWithFlag}{$name}:{$value}\n";
                return $acc;
            },
            ''
        );
        return "{\n{$lines}{$bracketIndent}}";
    };

    $result = $iter($diff, 1);
    return $result;
}

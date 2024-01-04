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

function genDiff($pathToFile1, $pathToFile2, $formatter = 'stylish')
{

    // Получить содержимое файлов
    $content1 = file_get_contents($pathToFile1);
    $content2 = file_get_contents($pathToFile2);

    // В зависимости от расширения файла, получить содержание в нужном парсере
    if (str_ends_with($pathToFile1, '.json') && str_ends_with($pathToFile2, '.json')) {
        $parsedData1 = parseJson($content1);
        $parsedData2 = parseJson($content2);
    } elseif (
        str_ends_with($pathToFile1, '.yml') && str_ends_with($pathToFile2, '.yml') ||
        str_ends_with($pathToFile1, '.yaml') && str_ends_with($pathToFile2, '.yaml')
    ) {
        $parsedData1 = parseYaml($content1);
        $parsedData2 = parseYaml($content2);
    } else {
        return false;
    }
    $diff = getDiff($parsedData1, $parsedData2);

    if ($formatter === 'stylish') {
        $formattedData = formatDataStylish($diff);
    }


    return $formattedData;
}

function getDiff($data1, $data2)
{


    $keys = array_unique([...array_keys($data1), ...array_keys($data2)]);
    sort($keys);

    $result = array_reduce($keys, function ($acc, $key) use ($data1, $data2) {

        if (array_key_exists($key, $data1) && array_key_exists($key, $data2)) {
            if (is_array($data1[$key]) && is_array($data2[$key])) {
                $acc[] = ['name' => $key, 'flag' => ' ', 'value' => getDiff($data1[$key], $data2[$key])];
                return $acc;
            }
            if ($data1[$key] === $data2[$key]) {
                $acc[] = ['name' => $key, 'flag' => ' ', 'value' => $data1[$key]];
                return $acc;
            }

            $acc[] = ['name' => $key, 'flag' => '-', 'value' => $data1[$key]];
            $acc[] = ['name' => $key, 'flag' => '+', 'value' => $data2[$key]];
            return $acc;
        }
        if (!array_key_exists($key, $data1)) {
            $acc[] = ['name' => $key, 'flag' => '+', 'value' => $data2[$key]];
            return $acc;
        }
        if (!array_key_exists($key, $data2)) {
            $acc[] = ['name' => $key, 'flag' => '-', 'value' => $data1[$key]];
            return $acc;
        }

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
        $lines = array_reduce(
            array_keys($currentTree),
            function ($acc, $key) use ($currentTree, $spacesCount, $replacer, $indentSize, $depth, $iter) {
                if (is_array($currentTree[$key]) && array_key_exists('flag', $currentTree[$key])) {
                    $indent = str_repeat($replacer, $indentSize - 2) . $currentTree[$key]['flag'] . ' ';
                } else {
                    $indent = str_repeat($replacer, $indentSize);
                }

                if (!is_array($currentTree[$key])) {
                    $acc .= $indent . $key . ': ' . toString($currentTree[$key]) . "\n";
                    return $acc;
                }

                if (!array_key_exists('flag', $currentTree[$key])) {
                    $acc .= "$indent{$key}: " . $iter($currentTree[$key], $depth + 1) . "\n";
                    return $acc;
                }

                if (is_array($currentTree[$key]['value'])) {
                    $currentTree[$key]['value'] = $iter($currentTree[$key]['value'], $depth + 1);
                }


                $value = toString($currentTree[$key]['value']);
                if ($value !== '') {
                    $value = " $value";
                }

                $acc .= "$indent{$currentTree[$key]['name']}:$value\n";
                return $acc;
            },
            ''
        );
        return "{\n{$lines}{$bracketIndent}}";
    };

    $result = $iter($diff, 1);
    return $result;
}

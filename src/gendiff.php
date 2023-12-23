<?php

namespace Differ\Differ;

use Docopt;

$doc = <<<DOC
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format <fmt>                Report format [default: stylish]

DOC;

$args = Docopt::handle($doc);
foreach ($args as $k => $v) {
    echo $k . ': ' . json_encode($v) . PHP_EOL;
}


function genDiff($pathToFile1, $pathToFile2): string
{
    // получаем содержимое файлов
    $data1 = file_get_contents($pathToFile1);
    $data2 = file_get_contents($pathToFile2);

    $data1json = json_decode($data1, true);
    $data2json = json_decode($data2, true);

    // сравниваем содержимое
    $result = getDiff($data1json, $data2json);

    // отправляем
    return $result;
}

function getDiff($data1, $data2): string
{
    $keys = array_unique([...array_keys($data1), ...array_keys($data2)]);
    sort($keys);

    $result = array_reduce($keys, function ($acc, $key) use ($data1, $data2) {
        if (!array_key_exists($key, $data1)) {
            $acc .= "  + $key: " . var_export($data2[$key], true) . "\n";
        } elseif (!array_key_exists($key, $data2)) {
            $acc .= "  - $key: " . var_export($data1[$key], true) . "\n";
        } elseif ($data1[$key] === $data2[$key]) {
            $acc .= "    $key: " . var_export($data1[$key], true) . "\n";
        } else {
            $acc .= "  - $key: " . var_export($data1[$key], true) . "\n";
            $acc .= "  + $key: " . var_export($data2[$key], true) . "\n";
        }

        return $acc;
    }, '');

    return "{\n$result}\n";
}

<?php

namespace Differ\Differ;

function genDiff($pathToFile1, $pathToFile2): string
{
    // получаем содержимое файлов
    $data1 = file_get_contents($pathToFile1);
    $data2 = file_get_contents($pathToFile2);
    $data1json = json_decode($data1, true);
    $data2json = json_decode($data2, true);

    // сравниваем содержимое
    $result = getJsonDiff($data1json, $data2json);

    // отправляем
    return $result;
}

function getJsonDiff($data1, $data2): string
{

    $keys = array_unique([...array_keys($data1), ...array_keys($data2)]);
    sort($keys);

    $result = array_reduce($keys, function ($acc, $key) use ($data1, $data2) {
        if (!array_key_exists($key, $data1)) {
            $acc .= formatData($key, $data2[$key], '+');
        } elseif (!array_key_exists($key, $data2)) {
            $acc .= formatData($key, $data1[$key], '-');
        } elseif ($data1[$key] === $data2[$key]) {
            $acc .= formatData($key, $data1[$key]);
        } else {
            $acc .= formatData($key, $data1[$key], '-');
            $acc .= formatData($key, $data2[$key], '+');
        }

        return $acc;
    }, '');

    return "{\n$result}\n";
}

function formatData($key, $value, $sign = ' '): string
{
    $value = var_export($value, true);
    return "  $sign $key: $value\n";
}

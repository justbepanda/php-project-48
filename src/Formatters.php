<?php

namespace Differ\Differ\Formatters;

use function Differ\Differ\Formatters\Stylish\formatStylish;
use function Differ\Differ\Formatters\Plain\formatPlain;
use function Differ\Differ\Formatters\Json\formatJson;

function formatData($data, $formatter)
{
    switch ($formatter) {
        case 'stylish':
            $formattedData = formatStylish($data);
            break;
        case 'plain':
            $formattedData = formatPlain($data);
            break;
        case 'json':
            $formattedData = formatJson($data);
            break;
        default:
            return "Неизвестный форматтер: $formatter";
    }
    return $formattedData;
}

function toString($value)
{
    $result = trim(var_export($value, true), "'");
    if ($result === 'NULL') {
        $result = 'null';
    }
    return $result;
}

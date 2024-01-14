<?php

namespace Differ\Differ\Formatters;

use function Differ\Differ\Formatters\Stylish\formatStylish;
use function Differ\Differ\Formatters\Plain\formatPlain;
use function Differ\Differ\Formatters\Json\formatJson;

function formatData(array $data, string $formatter): string|bool
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
            return false;
    }
    return $formattedData;
}

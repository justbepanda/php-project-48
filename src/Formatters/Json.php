<?php

namespace Differ\Differ\Formatters\Json;

function formatJson(array $data): string|false
{
    return json_encode($data, JSON_FORCE_OBJECT);
}

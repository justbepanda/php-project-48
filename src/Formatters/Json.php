<?php

namespace Differ\Differ\Formatters\Json;

function formatJson($data)
{
    return json_encode($data, JSON_FORCE_OBJECT);
}

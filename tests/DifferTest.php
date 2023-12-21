<?php

namespace Differ\Differ\Tests;

require_once __DIR__ . '/../src/autoload.php';

use PHPUnit\Framework\TestCase;
use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testGenDiff():void
    {
        $str = 'yo';
        $this->assertEquals($str, genDiff($str));
    }


}
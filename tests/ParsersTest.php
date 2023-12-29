<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\Parsers\getJsonDiff;
use function Differ\Differ\Parsers\getYamlDiff;

class ParsersTest extends TestCase
{
    public function getFixtureFullPath($fixtureName)
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    public function testJsonDiff(): void
    {
        $data1 = file_get_contents($this->getFixtureFullPath('file1.json'));
        $data2 = file_get_contents($this->getFixtureFullPath('file2.json'));
        $expected = file_get_contents($this->getFixtureFullPath('expected.txt'));

        $this->assertEquals($expected, getJsonDiff($data1, $data2));
    }

    public function testYamlDiff(): void
    {
        $data1 = file_get_contents($this->getFixtureFullPath('file1.yml'));
        $data2 = file_get_contents($this->getFixtureFullPath('file2.yml'));
        $expected = file_get_contents($this->getFixtureFullPath('expected.txt'));

        $this->assertEquals($expected, getYamlDiff($data1, $data2));
    }
}

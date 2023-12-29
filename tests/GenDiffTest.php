<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    public function getFixtureFullPath($fixtureName)
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    public function testJson(): void
    {
        $file1 = $this->getFixtureFullPath('file1.json');
        $file2 = $this->getFixtureFullPath('file2.json');
        $expected = file_get_contents($this->getFixtureFullPath('expected.txt'));

        $this->assertEquals($expected, genDiff($file1, $file2));
    }
    public function testYaml(): void
    {
        $file1 = $this->getFixtureFullPath('file1.yml');
        $file2 = $this->getFixtureFullPath('file2.yml');
        $expected = file_get_contents($this->getFixtureFullPath('expected.txt'));

        $this->assertEquals($expected, genDiff($file1, $file2));
    }
    public function testDifferentFormats(): void
    {
        $file1 = $this->getFixtureFullPath('file1.yml');
        $file2 = $this->getFixtureFullPath('file1.json');

        $this->assertEmpty(genDiff($file1, $file2));

    }

    public function testWrongFormat(): void {
        $file1 = $this->getFixtureFullPath('file1.jpg');
        $file2 = $this->getFixtureFullPath('file2.jpg');

        $this->assertEmpty(genDiff($file1, $file2));
    }
}

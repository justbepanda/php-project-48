<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\Parsers\parseJson;
use function Differ\Differ\Parsers\parseYaml;

class ParsersTest extends TestCase
{
    private $expectedFlat;
    private $expectedTree;

    protected function setUp(): void
    {
        $this->expectedFlat = [
            "host" => "hexlet.io",
            "timeout" => 50,
            "proxy" => "123.234.53.22",
            "follow" => false
        ];
        $this->expectedTree =
            [
                "common" => [
                    "setting1" => "Value 1",
                    "setting2" => 200,
                    "setting3" => true,
                    "setting6" => [
                        "key" => "value",
                        "doge" => [
                            "wow" => ""
                        ]
                    ]
                ],
                "group1" => [
                    "baz" => "bas",
                    "foo" => "bar",
                    "nest" => [
                        "key" => "value"
                    ]
                ],
                "group2" => [
                    "abc" => 12345,
                    "deep" => [
                        "id" => 45
                    ]
                ]
            ];
    }

    public function getFixtureFullPath($fixtureName)
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }


    public function testJsonFlatParse(): void
    {
        $file = file_get_contents($this->getFixtureFullPath('flat1.json'));
        $this->assertEquals($this->expectedFlat, parseJson($file));
    }

    public function testJsonTreeParse(): void
    {
        $file = file_get_contents($this->getFixtureFullPath('tree1.json'));
        $this->assertEquals($this->expectedTree, parseJson($file));
    }

    public function testYamlFlatParse(): void
    {
        $file = file_get_contents($this->getFixtureFullPath('flat1.yml'));
        $this->assertEquals($this->expectedFlat, parseYaml($file));
    }

    public function testYamlTreeParse(): void
    {
        $file = file_get_contents($this->getFixtureFullPath('tree1.yml'));
        $this->assertEquals($this->expectedTree, parseYaml($file));
    }
}

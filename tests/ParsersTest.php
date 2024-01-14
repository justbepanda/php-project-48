<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\Parsers\parseJson;
use function Differ\Differ\Parsers\parseYaml;

class ParsersTest extends TestCase
{
    private array $expectedFlat;
    private array $expectedTree;

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

    public function testJsonFlatParse(): void
    {
        $filepath = file_get_contents(__DIR__ . "/fixtures/flat1.json");
        if ($filepath) {
            $this->assertEquals($this->expectedFlat, parseJson($filepath));
        }
    }

    public function testJsonTreeParse(): void
    {
        $filepath = file_get_contents(__DIR__ . "/fixtures/tree1.json");
        if ($filepath) {
            $this->assertEquals($this->expectedTree, parseJson($filepath));
        }
    }

    public function testYamlFlatParse(): void
    {
        $filepath = file_get_contents(__DIR__ . "/fixtures/flat1.yml");
        if ($filepath) {
            $this->assertEquals($this->expectedFlat, parseYaml($filepath));
        }
    }

    public function testYamlTreeParse(): void
    {
        $filepath = file_get_contents(__DIR__ . "/fixtures/tree1.yml");
        if ($filepath) {
            $this->assertEquals($this->expectedTree, parseYaml($filepath));
        }
    }
}

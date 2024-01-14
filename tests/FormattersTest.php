<?php

namespace Differ\Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\Formatters\Stylish\formatStylish;
use function Differ\Differ\Formatters\Plain\formatPlain;
use function Differ\Differ\Formatters\Json\formatJson;

class FormattersTest extends TestCase
{
    public array $sourceData =
        [
            [
                "name" => "common",
                "flag" => "updated",
                "children" => [
                    [
                        "name" => "follow",
                        "flag" => "added",
                        "value" => false
                    ],
                    [
                        "name" => "setting1",
                        "flag" => "equal",
                        "value" => "Value 1"
                    ],
                    [
                        "name" => "setting2",
                        "flag" => "removed",
                        "value" => 200
                    ],
                    [
                        "name" => "setting3",
                        "flag" => "updated",
                        "valueBefore" => true,
                        "valueAfter" => null,
                    ],
                    [
                        "name" => "setting4",
                        "flag" => "added",
                        "value" => "blah blah"
                    ],
                    [
                        "name" => "setting5",
                        "flag" => "added",
                        "value" => [
                            "key5" => "value5"
                        ]
                    ],
                    [
                        "name" => "setting6",
                        "flag" => "updated",
                        "children" => [
                            [
                                "name" => "doge",
                                "flag" => "updated",
                                "children" => [
                                    [
                                        "name" => "wow",
                                        "flag" => "updated",
                                        "valueBefore" => "",
                                        "valueAfter" => "so much"
                                    ]
                                ]
                            ],
                            [
                                "name" => "key",
                                "flag" => "equal",
                                "value" => "value"
                            ],
                            [
                                "name" => "ops",
                                "flag" => "added",
                                "value" => "vops"
                            ]
                        ]
                    ]
                ]
            ],
            [
                "name" => "group1",
                "flag" => "updated",
                "children" => [
                    [
                        "name" => "baz",
                        "flag" => "updated",
                        "valueBefore" => "bas",
                        "valueAfter" => "bars"
                    ],
                    [
                        "name" => "foo",
                        "flag" => "equal",
                        "value" => "bar"
                    ],
                    [
                        "name" => "nest",
                        "flag" => "updated",
                        "valueBefore" => [
                            "key" => "value"
                        ],
                        "valueAfter" => "str"
                    ]
                ]
            ],
            [
                "name" => "group2",
                "flag" => "removed",
                "value" => [
                    "abc" => 12345,
                    "deep" => [
                        "id" => 45
                    ]
                ]
            ],
            [
                "name" => "group3",
                "flag" => "added",
                "value" => [
                    "deep" => [
                        "id" => [
                            "number" => 45
                        ]
                    ],
                    "fee" => 100500
                ]
            ]
        ];


    public function testFormatStylishTree(): void
    {
        $expected = file_get_contents(__DIR__ . "/fixtures/expected-gendiff-tree-stylish.txt");
        $this->assertEquals($expected, formatStylish($this->sourceData));
    }

    public function testFormatPlainTree(): void
    {
        $expected = file_get_contents(__DIR__ . "/fixtures/expected-gendiff-tree-plain.txt");
        $this->assertEquals($expected, formatPlain($this->sourceData));
    }

    public function testFormatJsonTree(): void
    {
        $expected = file_get_contents(__DIR__ . "/fixtures/expected-gendiff-tree-json.json");
        $this->assertEquals($expected, formatJson($this->sourceData));
    }
}

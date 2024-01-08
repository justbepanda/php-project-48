<?php

namespace Differ\Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\Formatters\Stylish\formatStylish;
use function Differ\Differ\Formatters\Plain\formatPlain;
use function Differ\Differ\Formatters\Json\formatJson;

class FormattersTest extends TestCase
{
    public function getFixtureFullPath($fixtureName)
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    public function testFormatStylishFlat(): void
    {
        $data = [
            [
                "name" => "follow",
                "flag" => "-",
                "value" => false,
            ],
            [
                "name" => "host",
                "flag" => " ",
                "value" => "hexlet.io",
            ],
            [
                "name" => "proxy",
                "flag" => "-",
                "value" => "123.234.53.22",

            ],
            [
                "name" => "timeout",
                "flag" => "-",
                "value" => 50,
            ],
            [
                "name" => "timeout",
                "flag" => "+",
                "value" => 20,
            ],
            [
                "name" => "verbose",
                "flag" => "+",
                "value" => true,

            ]
        ];
        $expected = file_get_contents($this->getFixtureFullPath('expected-gendiff-flat-stylish.txt'));
        $this->assertEquals($expected, formatStylish($data));
    }

    public function testFormatStylishTree(): void
    {
        $data = [
            [
                "name" => "common",
                "flag" => " ",
                "value" => [
                    [
                        "name" => "follow",
                        "flag" => "+",
                        "value" => false
                    ],
                    [
                        "name" => "setting1",
                        "flag" => " ",
                        "value" => "Value 1",
                    ],
                    ["name" => "setting2",
                        "flag" => "-",
                        "value" => 200,
                    ],
                    ["name" => "setting3",
                        "flag" => "-",
                        "value" => true,
                    ],
                    ["name" => "setting3",
                        "flag" => "+",
                        "value" => null,
                    ],
                    [
                        "name" => "setting4",
                        "flag" => "+",
                        "value" => "blah blah",
                    ],
                    ["name" => "setting5",
                        "flag" => "+",
                        "value" => [
                            "key5" => "value5"
                        ],
                    ],
                    [
                        "name" => "setting6",
                        "flag" => " ",
                        "value" => [
                            [
                                "name" => "doge",
                                "flag" => " ",
                                "value" => [
                                    [
                                        "name" => "wow",
                                        "flag" => "-",
                                        "value" => "",
                                    ],
                                    [
                                        "name" => "wow",
                                        "flag" => "+",
                                        "value" => "so much",
                                    ],
                                ],
                            ],
                            [
                                "name" => "key",
                                "flag" => " ",
                                "value" => "value",
                            ],
                            ["name" => "ops",
                                "flag" => "+",
                                "value" => "vops",
                            ]
                        ]
                    ]
                ]
            ],
            [
                "name" => "group1",
                "flag" => " ",
                "value" => [
                    [
                        "name" => "baz",
                        "flag" => "-",
                        "value" => "bas",
                    ],
                    [
                        "name" => "baz",
                        "flag" => "+",
                        "value" => "bars",
                    ],
                    [
                        "name" => "foo",
                        "flag" => " ",
                        "value" => "bar",
                    ],
                    [
                        "name" => "nest",
                        "flag" => "-",
                        "value" => [
                            "key" => "value"
                        ]
                    ],
                    ["name" => "nest",
                        "flag" => "+",
                        "value" => "str"
                    ]
                ]
            ],
            [
                "name" => "group2",
                "flag" => "-",
                "value" => [
                    "abc" => 12345,
                    "deep" => [
                        "id" => 45
                    ]
                ]
            ],
            [
                "name" => "group3",
                "flag" => "+",
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
        $expected = file_get_contents($this->getFixtureFullPath('expected-gendiff-tree-stylish.txt'));
        $this->assertEquals($expected, formatStylish($data));
    }

    public function testFormatPlainTree(): void
    {
        $data = [
            [
                "name" => "common",
                "flag" => " ",
                "value" => [
                    [
                        "name" => "follow",
                        "flag" => "+",
                        "value" => false
                    ],
                    [
                        "name" => "setting1",
                        "flag" => " ",
                        "value" => "Value 1",
                    ],
                    ["name" => "setting2",
                        "flag" => "-",
                        "value" => 200,
                    ],
                    ["name" => "setting3",
                        "flag" => "-",
                        "value" => true,
                    ],
                    ["name" => "setting3",
                        "flag" => "+",
                        "value" => null,
                    ],
                    [
                        "name" => "setting4",
                        "flag" => "+",
                        "value" => "blah blah",
                    ],
                    [
                        "name" => "setting5",
                        "flag" => "+",
                        "value" => [
                            "key5" => "value5"
                        ],
                    ],
                    [
                        "name" => "setting6",
                        "flag" => " ",
                        "value" => [
                            [
                                "name" => "doge",
                                "flag" => " ",
                                "value" => [
                                    [
                                        "name" => "wow",
                                        "flag" => "-",
                                        "value" => "",
                                    ],
                                    [
                                        "name" => "wow",
                                        "flag" => "+",
                                        "value" => "so much",
                                    ],
                                ],
                            ],
                            [
                                "name" => "key",
                                "flag" => " ",
                                "value" => "value",
                            ],
                            ["name" => "ops",
                                "flag" => "+",
                                "value" => "vops",
                            ]
                        ]
                    ]
                ]
            ],
            [
                "name" => "group1",
                "flag" => " ",
                "value" => [
                    [
                        "name" => "baz",
                        "flag" => "-",
                        "value" => "bas",
                    ],
                    [
                        "name" => "baz",
                        "flag" => "+",
                        "value" => "bars",
                    ],
                    [
                        "name" => "foo",
                        "flag" => " ",
                        "value" => "bar",
                    ],
                    [
                        "name" => "nest",
                        "flag" => "-",
                        "value" => [
                            "key" => "value"
                        ]
                    ],
                    ["name" => "nest",
                        "flag" => "+",
                        "value" => "str"
                    ]
                ]
            ],
            [
                "name" => "group2",
                "flag" => "-",
                "value" => [
                    "abc" => 12345,
                    "deep" => [
                        "id" => 45
                    ]
                ]
            ],
            [
                "name" => "group3",
                "flag" => "+",
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
        $expected = file_get_contents($this->getFixtureFullPath('expected-gendiff-tree-plain.txt'));
        $this->assertEquals($expected, formatPlain($data));
    }

    public function testFormatJsonTree(): void
    {
        $data = [
            [
                "name" => "common",
                "flag" => " ",
                "value" => [
                    [
                        "name" => "follow",
                        "flag" => "+",
                        "value" => false
                    ],
                    [
                        "name" => "setting1",
                        "flag" => " ",
                        "value" => "Value 1",
                    ],
                    ["name" => "setting2",
                        "flag" => "-",
                        "value" => 200,
                    ],
                    ["name" => "setting3",
                        "flag" => "-",
                        "value" => true,
                    ],
                    ["name" => "setting3",
                        "flag" => "+",
                        "value" => null,
                    ],
                    [
                        "name" => "setting4",
                        "flag" => "+",
                        "value" => "blah blah",
                    ],
                    [
                        "name" => "setting5",
                        "flag" => "+",
                        "value" => [
                            "key5" => "value5"
                        ],
                    ],
                    [
                        "name" => "setting6",
                        "flag" => " ",
                        "value" => [
                            [
                                "name" => "doge",
                                "flag" => " ",
                                "value" => [
                                    [
                                        "name" => "wow",
                                        "flag" => "-",
                                        "value" => "",
                                    ],
                                    [
                                        "name" => "wow",
                                        "flag" => "+",
                                        "value" => "so much",
                                    ],
                                ],
                            ],
                            [
                                "name" => "key",
                                "flag" => " ",
                                "value" => "value",
                            ],
                            ["name" => "ops",
                                "flag" => "+",
                                "value" => "vops",
                            ]
                        ]
                    ]
                ]
            ],
            [
                "name" => "group1",
                "flag" => " ",
                "value" => [
                    [
                        "name" => "baz",
                        "flag" => "-",
                        "value" => "bas",
                    ],
                    [
                        "name" => "baz",
                        "flag" => "+",
                        "value" => "bars",
                    ],
                    [
                        "name" => "foo",
                        "flag" => " ",
                        "value" => "bar",
                    ],
                    [
                        "name" => "nest",
                        "flag" => "-",
                        "value" => [
                            "key" => "value"
                        ]
                    ],
                    ["name" => "nest",
                        "flag" => "+",
                        "value" => "str"
                    ]
                ]
            ],
            [
                "name" => "group2",
                "flag" => "-",
                "value" => [
                    "abc" => 12345,
                    "deep" => [
                        "id" => 45
                    ]
                ]
            ],
            [
                "name" => "group3",
                "flag" => "+",
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
        $expected = file_get_contents($this->getFixtureFullPath('expected-gendiff-tree-json.json'));
        $this->assertEquals($expected, formatJson($data));
    }
}

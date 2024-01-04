<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;
use function Differ\Differ\getDiff;
use function Differ\Differ\formatDataStylish;

class GenDiffTest extends TestCase
{
    public function getFixtureFullPath($fixtureName)
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    public function testGetDiffFlat(): void
    {
        $parsedFlatData1 = [
            "host" => "hexlet.io",
            "timeout" => 50,
            "proxy" => "123.234.53.22",
            "follow" => false
        ];
        $parsedFlatData2 = [
            "timeout" => 20,
            "verbose" => true,
            "host" => "hexlet.io"
        ];
        $expectedFlatData = [
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
        $this->assertEquals($expectedFlatData, getDiff($parsedFlatData1, $parsedFlatData2));
    }

    public function testGetDiffTree(): void
    {
        $parsedTreeData1 = [
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
        $parsedTreeData2 = [
            "common" => [
                "follow" => false,
                "setting1" => "Value 1",
                "setting3" => null,
                "setting4" => "blah blah",
                "setting5" => [
                    "key5" => "value5"
                ],
                "setting6" => [
                    "key" => "value",
                    "ops" => "vops",
                    "doge" => [
                        "wow" => "so much"
                    ]
                ]
            ],
            "group1" => [
                "foo" => "bar",
                "baz" => "bars",
                "nest" => "str",
            ],
            "group3" => [
                "deep" => [
                    "id" => [
                        "number" => 45
                    ]
                ],
                "fee" => 100500
            ]
        ];
        $expectedTreeData = [
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
            ["name" => "group2",
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
        $this->assertEquals($expectedTreeData, getDiff($parsedTreeData1, $parsedTreeData2));
    }

    public function testFormatterFlatStylish(): void
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
        $this->assertEquals($expected, formatDataStylish($data));
    }

    public function testFormatterTreeStylish(): void
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
        $this->assertEquals($expected, formatDataStylish($data));
    }

    public function testGenDiffStylish(): void
    {
        $file1 = $this->getFixtureFullPath('tree1.yml');
        $file2 = $this->getFixtureFullPath('tree2.yml');
        $expected = file_get_contents($this->getFixtureFullPath('expected-gendiff-tree-stylish.txt'));
        $this->assertEquals($expected, genDiff($file1, $file2));
    }
}

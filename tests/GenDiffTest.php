<?php

namespace Differ\Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\compareData;

class GenDiffTest extends TestCase
{
    public function testCompareDataFlat(): void
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
                "flag" => "removed",
                "value" => false,
            ],
            [
                "name" => "host",
                "flag" => "equal",
                "value" => "hexlet.io",
            ],
            [
                "name" => "proxy",
                "flag" => "removed",
                "value" => "123.234.53.22",
            ],
            [
                "name" => "timeout",
                "flag" => "updated",
                "valueBefore" => 50,
                "valueAfter" => 20,
            ],
            [
                "name" => "verbose",
                "flag" => "added",
                "value" => true,
            ]
        ];
        $this->assertEquals($expectedFlatData, compareData($parsedFlatData1, $parsedFlatData2));
    }

    public function testCompareDataTree(): void
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
        $expectedTreeData =
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

        $this->assertEquals($expectedTreeData, compareData($parsedTreeData1, $parsedTreeData2));
    }
}

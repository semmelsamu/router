<?php

$example_routes = [
    [
        "url" => "*",
        "path" => "index.php",
        "args" => true,
    ],
    [
        "url" => "start",
        "path" => "index.php",
    ],
    [
        "url" => "site",
        "path" => "site.php",
        "suburls" => [
            [
                "url" => "sub",
                "path" => "sub.php",
            ],
        ]
    ],
];

?>
<?php

# TODO: Implement all features needed in the route() function
$routes = [
    [
        "id" => "index",
        "url" => "*", # index file for this directory
        "path" => "index.php"
    ],
    [
        "id" => "site0", # id for link generation
        "url" => "site_0", # request url
        "path" => "site0.php" # path to file in htdocs folder
    ],
    [
        "id" => "site1",
        "url" => "site_1",
        "path" => "site1.php",
        "suburls" => [
            [
                "id" => "site2",
                "url" => "hello", 
                "path" => "site2.php"
            ]
        ]
    ],
    [
        "id" => "searchsite",
        "url" => "search",
        "path" => "search.php"
    ]
];

?>
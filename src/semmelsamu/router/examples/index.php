<?php

    $routes = [
        "*" => [
            "file" => "index.php",
        ],
        "start" => [
            "file" => "index.php",
            "routes" => [
                "site" => [
                    "file" => "site.php",
                ],
            ],
        ],
    ];

    include("../index.php");

?>
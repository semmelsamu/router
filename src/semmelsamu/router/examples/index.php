<?php

    include("../index.php");

    $routes = new Route([
        "file" => "index.php",
        "routes" => [
            "start" => new Route([
                "file" => "index.php", 
                "args" => true,
                "routes" => [
                    "site" => new Route(["file" => "site.php", "id" => "site"]),
                ]
            ])
        ]
    ]);

    var_dump($routes->get_uri("site"));

?>
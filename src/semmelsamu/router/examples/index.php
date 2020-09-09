<?php

    include("../index.php");

    $routes = new Route([
        "file" => "index.php",
        "routes" => [
            "start" => new Route([
                "file" => "index.php", 
                "args" => true,
                "routes" => [
                    "site" => new Route(["file" => "site.php"]),
                ]
            ])
        ]
    ]);

    var_dump($routes->route());
    echo "<br>";
    var_dump($args);

?>
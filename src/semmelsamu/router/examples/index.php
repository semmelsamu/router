<?php

    function lg($content) {
        echo "<pre>";
        var_dump($content);
        echo "</pre>";
    }

    include("../index.php");

    $htdocs_folder = "htdocs/";

    $routes = new Route([
        "file" => $htdocs_folder."index.php",
        "routes" => [
            "start" => new Route([
                "file" => $htdocs_folder."start.php", 
                "args" => true,
                "routes" => [
                    "site" => new Route(["file" => $htdocs_folder."site.php", "id" => "site"]),
                ],
                "id" => "start"
            ])
        ],
        "id" => "index"
    ]);

    echo $routes->get_to("start");

?>
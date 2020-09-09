<?php

    include("../index.php");

    $htdocs_folder = "htdocs/";

    $routes = new Route([
        "file" => $htdocs_folder."start.php",
        "routes" => [
            "start" => new Route([
                "file" => $htdocs_folder."start.php", 
                "args" => true,
                "routes" => [
                    "site" => new Route(["file" => $htdocs_folder."site.php", "id" => "site"]),
                ]
            ])
        ]
    ]);

    $result = $routes->route();
    if(isset($result)) {
        include($result);
    }
    else {
        include($htdocs_folder."404.php");
    }

    echo "<br>Args: <pre>";
    print_r($args);
    echo "</pre>"

?>
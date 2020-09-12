<?php

    function lg($content) {
        echo "<pre>";
        var_dump($content);
        echo "</pre>";
    }

    include("../src/semmelsamu/router/index.php");

    $htdocs_folder = "htdocs/";

    $routes = new Route(["file" => $htdocs_folder."index.php", "id" => "index", "routes" => [
        "start" => new Route(["file" => $htdocs_folder."start.php", "args" => true, "id" => "start", "routes" => [
            "site" => new Route(["file" => $htdocs_folder."site.php", "id" => "site"]),
        ]])
    ]]);

    echo $routes->get_to("start");

?>
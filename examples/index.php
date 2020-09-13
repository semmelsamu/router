<?php

    include("../src/semmelsamu/router/index.php");

    $router = new Router(new Route(["file" => "htdocs/index.php", "id" => "start", "routes" => [
        "site" => new Route(["file" => "htdocs/site.php", "id" => "site", "routes" => [
            "sub" => new Route(["file" => "htdocs/sub.php", "id" => "sub", "accept_args" => true]),
        ]])
    ]]), "htdocs/404.php");

    $route = $router->route();

    echo "<br>Arguments: ";
    var_dump($route->args);

    echo "<br>Links: ";
    echo '<a href="'.$router->route_id("start").'">Link to Start</a> ';
    echo '<a href="'.$router->route_id("site").'">Link to Site</a> ';
    echo '<a href="'.$router->route_id("sub").'">Link to Sub</a> ';

    echo "<br>Relative path to root: ";
    echo $router->route_rel();


?>
<?php

    include("../src/semmelsamu/router/index.php");

    $htdocs_folder = "htdocs/";

    use \semmelsamu\Route;

    $route = new Route($htdocs_folder."index.php", [
        "site-1" => new Route($htdocs_folder."doc1.php", [
            "sub-site" => new Route($htdocs_folder."sub.php")
        ]),
        "site-2" => new Route($htdocs_folder."doc2.php")
    ]);

    $route->route();

    ?><a href="<?= $route->base() ?>">Back</a><?php












    /*

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

    echo "<br>URI: ";
    var_dump($router->get_uri());

    */

?>
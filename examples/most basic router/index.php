<?php

    // Include lib
    include("../../src/semmelsamu/router/index.php");

    // Namespace
    use \semmelsamu\Router;

    // Create Router
    $router = new Router();

    $router->url();

    // Route
    $router->route();

?>
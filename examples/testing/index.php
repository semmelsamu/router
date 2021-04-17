<?php

    include("../../index.php");

    $router = new \semmelsamu\Router();

    $router->add(new \semmelsamu\Route(url: "/video\/([0-9]*)\/comments\/([0-9]*)/"));

    $router->route();

?>
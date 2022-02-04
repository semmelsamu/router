<?php

include("../src/router.php");

$router = new \semmelsamu\Router();
$router->add("", function() { echo "Hello!"; });
echo $router->route();
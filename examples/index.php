<?php

// As this is an example, we need to overwrite the Base Path. This is not nessecary if used correctly.
error_reporting(0);
define("BASE_PATH", "/php-router/examples");

include("../src/php-router/main.php");
include("example_routes.php");

$router = new Php_router($example_routes);
$router->route_include();

?>
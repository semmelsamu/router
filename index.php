<?php

include("src/php-router/main.php");
$router = new Php_router($routes);

$router->route($_SERVER["REQUEST_URI"]);

?>
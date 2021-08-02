<?php

include("../src/router.php");

$router = new \semmelsamu\Router();

$router->add(url: "", callback: function() {echo "Hello";}, id: "index");
$router->add(url: "foto", callback: function() { global $router; $router->routes["index"]["callback"](); });

$router->add_404(function() { echo "404"; });

$router->route();

?>
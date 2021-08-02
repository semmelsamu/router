<?php

include("../src/router.php");

$router = new \semmelsamu\Router();

$router->add(url: "", callback: function() { global $router; var_dump($router->matches); });

$router->add_404(function() { echo "404"; });

$router->route();

?>
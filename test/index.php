<?php

include("../src/router.php");

$router = new \semmelsamu\Router();

$router->add(callback: function() { global $router; var_dump($router->matches); }, id: 1);

$router->add_404(function() { echo "404"; });

$router->route();

?>

<base href="<?= $router->base ?>">

<a href="<?= $router->id(1) ?>">Base <?= $router->base ?></a>
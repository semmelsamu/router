<?php

include("../src/router.php");

$router = new \semmelsamu\Router();

$router->add(url: "video/<video_id>/comments/<comment_id>", callback: function() {  global $router; var_dump($router->matches); }, id: 1);

$router->add_404(function() { echo "404"; });

$router->route();
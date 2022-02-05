<?php

echo "About Page here!<br>";


global $router;

$link = $router->base . $router->id(4);

echo "<a href=\"$link\">Link to contact page</a>";
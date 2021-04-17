<?php

    include("../../index.php");

    $router = new Router([
        "htdocs_folder" => "htdocs/",
        "error_document" => "404.php",
        "enable_sitemap" => true, // outputs a sitemap of all visible routes
        "file_modifiers" => true, // e.g. file modifiers
    ]);

    $router->add([
        "url" => "/.*/", // regex for the url
        "file" => "index.php", // the file the route should include
        "id" => 0, // the unique id of the route
        "accept_arguments" => false, // if further parts of the url are given, still use this route and get the parts
        "visible" => true, // should be included in the sitemap?
        "routes" => [], // further sub-routes
        "goto" => false, // url which has the same route with the id]);
    ]);

    $router->route();

?>
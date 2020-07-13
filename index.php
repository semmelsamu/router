<?php

# If the router should be only active in a specific directory, it should be defined here:
define("BASE_PATH", "/php-router");

# Path to where all documents are.
define("HTDOCS_PATH", "/htdocs");


# This is where the config and all our key functions are, so we should include it:
require_once("routes.php");
require_once("functions.php");

# To get the actual request our router should handle, we need to cut off the base path directories. For that we first need to know how many there are:
$base_paths = explode("/", trim(BASE_PATH, " /"));

# We also need the whole url:
$url = explode("/", trim($_SERVER["REQUEST_URI"], " /"));

# The request is an array, which contains every url part except the base paths:
$request = array_slice($url, sizeof($base_paths));

var_dump(route($request));

?>
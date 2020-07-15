<?php

# All settings the user would change are in settings.ini. We read them here and define the constants for our PHP script:
$settings = parse_ini_file("settings.ini", true);

# If the router should be only active in a specific directory, it should be defined here:
define("BASE_PATH", $settings["paths"]["base"]);

# Path to where all html documents should be.
define("HTDOCS_PATH", $settings["paths"]["htdocs"]);

# Default strings for the routes array and functions:
define("ROUTES_INDEX_URL", "*");
define("ROUTES_URL", "url");
define("ROUTES_SUBURLS", "suburls");
define("ROUTES_PATH", "path");

?>
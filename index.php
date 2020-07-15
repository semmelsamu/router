<?php

# If the router should be only active in a specific directory, it should be defined here:
define("BASE_PATH", "/php-router");

# Path to where all documents are.
define("HTDOCS_PATH", "/htdocs");

# In the routes list, how should the url of a index page be defined:
define("ROUTES_INDEX", "*");

# To get the actual request our router should handle, we need to cut off the base path directories. For that we first need to know how many there are:
$base_paths = explode("/", trim(BASE_PATH, " /"));

# We also need the whole url:
$url = explode("/", trim($_SERVER["REQUEST_URI"], " /"));

# The request is an array, which contains every url part except the base paths:
$request = array_slice($url, sizeof($base_paths));

$routes = [
    [
        "url" => ROUTES_INDEX,
        "path" => "index.html",
    ],
    [
        "url" => "site",
        "path" => "site.html",
    ],
];

var_dump($request); echo "<br><br><br>";
var_dump($routes); echo "<br><br><br>";

function route($request, $routes)
{
    # Process the next element of the request queue:
    $current_path = array_shift($request);
    $current_urls = array_column($routes, "url");

    # Check if the current request contains something, else we need to return the index file:
    if(isset($current_path))
    {
        # Check if a mathing url in the routes list exists
        if(in_array($current_path, $current_urls))
        {
            $current_url = $routes[array_search($current_path, $current_urls)];
            # Check if the request contains further elements
            if(!empty($request))
            {
                # Check if we have suburls we can process further
                if(isset($current_url["suburls"]))
                {
                    return(route($request, $current_url["suburls"]));
                }
            }
            else
            {
                return($current_url["path"]);
            }
        }
    }
    else
    {
        # Return the index page (if existing):

        if(in_array(ROUTES_INDEX, $current_urls));
        {
            return($routes[array_search(ROUTES_INDEX, $current_urls)]["path"]);
        }
    }
}

var_dump(route($request, $routes));


?>
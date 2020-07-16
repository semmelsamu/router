<?php
# ROUTER.PHP - Routing functions

function route_all() 
{
    # To get the actual request our router should handle, we need to cut off the base path directories. For that we first need to know how many there are:
    $base_paths = explode("/", trim(BASE_PATH, " /"));

    # We also need the whole url:
    $url = explode("/", trim($_SERVER["REQUEST_URI"], " /"));

    # The request is an array, which contains every url part except the base paths:
    $request = array_slice($url, sizeof($base_paths));

    global $routes;

    $route_path = route($request, $routes);
    if($route_path)
    {
        include(getcwd().HTDOCS_PATH."/".$route_path);
        return 0;
    }
    else
    {
        include(getcwd().HTDOCS_PATH."/".PATH_TO_404);
    }
}

function route($request, $routes)
{
    # Process the next element of the request queue:
    $current_path = array_shift($request);
    $current_urls = array_column($routes, ROUTES_URL);

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
                if(isset($current_url[ROUTES_SUBURLS]))
                {
                    return(route($request, $current_url[ROUTES_SUBURLS]));
                }
            }
            else
            {
                return($current_url[ROUTES_PATH]);
            }
        }
    }
    else
    {
        # Return the index page (if existing):
        if(in_array(ROUTES_INDEX_URL, $current_urls))
        {
            return($routes[array_search(ROUTES_INDEX_URL, $current_urls)][ROUTES_PATH]);
        }
    }
}

?>
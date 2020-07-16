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

    # route() needs the routes as an argument
    global $routes;

    # If we find a file, route returns true. We are saving it here so we don't have to process the infrmation again later when we include the file.
    $route = route($request, $routes);

    if(isset($route[ROUTES_PATH]))
    {
        # File found, include it:
        include(getcwd().HTDOCS_PATH."/".$route[ROUTES_PATH]);
        return 0;
    }
    else
    {
        # No file found, include the 404 file.
        include(getcwd().HTDOCS_PATH."/".PATH_TO_404);
        return 1;
    }
}

function route($request, $routes)
{
    $return_value = [
        ROUTES_PATH => null,
        ROUTES_ARGS => null,
    ];

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
                    $return_value[ROUTES_PATH] = route($request, $current_url[ROUTES_SUBURLS])[ROUTES_PATH];
                }
            }
            else
            {
                $return_value[ROUTES_PATH] = $current_url[ROUTES_PATH];
            }
        }
    }
    else
    {
        # Return the index page (if existing):
        if(in_array(ROUTES_INDEX_URL, $current_urls))
        {
            $return_value[ROUTES_PATH] = $routes[array_search(ROUTES_INDEX_URL, $current_urls)][ROUTES_PATH];
        }
    }

    return $return_value;
}

?>
<?php
# ROUTER.PHP - Routing functions

class Php_router
{
    private $routes;

    function __construct($routes)
    {
        $this->routes = $routes;
    }

    private function get_route($request, $routes)
    {
        /*
        This function handles the actual routing. We go through the routes tree and search for 
        matching urls. If suburls exist, we call ourselves recursive and repeat the process. We
        also return the further arguments if accepted by the routes.
        */

        $return_value = [
            ROUTES_PATH => null,
            ROUTES_ARGS => null,
        ];

        # Process the next element of the request queue:
        $current_request_part = array_shift($request);
        $current_route_urls = array_column($routes, ROUTES_URL);

        # Check if the current request contains something, else we need to return the index file:
        if(isset($current_request_part))
        {
            # Check if a mathing url in the routes list exists
            if(in_array($current_request_part, $current_route_urls))
            {
                $current_url = $routes[array_search($current_request_part, $current_route_urls)];
                # Check if the request contains further elements
                if(!empty($request))
                {
                    # Check if we have suburls we can process further
                    if(isset($current_url[ROUTES_SUBURLS]))
                    {
                        # Yes, we can! Now call the recursive function again:
                        $return_value = $this->get_route($request, $current_url[ROUTES_SUBURLS]);
                        

                        # The recursive function returned a * which means there are no matching routes for the request. So, the request are arguments: 
                        if($return_value[ROUTES_PATH] == "*")
                        {
                            if(isset($current_url[ROUTES_ARGS]) && $current_url[ROUTES_ARGS])
                            {
                                $return_value[ROUTES_PATH] = $current_url[ROUTES_PATH];
                                $return_value[ROUTES_ARGS] = $request;
                            }
                        }
                    }
                    else
                    {
                        # No suburls, which means they must be arguments:
                        if(isset($current_url[ROUTES_ARGS]) && $current_url[ROUTES_ARGS])
                        {
                            $return_value[ROUTES_PATH] = $current_url[ROUTES_PATH];
                            $return_value[ROUTES_ARGS] = $request;
                        }
                    }
                }
                else
                {
                    # No further elements, so we return the current page:
                    $return_value[ROUTES_PATH] = $current_url[ROUTES_PATH];
                }
            }
            else
            {
                # We have a request to process, but mo matching routes. Let's see if there is an index page that accepts arguments:
                $return_value[ROUTES_PATH] = "*";

                if(in_array("*", $current_route_urls))
                {
                    $index_key = array_search("*", $current_route_urls);
                    if(isset($routes[$index_key][ROUTES_ARGS]) && $routes[$index_key][ROUTES_ARGS])
                    {
                        # The index page accepts arguments, now we have to return them:

                        array_unshift($request, $current_request_part);
                        $return_value[ROUTES_PATH] = $routes[$index_key][ROUTES_PATH];
                        $return_value[ROUTES_ARGS] = $request;
                    }
                }
            }
        }
        else
        {
            # No request to process, return the index page (if existing):
            if(in_array(ROUTES_INDEX_URL, $current_route_urls))
            {
                $return_value[ROUTES_PATH] = $routes[array_search(ROUTES_INDEX_URL, $current_route_urls)][ROUTES_PATH];
            }
        }

        return $return_value;
    }

    public function route($request_uri = null) 
    {
        /*
        This function returns a path to a file that corresponds with the request uri. If no file is found, it returns null.
        */

        # If we didn't specify any specific request address the function should process (the default case), we just takt the request uri:
        if(!isset($request_uri))
        {
            $request_uri = $_SERVER["REQUEST_URI"];
        }

        # We use arrays to explode the long request string into smaller array values, which are better to process:
        $request_uri = explode("/", trim($request_uri, " /"));

        # To get the actual request our router should handle, we need to cut off the base directories:
        $base_directories = explode("/", trim(BASE_PATH, " /"));
        $request = array_slice($request_uri, sizeof($base_directories));

        return $this->get_route($request, $this->routes);
    }

    public function route_include($request_uri = null)
    {
        /*
        Basicly is this the route() function, but it also includes the routed file if found. If not, it includes the 404 file.
        Returns the arguments in the request if the route accepts arguments.
        */

        # Finding of the path already handles the route() function. We just call it here and save the output in the variable for further processing later.
        $route = $this->route($request_uri);

        # Does a matching file exist?
        if(isset($route[ROUTES_PATH]))
        {
            # File found, include it and, if existing, return arguments
            include(getcwd().HTDOCS_PATH."/".$route[ROUTES_PATH]);
            return isset($route[ROUTES_ARGS]) ? $route[ROUTES_ARGS] : null;
        }
        else
        {
            # No file found, include 404 document, throw 404 response and return no arguments
            http_response_code(404);
            include(getcwd().HTDOCS_PATH."/".PATH_TO_404);
            return null;
        }
    }

}



?>
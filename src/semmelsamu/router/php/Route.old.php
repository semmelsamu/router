<?php

namespace semmelsamu;

/**
 * Main Route class, routes urls and provides useful file linking functions
 *
 * @author Samuel Kroiß
 * @version 0.4
 */
class Route 
{
    /**
     * __construct
     * Route constructor
     * 
     * @param string $file path to the Route's file
     * @param array $routes Sub-Routes
     * @return void
     */
    function __construct($file, $routes = [])
    {
        $this->file = $file;
        $this->routes = $routes;

        $this->url = substr($_SERVER["REQUEST_URI"], strrpos($_SERVER['PHP_SELF'], "/")+1);
        $this->url = strpos($this->url, "?") ? substr($this->url, 0, strpos($this->url, "?")) : $this->url;
    }

    /**
     * route
     * Includes the corresponding file
     * 
     * @return array Further arguments
     */
    function route() 
    {
        // If the url directs to a file, we output the file:
        // We don't allow the execution of PHP scripts when the file itself is accessed
        if(file_exists($this->url) && substr($this->url, -4) != ".php") {

            // It's a file, so we output the file:

            $mime_type = get_mime_type($this->url);

            if($mime_type == "image/jpeg" && function_exists("\semmelsamu\jpegscaled")) jpegscaled($this->url);

            header("Content-Type: ".$mime_type);
            readfile($this->url);

            exit;
        }

        // TODO: 404 if sizeof array > 0
        return $this->route_inner();
    }

    /**
     * route_inner
     * Actual routing business.
     * 
     * @param array $routes Routes to work off
     * @return array Further arguments
     */
    function route_inner($routes = null)
    {
        // If we are the first route, create route array to work on.
        if(!isset($routes)) 
        {
            $routes = array_filter(explode("/", $this->url));
        }

        // No more routes to check, we are at our goal:
        if(sizeof($routes) == 0) 
        {
            include($this->file);
            return [];
        }

        // We have routes which correspind to the url, let them handle the request further:
        if(array_key_exists($routes[0], $this->routes))
        {
            $next_route = array_shift($routes);
            return $this->routes[$next_route]->route_inner($routes);
        }
       
        // No mathing routes. We stay at the furthest pont we know and return further parts of the url as arguments.
        include($this->file);
        return $routes;
    }

    /**
     * base
     * Returns the relative path to the root directory. Intended for HTML <base> tag.
     * 
     * @return string Relative path to root directory
     */
    function base()
    {
        return str_repeat("../", substr_count($this->url, "/"));
    }

    /**
     * to
     * Returns the relative path to the route $id.
     * 
     * @param string $id The route the relative part should go to
     * @return string Relative path to the route
     */
    function to($id) 
    {
        $path = "";
        return $path;
    }
}

?>
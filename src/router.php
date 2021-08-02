<?php

namespace semmelsamu;

define("HTTPS", 0x1);
define("NO_WWW", 0x2);
define("NO_TRAILING_SLASHES", 0x4);

include("route.php");

/**
 * Router
 * @author semmelsamu
 */
class Router
{
    function __construct()
    {
        $this->url = $this->url();

        $this->routes = [];
        $this->route_403 = null;
    }

    /**
     * Add one or multiple Routes to the Router
     * @param array $routes the Route(s) to add
     * @return null
     */
    function add($routes) 
    {
        foreach($routes as $route)
        {
            if(is_a($route, '\semmelsamu\Route'))
                array_push($this->routes, $route);
        }
    }

    // Main Route

    /**
     * Main routing function
     */
    function route()
    {
        $this->result = $this->route_inner($id);

        if(!isset($this->result))
            $this->result = $this->route_403;

        return $this->result;
    }

    private function route_inner($id = null)
    {
        $result = null;

        // Loop through all routes and check if the url corresponds to any
        foreach($this->routes as $route)
        {
            if($route->route($this->url) || (isset($id) && $route->id == $id))
            {
                $result = $route;
                break;
            }
        }

        // Check for goto
        if(isset($result) && $result->goto)
        {
            $result = $this->route($result->goto);
        }

        if(!isset($result))
        {
            if(is_file($this->url))
            {
                $result = new Route($this->url, $this->url);
            }
        }

        return $result;
    }

    /**
     * Route (if not yet done) and output the routed file
     */
    function output()
    {
        if(!isset($this->result))
            $this->route();

        if(substr($this->result->file, -4) == ".php")
        {
            include($this->htdocs_folder.$this->result->file);
        }
        else
        {
            $this->output_file($this->result->file);
        }
    }

    // URL managing functions

    private function url()
    {
        // Getting the Relative path from the root directory
        $url = substr(urldecode($_SERVER["REQUEST_URI"]), strrpos($_SERVER['PHP_SELF'], "/")+1);

        // Parts of the PHP arguments (everything afther the "?" and the "?" itself are not part of the url)
        $url = strpos($url, "?") ? substr($url, 0, strpos($url, "?")) : $url;

        // No trailing slashes
        if(substr($url, -1) == "/")
            $url = substr($url, 0, -1);

        return $url;
    }
    
    /**
     * Return the relative path to the base/root directory
     * @return string the relative path to the base/root directory
     */
    function base()
    {
        $result = str_repeat("../", substr_count($this->url, "/")+1);
        return $result == "" ? "./" : $result;
    }

    /**
     * Return the relative path to the route with the id $id
     * Returns NULL if the Route's url is a regular expression
     * @param int|string the id of the route
     * @return string|null relative path to the route or NULL if the Route's url is a regular expression
     */
    function id($id)
    {
        foreach($this->routes as $route)
        {
            if($route->id == $id)
            {
                if(!$route->url_is_regex)
                {
                    return $route->url;
                }
                else
                {
                    return null;
                }
            }
        }
    }
        
    /**
     * Output a file to the user and end the script
     * @param string $file path to the file
     * @return void
     */
    private function output_file($file) 
    {
        if(!file_exists($file)) return;


        // Return mime type ala mimetype extension
        switch (substr($file, strrpos($file, ".")+1)) {
            case "css": $mime_type = "text/css"; break;
            case "js": $mime_type = "text/javascript"; break;
            default: $mime_type = mime_content_type($file); break;
        }

        header("Content-Type: ".$mime_type);
        readfile($file);
        exit;
    }
}
?>
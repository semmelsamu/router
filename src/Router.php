<?php

namespace semmelsamu;

/**
 * Router
 * @author semmelsamu
 */
class Router
{
    public $htdocs_folder, $error_document;
    private $routes, $result;

    /**
     * Class constructor
     * 
     * @param string $htdocs_folder the folder, i.e. the prefix for all files
     * @param string $error_document path to the 404 document
     * 
     * @return null
     */
    function __construct(
        $htdocs_folder = "htdocs/", 
        $error_document = "404.php"
    )
    {
        $this->htdocs_folder = $htdocs_folder;
        $this->error_document = $error_document;
        $this->routes = [];
    }

    // Getter & Setter

    public function __get($property) {
        if (property_exists($this, $property)) {
          return $this->$property;
        }
    }

    /**
     * Add one or multiple Routes to the Router
     * @param Route ...$routes the routes
     * @return null
     */
    function add(...$routes) 
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
     * @param bool $include specifies if the route should be included
     * @return Route the route corresponding to the url
     */
    function route($include = true)
    {
        $this->result = $this->route_inner();

        if($include)
        {
            if(isset($this->result))
                include($this->htdocs_folder.$this->result->file);

            else
                include($this->htdocs_folder.$this->error_document);
        }
        
        return $this->result;
    }

    private function route_inner($id = null)
    {
        // Loop through all routes and check if the url corresponds to any
        foreach($this->routes as $route)
        {
            if((isset($id) && $route->id == $id) || $route->route($this->url()))
            {
                $result = $route;
                break;
            }
        }

        // Found a corresponding route. Return it, or, if goto is specified, run function again recursively
        if(isset($result))
        {
            if($result->goto)
            {
                return $this->route_inner($result->goto);
            }
            else 
            {
                return $result;
            }
        }
    }

    // URL managing functions

    /**
     * Return the relative URL from the router root directory, without the PHP parameters
     * @return string the relative URL from the router root directory, without the PHP parameters
     */
    function url($trailing_slashes = false)
    {
        // Getting the Relative path from the root directory
        $url = substr(urldecode($_SERVER["REQUEST_URI"]), strrpos($_SERVER['PHP_SELF'], "/")+1);

        // Parts of the PHP arguments (everything afther the "?" and the "?" itself are not part of the url)
        $url = strpos($url, "?") ? substr($url, 0, strpos($url, "?")) : $url;

        // No trailing slashes
        if(substr($url, -1) == "/" && !$trailing_slashes)
            $url = substr($url, 0, -1);

        return $url;
    }
    
    /**
     * Return the relative path to the base/root directory
     * @return string the relative path to the base/root directory
     */
    function base()
    {
        $result = str_repeat("../", substr_count($this->url(true), "/"));
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
}
?>
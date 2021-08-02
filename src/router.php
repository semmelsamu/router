<?php

namespace semmelsamu;

class Router
{
    function __construct()
    {
        $this->url = $this->url();

        $this->routes = [];
        $this->route_403 = null;
    }

    function add(
        $url,
        $callback,
        $methods = ["get"], 
        $id = null,
        $tags = [],
    )
    {
        $route = [
            "methods" => $methods,
            "url" => $url,
            "callback" => $callback,
            "tags" => $tags,
        ];

        if(isset($id))
        {
            $this->routes[$id] = $route;
        }
        else
        {
            array_push($this->routes, $route);
        }
    }

    function add_404($callback)
    {
        $this->callback_404 = $callback;
    }

    // Main Route

    /**
     * Main routing function
     */
    function route()
    {
        foreach($this->routes as $route)
        {
            if(
                (   // For regex URL
                    strlen($route["url"]) > 1 && substr($route["url"], 0, 1) == "/" && substr($route["url"], -1) == "/") && 
                    preg_match($route["url"], $this->url, $this->matches
                ) ||
                // For non-regex URL
                $this->url == $route["url"]
            ) 
            {
                $result = $route;

                if(is_callable($result["callback"]))
                {
                    $result["callback"]();
                }
                break;
            }
        }

        if(!isset($result))
        {
            if(isset($this->callback_404) && is_callable($this->callback_404))
            {
                call_user_func($this->callback_404);
            }
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
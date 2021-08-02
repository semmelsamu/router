<?php

namespace semmelsamu;

class Router
{
    function __construct()
    {
        $this->url = trim(substr($_SERVER["REQUEST_URI"], strlen(dirname($_SERVER["PHP_SELF"]))), "/");
        $this->matches = [];

        $this->routes = [];
        $this->route_403 = null;
    }

    function add(
        $callback,
        $url = "/^(.*)$/",
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

    function route()
    {
        foreach($this->routes as $route)
        {
            if(
                // For regex URL
                (strlen($route["url"]) > 1 && substr($route["url"], 0, 1) == "/" && substr($route["url"], -1) == "/") && preg_match($route["url"], $this->url, $this->matches) ||
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
    
    /**
     * Return the relative path to the base/root directory
     * @return string the relative path to the base/root directory
     */
    function base()
    {
        $result = str_repeat("../", substr_count($this->url, "/")+1);
        return $result == "" ? "./" : $result;
    }

    function id($id)
    {

    }
}
?>
<?php

namespace semmelsamu;

class Router
{
    function __construct()
    {
        $this->url = trim(substr($_SERVER["REQUEST_URI"], strlen(dirname($_SERVER["PHP_SELF"]))), "/");

        $this->base = str_repeat("../", substr_count(substr($_SERVER["REQUEST_URI"], strlen(dirname($_SERVER["PHP_SELF"]))), "/")-1);
        $this->base = $this->base == "" ? "./" : $this->base;
        
        $this->matches = [];

        $this->routes = [];
        $this->route_403 = null;
    }

    function add(
        $callback,
        $url = "/.*/",
        $methods = true, 
        $id = null,
        $tags = []
    )
    {
        $route = [
            "methods" => is_array($methods) ? array_map("strtolower", $methods) : true,
            "url" => $url,
            "is_regex" => strlen($url) > 1 && substr($url, 0, 1) == "/" && substr($url, -1) == "/",
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
            if(is_array($route["methods"]) && !in_array(strtolower($_SERVER["REQUEST_METHOD"]), $route["methods"])) continue;

            if(
                // For regex URL
                ($route["is_regex"] && preg_match($route["url"], $this->url, $this->matches)) ||
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
                http_response_code(404);
                call_user_func($this->callback_404);
            }
        }
    }

    function id($id)
    {
        if(array_key_exists($id, $this->routes) && !$this->routes[$id]["is_regex"]) 
            return $this->routes[$id]["url"];
    }
}
?>
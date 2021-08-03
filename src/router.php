<?php

namespace semmelsamu;

class Router
{
    function __construct()
    {
        $this->url = trim(substr(urldecode(parse_url($_SERVER["REQUEST_URI"])["path"]), strlen(dirname($_SERVER["PHP_SELF"]))), "/");

        $this->base = str_repeat("../", substr_count(substr($_SERVER["REQUEST_URI"], strlen(dirname($_SERVER["PHP_SELF"]))), "/")-1);
        $this->base = $this->base == "" ? "./" : $this->base;
        
        $this->matches = [];

        $this->routes = [];
        $this->route_403 = null;
    }

    function add(
        $url = "",
        $callback = null,
        $methods = true, 
        $id = null,
        $tags = []
    )
    {
        $route = [
            "methods" => is_array($methods) ? array_map("strtolower", $methods) : true,
            "url" => $url,
            "is_regex" => preg_match("/^\/.+\/[a-z]*$/i", $url),
            "callback" => $callback,
            "tags" => $tags
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

    function call_404()
    {
        http_response_code(404);
        $this->call($this->callback_404);
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
                if($this->call($route["callback"]) === false)
                {
                    $this->call_404();
                }
                break;
            }
        }
    }

    function call($callback)
    {
        if(is_callable($callback))
        {
            call_user_func($callback);
        }
        else if(file_exists($callback))
        {
            if(substr($callback, -4) == ".php")
                include($callback);
            else
                $this->output_file($callback);
        }
    }

    function id($id)
    {
        if(array_key_exists($id, $this->routes) && !$this->routes[$id]["is_regex"]) 
            return $this->routes[$id]["url"];
    }

    function output_file($file) 
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
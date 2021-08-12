<?php

namespace semmelsamu;

class Router
{
    function __construct()
    {
        $this->url = trim(substr(urldecode(parse_url($_SERVER["REQUEST_URI"])["path"]), strlen(dirname($_SERVER["PHP_SELF"]))), "/");

        $times = substr_count(substr($_SERVER["REQUEST_URI"], strlen(dirname($_SERVER["PHP_SELF"]))), "/")-1;
        $times = $times > 0 ? $times : 0;
        $this->base = str_repeat("../", $times);
        $this->base = $this->base == "" ? "./" : $this->base;
        
        $this->matches = [];
        $this->routes = [];
        $this->callback_404 = null;
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
            "callback" => $callback,
            "tags" => $tags
        ];

        if(isset($id))
            $this->routes[$id] = $route;

        else
            array_push($this->routes, $route);
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

            if(is_array($route["methods"]) && !in_array(strtolower($_SERVER["REQUEST_METHOD"]), $route["methods"])) 
                continue;

            $error = false;
            $this->matches = [];

            if(preg_match("/^\/.+\/[a-z]*$/i", $route["url"]))
            {
                if(!preg_match($route["url"], $this->url, $this->matches))
                    $error = true;
            }
            else
            {
                $url_parts = explode("/", $this->url);
                $route_parts = explode("/", $route["url"]);

                if(sizeof($url_parts) > sizeof($route_parts))
                {
                    $error = true;
                }
                else
                {
                    $last_required_route_part = 0;

                    foreach($route_parts as $i => $part)
                    {
                        if(!preg_match("/^<(.+)>$/", $part))
                        {
                            $last_required_route_part = $i;
                        }
                    }

                    foreach($route_parts as $i => $part)
                    {
                        if(preg_match("/^<(.+)>$/", $part, $match_name))
                        {
                            if(isset($url_parts[$i]))
                                $this->matches[$match_name[1]] = $url_parts[$i];
                            
                            else
                            {
                                if($i > $last_required_route_part)
                                    $this->matches[$match_name[1]] = "";

                                else
                                {
                                    $error = true;
                                    break;
                                }
                            }
                        }
                        else
                        {
                            if(isset($url_parts[$i]) && $part == $url_parts[$i])
                                continue;

                            else
                            {
                                $error = true;
                                break;
                            }
                        }
                    }
                }
            }

            if(!$error)
            {
                $this->call($route["callback"]);
                break;
            }
        }

        if(isset($error) && $error)
        {
            $this->matches = [];
            $this->call_404();
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
        if(array_key_exists($id, $this->routes) && !preg_match("/^\/.+\/[a-z]*$/i", $this->routes[$id]["url"]))
        {
            $url = "";

            foreach(explode("/", $this->routes[$id]["url"]) as $part)
            {
                if(!preg_match("/^<(.+)>$/", $part))
                {
                    $url .= $part;
                }
                else
                    break;
            }

            return $url;
        }
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
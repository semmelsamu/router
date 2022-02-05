<?php

declare(strict_types=1);

namespace semmelsamu;

class Router
{
    function __construct()
    {
        $this->url = trim(substr(urldecode(parse_url($_SERVER["REQUEST_URI"])["path"]), strlen(dirname($_SERVER["PHP_SELF"]))), "/");

        
        // Calculate base

        $url_without_base = substr($_SERVER["REQUEST_URI"], strlen(dirname($_SERVER["PHP_SELF"])));
        $url_has_trailing_slash = substr($_SERVER["REQUEST_URI"], strrpos($_SERVER["REQUEST_URI"], "?")-1, 1) == "/";

        $times = sizeof(array_filter(explode("/", $url_without_base)))-1;

        if($url_has_trailing_slash)
            $times++;

        if($times < 1)
            $this->base = "./";
        else
            $this->base = str_repeat("../", $times);


        // Initialize Variables
        
        $this->matches = [];
        $this->routes = [];
        $this->callback_404 = null;
    }

    function add(
        string $url = "",
        string|callable $callback = null,
        bool|array $methods = true, 
        int $id = null,
        array $tags = []
    ): void
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

    function add_404(string|callable $callback): void
    {
        $this->callback_404 = $callback;
    }

    function call_404(): string
    {
        http_response_code(404);
        return $this->call($this->callback_404);
    }

    function route(): string
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
                return $this->call($route["callback"]);
            }
        }

        $this->matches = [];
        return $this->call_404();
        
    }

    function call(string|callable|null $callback): string
    {
        ob_start();

        if(!empty($callback))
        {
            if(is_callable($callback))
            {
                call_user_func($callback, $this->matches);
            }
            else if(file_exists($callback))
            {
                if(substr($callback, -4) == ".php")
                    include($callback);
                else
                    $this->output_file($callback);
            }
        }

        return ob_get_clean();
    }

    function id(int $id): string
    {
        if(array_key_exists($id, $this->routes) && !preg_match("/^\/.+\/[a-z]*$/i", $this->routes[$id]["url"]))
        {
            $url = "";

            foreach(explode("/", $this->routes[$id]["url"]) as $part)
            {
                if(!preg_match("/^<(.+)>$/", $part))
                {
                    $url .= $part."/";
                }
                else
                    break;
            }

            return $url;
        }
    }

    function output_file(string $file) 
    {
        if(!file_exists($file)) return;

        // Return mime type ala mimetype extension
        $mime_type = match(substr($file, strrpos($file, ".")+1)) {
            "css" => "text/css",
            "js" => "text/javascript",
            default => mime_content_type($file)
        };

        header("Content-Type: ".$mime_type);
        readfile($file);
        exit;
    }
}
<?php

declare(strict_types=1);

namespace semmelsamu;

class Router
{
    protected $url = "/";
    protected $routes = array();
    
    protected $base;
    protected $callback_404;
    
    function __construct()
    {
        $this->url = $_SERVER["REQUEST_URI"];
        
        if(substr($this->url, 0, 1) == "/")
            $this->url = substr($this->url, 1);
    }
    
    function base(): string 
    {
        if(!$this->base)
            $this->base = str_repeat("../", substr_count($url, "/"));
            
        return $this->base;
    }

    function add(
        string $url,
        string|callable $callback,
        bool|array $methods = true,
        int|string $id = null,
        array $tags = []
    ): void
    {
        $route_to_add = array(
            "methods" => is_array($methods) ? array_map("strtolower", $methods) : true,
            "url" => $url,
            "callback" => $callback,
            "tags" => $tags
        );

        if(isset($id))
            $this->routes[$id] = $route_to_add;

        else
            array_push($this->routes, $route_to_add);
    }

    function set_404(string|callable $callback): void
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
            $error = false;
            
            if(is_array($route["methods"]) && !in_array(strtolower($_SERVER["REQUEST_METHOD"]), strtolower($route["methods"]))) 
                continue;

            $this->matches = array();
            
            # Check if the url of the route is a regular expression (starts and ends with a forward slash "/")
            if(substr($route["url"], 0, 1) == "/" && substr($route["url"], -1) == "/")
            {
                if(!preg_match($route["url"], $this->url, $this->matches))
                    continue;
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

    function id(int|string $id): ?string
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

        return null;
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


    function sitemap($subdomain = "www.", $trailing_slashes = true)
    {
        // Controller

        $base = (isset($_SERVER["HTTPS"]) ? "https://" : "http://") . $subdomain . $_SERVER['SERVER_NAME'] . "/";

        $routes_to_render = [];

        foreach($this->routes as $route)
        {
            if(
                !preg_match("/^\/.+\/[a-z]*$/i", $route["url"]) && 
                !in_array("hidden", $route["tags"]) &&
                !preg_match(('/.*<.*>.*/'), $route["url"])
            )
            {
                if ($trailing_slashes) {
                    // Trailing slashes can't be added to files
                    if (!file_exists($_SERVER["DOCUMENT_ROOT"] . $route["url"])) {
                        if (substr($route["url"], -1) != "/") {
                            $route["url"] = $route["url"]."/";
                        }
                    }
                }
                array_push($routes_to_render, $base.$route["url"]);
            }
        }

        // View

        header('Content-Type: text/xml');

        echo '<?xml version="1.0" encoding="UTF-8"?>';

        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach($routes_to_render as $loc):
            echo "<url><loc>$loc</loc></url>";
        endforeach;

        echo '</urlset>';

        exit;
    }
}

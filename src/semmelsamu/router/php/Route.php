<?php

namespace semmelsamu;

class Route 
{
    function __construct($file, $routes = [])
    {

        $this->file = $file;
        $this->routes = $routes;

        $this->url = substr($_SERVER["REQUEST_URI"], strrpos($_SERVER['PHP_SELF'], "/")+1);
        $this->url = strpos($this->url, "?") ? substr($this->url, 0, strpos($this->url, "?")) : $this->url;
    }

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

        return $this->route_inner();
    }

    // Actual routing business.
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

    function base()
    {
        return str_repeat("../", substr_count($this->url, "/"));
    }

    function to($id) 
    {
        $path = "";
        return $path;
    }
}








/*

    function __construct($params = []) {

        $default_params = [
            "file" => null,
            "id" => null,
            "accept_args" => false,
            "routes" => [],
            "visible" => true,
        ];

        $params = array_replace($default_params, $params);

        foreach($default_params as $key => $val) {
            $this->$key = array_key_exists($key, $params) ? $params[$key] : $val;
        }
    }

    function get_from_request($request) {

        $this->args = [];

        if(sizeof($request) > 0) {
            if(sizeof($this->routes) > 0 && array_key_exists($request[0], $this->routes)) {
                $current_request = array_shift($request);
                return $this->routes[$current_request]->get_from_request($request);
            }
            else {
                if($this->accept_args) {
                    $this->args = $request;
                    return $this;
                }
            }
        }
        else {
            return $this;
        }

    }

    function get_uri_from_id($id) {
        if($id == $this->id) {
            return ".";
        }
        else {
            foreach($this->routes as $key => $route) {
                $result = $route->get_uri_from_id($id);
                if(isset($result)) {
                    return $key."/".$result;
                }
            }
        }
    }

    function get_all_routes() {
        $result = array();
        foreach($this->routes as $route => $value) {
            if($value->visible) {
                array_push($result, $route);
                $sub_routes = $value->get_all_routes();
                foreach($sub_routes as $sub_route) {
                    array_push($result, $route."/".$sub_route);
                }
            }
        }
        return $result;
    }
}


*/

?>
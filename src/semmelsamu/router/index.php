<?php

    class Route {

        function __construct($params = []) {

            $default_params = [
                "file" => null,
                "id" => null,
                "args" => false,
                "routes" => [],
            ];

            $params = array_replace($default_params, $params);

            $this->file = $params["file"];
            $this->id = $params["id"];
            $this->args = $params["args"];
            $this->routes = $params["routes"];
        }

        function get_path_list($uri) {
            return array_map("strtolower", array_filter(explode("/", $uri)));
        }

        function get_request() {
            return trim(substr($_SERVER['REQUEST_URI'], strlen(substr(getcwd(), strlen($_SERVER["DOCUMENT_ROOT"])))), " /\\");
        }

        function route($request = null, $auto_include = true) {

            global $args;
            $args = [];

            if(!isset($request)) {
                $request = $this->get_path_list($this->get_request());
            }

            if(sizeof($request) > 0) {
                if(sizeof($this->routes) > 0 && array_key_exists($request[0], $this->routes)) {
                    $current_request = array_shift($request);
                    return $this->routes[$current_request]->route($request);
                }
                else {
                    if($this->args) {
                        $args = $request;
                        return $this->file;
                    }
                }
            }
            else {
                return $this->file;
            }

            return null;
        }

        function get_relative_path($to) {
            $from = $this->get_path_list($this->get_request());
            if(!is_array($to)) {
                $to = $this->get_path_list($to);
            }

            if(sizeof($from) > 0 && sizeof($to) > 0)
            {
                while($from[0] == $to[0] && sizeof($from) > 0 && sizeof($to) > 0) {
                    array_shift($from);
                    array_shift($to);
                }
            }

            return str_repeat("../", sizeof($from)).implode("/", $to);
        }

        function get_uri($id) {
            if($this->id == $id) {
                return true;
            }
            else {
                foreach($this->routes as $path => $route) {
                    $result = $route->get_uri($id);
                    if($result === true) {
                        return $path;
                    }
                    elseif($result) {
                        return $path."/".$result;
                    }
                }
            }
            return null;
        }
    }

?>
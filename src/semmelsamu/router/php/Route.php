<?php

    class Route {

        function __construct($params = []) {

            $default_params = [
                "file" => null,
                "id" => null,
                "accept_args" => false,
                "routes" => [],
            ];

            $params = array_replace($default_params, $params);

            foreach  ($default_params as $key => $val) {
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
    }

?>
<?php

    class Route {

        function __construct($params = []) {

            global $args;
            $args = [];
            
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

        function get_request() {
            return array_map(
                "strtolower",
                array_filter(
                    explode(
                        "/", 
                        trim(
                            substr(
                                $_SERVER['REQUEST_URI'], 
                                strlen(
                                    substr(
                                        getcwd(), 
                                        strlen($_SERVER["DOCUMENT_ROOT"])
                                    )
                                )
                            ), 
                            " /\\"
                        )
                    )
                )
            );
        }

        function route($request = null) {
            if(!isset($request)) {
                $request = $this->get_request();
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

        function get_file($id) {
            if($this->id == $id) {
                return $this->file;
            }
            else {
                foreach($this->routes as $key => $route) {
                    $result = $route->get_file($id);
                    if($result) {
                        return $result;
                    }
                }
            }
            return null;
        }
    }

?>
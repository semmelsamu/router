<?php

    class Route {

        function __construct() {
            
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

            return false;
        }
    }

?>
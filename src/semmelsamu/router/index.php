<?php

    # Don't mind me, i just convert the request uri to an array.
    $request = array_map(
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

    echo "Request: ";
    var_dump($request);
    echo "<br><br>";

    if(!isset($routes)) {
        $routes = [];
    }

    $routes = array_change_key_case($routes);

    if(!$routes) {
        $routes = [];
    }

    $routes = array_filter($routes);

    echo "Routes: ";
    var_dump($routes);
    echo "<br><br>";

    function route($request, $routes)
    {
        if(sizeof($request) > 0) {
            if(sizeof($routes) > 0) {
                if(array_key_exists($request[0], $routes)) {
                    if(isset($routes[$request[0]]["routes"])) {
                        if(sizeof($request) > 1) {
                            $new_routes = $routes[$request[0]]["routes"];
                            array_shift($request);
                            return route($request, $new_routes);
                        }
                        else {
                            return $routes[$request[0]]["file"];
                        }
                    }
                    else {
                        return $routes[$request[0]]["file"];
                    }
                }
            }
        }
        else {
            if(array_key_exists("*", $routes)) {
                return $routes["*"]["file"];
            }
        }
        return false;
    }

    echo "Result: ".route($request, $routes);
?>
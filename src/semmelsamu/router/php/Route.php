<?php

namespace semmelsamu;

/**
 * Route
 *
 * @author Samuel Kroiß
 * @version 0.4
 */
class Route 
{
    public $file, $id, $accept_arguments, $visible, $routes, $goto;

    function __construct($values)
    {
        $default_values = [
            "file" => "index.php", // the file the route should include
            "id" => 0, // the unique id of the route
            "accept_arguments" => false, // if further parts of the url are given, still use this route and get the parts
            "visible" => true, // should be included in the sitemap?
            "routes" => [], // further sub-routes
            "goto" => false // url which has the same route with the id
        ];

        $values = array_merge($default_values, $values);

        $this->file = $values["file"];
        $this->id = $values["id"];
        $this->accept_arguments = $values["accept_arguments"];
        $this->visible = $values["visible"];
        $this->routes = $values["routes"];
        $this->goto = $values["goto"];

        foreach($this->routes as $key => $route_values)
        {
            $this->routes[$key] = new Route($route_values);
        }
        
    }

    /**
     * route
     * Actual routing business.
     * 
     * @param array $routes Routes to work off
     * @return array containing the file and further arguments. If no route was found, the function will return nothing.
     */
    function route($routes)
    {
        // No more routes to check, we are at our goal:
        if(empty($routes)) 
        {
            return ["file" => $this->file];
        }

        // We have a route which corresponds to the url part, let it handle the request further:
        if(array_key_exists($routes[0], $this->routes))
        {
            $next_route = array_shift($routes);

            // If it is a goto route, we return it to the parent class:
            if($this->routes[$next_route]->goto)
            {
                return ["id" => $this->routes[$next_route]->goto];
            }
            return $this->routes[$next_route]->route($routes);
        }

        // No mathing routes. We stay at the furthest pont we know and if wanted we return further parts of the url as arguments.
        if($this->accept_arguments)
        {
            return ["file" => $this->file, "args" => $routes];
        }
    }

    /**
     * id
     * Returns the url to the Route with the id $id
     * 
     * @param string $id The id to which the url should go to
     * @return string Url to the Route
     */
    function id($id)
    {
        if($id == $this->id)
        {
            return "";
        }
        
        foreach($this->routes as $key => $route)
        {
            $result = $route->id($id);
            if($result === "" or $result)
            {
                return $key."/".$result;
            }
            
        }

        return false;
    }

    function sitemap($base, $prefix = "")
    {
        if($this->visible)
            secho "\t<url><loc>$base$prefix</loc></url>\n";

        foreach($this->routes as $key => $route)
        {
            $route->sitemap($base, $prefix.$key."/");
        }
    }
}

?>
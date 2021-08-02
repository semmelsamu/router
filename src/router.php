<?php

namespace semmelsamu;

define("HTTPS", 0x1);
define("NO_WWW", 0x2);
define("NO_TRAILING_SLASHES", 0x4);

include("route.php");

/**
 * Router
 * @author semmelsamu
 */
class Router
{
    public $htdocs_folder, $error_document, $flags;
    private $routes, $result, $mime_types;

    /**
     * Class constructor
     * 
     * @param string $htdocs_folder the folder where all your htdocs are
     * @param string $error_document path to the 404 document
     * @param flags HTTPS, NO_WWW, NO_TRAILING_SLASHES
     * 
     * @return null
     */
    function __construct(
        $htdocs_folder = "htdocs/", 
        $error_document = "404.php",
        $beautify_url = HTTPS | NO_WWW | NO_TRAILING_SLASHES
    )
    {
        // Import all parameters
        foreach(get_defined_vars() as $key => $val)
            $this->$key = $val;

        if($beautify_url !== false)
        {
            $this->beautify_url($beautify_url);
        }
        
        $this->routes = [];
    }

    // Getter & Setter

    public function __get($property) {
        if (property_exists($this, $property)) {
          return $this->$property;
        }
    }

    /**
     * Add one or multiple Routes to the Router
     * @param array $routes the Route(s) to add
     * @return null
     */
    function add($routes) 
    {
        foreach($routes as $route)
        {
            if(is_a($route, '\semmelsamu\Route'))
                array_push($this->routes, $route);
        }
    }

    // Main Route

    /**
     * Main routing function
     */
    function route($id = null)
    {
        $this->result = $this->route_inner($id);

        if(!isset($this->result))
            $this->result = new Route(null, $this->error_document);

        return $this->result;
    }

    private function route_inner($id = null)
    {
        $result = null;

        // Loop through all routes and check if the url corresponds to any
        foreach($this->routes as $route)
        {
            if($route->route($this->url()) || (isset($id) && $route->id == $id))
            {
                $result = $route;
                break;
            }
        }

        // Check for goto
        if(isset($result) && $result->goto)
        {
            $result = $this->route($result->goto);
        }

        if(!isset($result))
        {
            if(is_file($this->url()))
            {
                $result = new Route($this->url(), $this->url());
            }
        }

        return $result;
    }

    /**
     * Route (if not yet done) and output the routed file
     */
    function output()
    {
        if(!isset($this->result))
            $this->route();

        if(substr($this->result->file, -4) == ".php")
        {
            include($this->htdocs_folder.$this->result->file);
        }
        else
        {
            $this->output_file($this->result->file);
        }
    }

    // URL managing functions

    private function beautify_url($flags)
    {
        $redirect = false;

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER["HTTP_HOST"];
        $uri = $_SERVER["REQUEST_URI"];

        if($flags & HTTPS)
        {
            if($protocol == "http://")
            {
                $redirect = true;
                $protocol = "https://";
            }
        }

        if($flags & NO_WWW)
        {
            if(substr($host, 0, 4) == "www.")
            {
                $redirect = true;
                $host = substr($host, 4);
            }
        }

        if($flags & NO_TRAILING_SLASHES)
        {
            // Trailing slashes can't be removed from directories or the website root
            if(!is_dir($_SERVER["DOCUMENT_ROOT"].$uri) && $uri != "/")
            {
                if(substr($uri, -1) == "/")
                {
                    $redirect = true;
                    $uri = substr($uri, 0, -1);
                }
            }
        }

        if($redirect)
        {
            $location = $protocol . $host . $uri;

            header("Link: <$location>; rel=\"canonical\"");
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: $location");
            
            exit;
        }
    }

    /**
     * Return the relative URL from the router root directory, without the PHP parameters
     * @return string the relative URL from the router root directory, without the PHP parameters
     */
    function url($trailing_slashes = false)
    {
        // Getting the Relative path from the root directory
        $url = substr(urldecode($_SERVER["REQUEST_URI"]), strrpos($_SERVER['PHP_SELF'], "/")+1);

        // Parts of the PHP arguments (everything afther the "?" and the "?" itself are not part of the url)
        $url = strpos($url, "?") ? substr($url, 0, strpos($url, "?")) : $url;

        // No trailing slashes
        if(substr($url, -1) == "/" && !$trailing_slashes)
            $url = substr($url, 0, -1);

        return $url;
    }
    
    /**
     * Return the relative path to the base/root directory
     * @return string the relative path to the base/root directory
     */
    function base()
    {
        $result = str_repeat("../", substr_count($this->url(true), "/"));
        return $result == "" ? "./" : $result;
    }

    /**
     * Return the relative path to the route with the id $id
     * Returns NULL if the Route's url is a regular expression
     * @param int|string the id of the route
     * @return string|null relative path to the route or NULL if the Route's url is a regular expression
     */
    function id($id)
    {
        foreach($this->routes as $route)
        {
            if($route->id == $id)
            {
                if(!$route->url_is_regex)
                {
                    return $route->url;
                }
                else
                {
                    return null;
                }
            }
        }
    }
        
    /**
     * Output a file to the user and end the script
     * @param string $file path to the file
     * @return void
     */
    private function output_file($file) 
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
?>
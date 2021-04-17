<?php

namespace semmelsamu;

class Router
{
    private $htdocs_folder, $error_document, $enable_sitemap, $routes;

    function __construct(
        $htdocs_folder = "htdocs/", 
        $error_document = "404.php",
        $enable_sitemap = true,
    )
    {
        $this->htdocs_folder = $htdocs_folder;
        $this->error_document = $error_document;
        $this->enable_sitemap = $enable_sitemap;
        $this->routes = [];
    }

    // Getter & Setter

    /**
     * add
     * adds one or multiple Routes to the Router.
     * @param Route ...$routes The routes.
     * @return null
     */
    function add(...$routes) 
    {
        foreach($routes as $route)
        {
            if(is_a($route, '\semmelsamu\Route'))
                array_push($this->routes, $route);
        }
    }

    // Main Route

    function route()
    {
        $url = $this->url();

        $result = false;

        foreach($this->routes as $route)
        {
            if($route->route($url))
            {
                $result = $route;
                break;
            }
        }

        if($result)
        {
            db($result);
        }
    }

    // URL managing functions

    /**
     * url
     * Returns the relative URL from the router root directory, without the PHP parameters
     * 
     * @return string the relative URL from the router root directory, without the PHP parameters
     */
    function url()
    {
        // Getting the Relative path from the root directory
        $url = substr(urldecode($_SERVER["REQUEST_URI"]), strrpos($_SERVER['PHP_SELF'], "/")+1);

        // Parts of the PHP arguments (everything afther the "?" and the "?" itself are not part of the url)
        $url = strpos($url, "?") ? substr($url, 0, strpos($url, "?")) : $url;

        return $url;
    }
    
    /**
     * base
     * Returns the relative path to the base/root directory
     * 
     * @return string Relative path to the base/root directory
     */
    function base()
    {
        return str_repeat("../", substr_count($this->url, "/"));
    }

    // Output functions

    /**
     * output_file
     * Outputs a file to the user and ends the script.
     * 
     * @param string $file path to the file
     * @return void
     */
    private function output_file($file) 
    {
        $mime_type = get_mime_type($file);

        if($this->file_modifiers) {

            // Enter file modifiers:
            if($mime_type == "image/jpeg" && function_exists("\semmelsamu\jpegscaled")) jpegscaled($file);

        }

        header("Content-Type: ".$mime_type);
        readfile($this->url());

        exit;
    }

    /**
     * sitemap
     * Print a basic sitemap of all visible sites mentioned in the route tree and terminate the script
     * 
     * @return void
     */
    function sitemap()
    {
        header('Content-Type: text/xml');

        $base = substr($_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"], 0, -11);

        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        // $this->index_route->sitemap($base);

        echo '</urlset>';

        exit;
    }
}
?>
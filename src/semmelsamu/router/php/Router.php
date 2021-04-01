<?php

namespace semmelsamu;

/**
 * Router
 * Routes urls and provides useful file linking functions
 *
 * @author Samuel Kroiß
 * @version 0.4
 */
class Router
{
    private $htdocs_folder, $error_document, $enable_sitemap, $file_modifiers;
    private $index_route;

    private $args;

    /**
     * __construct
     * Router constructor
     */
    function __construct($index_route = [], $options = [])
    {
        $default_options = [
            "htdocs_folder" => "htdocs/",
            "error_document" => "404.php",
            "enable_sitemap" => true, // outputs a sitemap of all visible routes
            "file_modifiers" => true, // e.g. jpegscaler
        ];
        
        $options = array_merge($default_options, $options);

        $this->htdocs_folder = $options["htdocs_folder"];
        $this->error_document = $options["error_document"];
        $this->enable_sitemap = $options["enable_sitemap"];
        $this->file_modifiers = $options["file_modifiers"];

        $this->index_route = new Route($index_route);
    }

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
        readfile($this->url);

        exit;
    }

    /**
     * route
     * Routes current url to the matching file
     * 
     * @return void
     */
    function route($url = null)
    {
        if(!isset($url)) $url = $this->url();

        if($url == "sitemap.xml" && $this->enable_sitemap) 
        {
            $this->sitemap();
        }

        // If the url directs to a file, we output the file:
        if(file_exists($url) && substr($url, -4) != ".php") 
        {
            $this->output_file($url);
        }

        // Split the url into an array, which we can work off:
        $routes = array_filter(explode("/", $url));

        // The actual routing part. Executed recursively by the Route classes themselves.
        // The result is saved, as we need it later.
        $route_result = $this->index_route->route($routes);

        // The normal ending: We include the file and if given, save the arguments.
        if(isset($route_result["file"]))
        {
            if(isset($route_result["args"])) $this->args = $route_result["args"];
            include($this->htdocs_folder.$route_result["file"]);
        }
        // If the route only was an alias, we go to the route and run the function again:
        elseif(isset($route_result["id"]))
        {
            $this->route($route_result["id"]);
        }
        // No matching route found. Include error document.
        else
        {
            include($this->htdocs_folder.$this->error_document);
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
        return $this->index_route->id($id);
    }

    /**
     * base
     * Returns the relative path to the base/root directory
     * 
     * @return string Relative path to the base/root directory
     */
    function base()
    {
        $url = $this->url();
        return str_repeat("../", substr_count($url, "/"));
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

        $this->index_route->sitemap($base);

        echo '</urlset>';

        exit;
    }

    /**
     * args
     * Returns private $args
     * @return string $args
     */
    function args() { return $this->args; }

    /**
     * file
     * Return if the router will output a file and terminate the script when calling route()
     * @return boolean
     */
    function file() { return file_exists($this->url()) || ($this->url() == "sitemap.xml" && $this->enable_sitemap); }

}
?>
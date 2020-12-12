<?php

    class Router {

        private $route;
        private $default_page;

        function __construct($route, $default_page) {
            $this->route = $route;
            $this->default_page = $default_page;
        }

        public function route($request = null, $include_file = true) {
            if(!isset($request)) {
                $request = $this->get_path_list($this->get_uri());
            }

            $old_request = $request;

            if(array_shift($request) == "sitemap.xml") {
                $this->get_sitemap();
                die;
            }

            $request = $old_request;

            $route = $this->route->get_from_request($request);

            if($include_file) {
                if($route) {
                    include($route->file);
                }
                else {
                    include($this->default_page);
                }
            }
            return $route;
        }

        private function get_uri() {
            return substr(parse_url($_SERVER["REQUEST_URI"])["path"], strlen(substr(getcwd(), strlen($_SERVER["DOCUMENT_ROOT"]))));
        }

        private function get_path_list($uri) {
            return array_values(array_map("strtolower", array_filter(explode("/", $uri))));
        }

        public function route_rel($to = "") {
            $from = $this->get_path_list(substr($this->get_uri(), 0, strrpos($this->get_uri(), "/")));
            $to = $this->get_path_list($to);

            
            while(!empty($from) && !empty($to) && $from[0] == $to[0]) {
                array_shift($from);
                array_shift($to);
            }

            $result = str_repeat("../", sizeof($from)).implode("/", $to);

            if($result == "") {
                $result = ".";
            }

            if(substr($result, -1) != "/") {
                $result .= "/";
            }

            return $result;
        }

        function route_id($id) {
            $to = substr($this->route->get_uri_from_id($id), 0, -1);
            if(isset($to)) {
                return $this->route_rel($to);
            }
            else {
                return null;
            }
        }

        function get_sitemap() {
            header('Content-Type: text/xml');

            $result = "";
            $result .= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            $result .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

            $root = substr($_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"], 0, -11);
            $routes = $this->route->get_all_routes();
            array_unshift($routes, "");

            foreach($routes as &$route) {
                $route = $root.$route;
            }
            unset($route);

            foreach($routes as $route) {
                $result .= "\t<url><loc>$route</loc></url>\n";
            }

            $result .= "</urlset>";

            echo $result;
        }
    }

?>
<?php

namespace semmelsamu;

class Route 
{
    function __construct(
        $url = "/.*/", // Regular expression URL
        $file = "index.php", // the file the route should include
        $id = 0, // the unique id of the route
        $visible = true, // should be included in the sitemap?
        $goto = false // url which has the same route with the id
    )
    {
        $this->url = $url;
        $this->file = $file;
        $this->id = $id;
        $this->visible = $visible;
        $this->goto = $goto;
    }

    function route($url) 
    {
        if(preg_match($this->url, $url, $matches))
        {
            $this->matches = $matches;
            return true;
        }

        return false;
    }
}

?>
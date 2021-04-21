<?php

namespace semmelsamu;

/**
 * Route
 * @author semmelsamu
 */
class Route 
{
    /**
     * Class constructor
     * 
     * @param string $url regular expression URL
     * @param string $file path to the file the route should include
     * @param string $link the link to the route
     * @param int|string $id the unique id of the route
     * @param bool $visible specifies if the route should be included in the sitemap
     * @param bool|int|string if not false, specifies the id of another route which this route refers to
     * 
     * @return null
     */
    function __construct(
        $url = "/.*/",
        $file = "index.php",
        $link = "",
        $id = 0,
        $visible = true,
        $goto = false,
    )
    {
        $this->url = $url;
        $this->file = $file;
        $this->link = $link;
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
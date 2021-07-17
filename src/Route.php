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
     * @param int|string $id the unique id of the route
     * @param bool|int|string if not false, specifies the id of another route which this route refers to and makes the route invisible in the sitemap
     * @param array $tags
     * 
     * @return null
     */
    function __construct(
        $url = "/^$/",
        $file = "index.php",
        $id = 0,
        $goto = false,
        $tags = [],
    )
    {
        if(substr($url, 0, 1) == "/" && substr($url, -1) == "/")
            $this->url_is_regex = true;
        else
            $this->url_is_regex = false;

        $this->url = $url;

        $this->goto = $goto;
        $this->file = $file;
        $this->id = $id;
        $this->tags = $tags;
    }

    function route($url) 
    {
        $this->matches = [];

        if($this->url_is_regex)
        {
            if(preg_match($this->url, $url, $this->matches))
            {
                $this->matches = $this->matches;
                return true;
            }
        }
        else
        {
            if($this->url == $url)
            {
                return true;
            }
        }

        return false;
    }
}

?>
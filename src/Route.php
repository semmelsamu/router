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
     * @param bool|int|string $goto if not false, specifies the id of another route which this route refers to and makes the route invisible in the sitemap
     * 
     * @return null
     */
    function __construct(
        $url = "",
        $file = "index.php",
        $id = null,
        $goto = false,
        $tags = []
    )
    {
        // Import all parameters
        foreach(get_defined_vars() as $key => $val)
            $this->$key = $val;
        
        $this->file_extension = substr($file, strrpos($file, ".")+1);
        
        if(substr($url, 0, 1) == "/" && substr($url, -1) == "/")
            $this->url_is_regex = true;
        else
            $this->url_is_regex = false;
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
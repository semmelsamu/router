<?php

# route(): Recursive function which processes the request and returns an array containing the corresponding file and further arguments. Returns null if no file is found.
function route($request, $routes = null)
{
    # get the routes list to search for matches
    global $routes;

    if(sizeof($request) > 0)
    {
        # Our request contains something! Now let's check if we need to dig down deeper:
    }
    else
    {
        # Our request doesn't contain anything, so we need to return a index file here:
    }
}

?>
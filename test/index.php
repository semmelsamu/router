<?php

// Set up the router
include("../src/router.php");
use \semmelsamu\Router;

$router = new Router();


// Add a simple Route
$router->add("", function() { echo "Hello!"; });


// Add a Route with a file instead of a callback function
$router->add("image", "image.jpg");

// PHP files will be automatically included
$router->add("about", "about.php"); 


// Add a Route only accessable via POST
$router->add(url: "form", callback: "process_form.php", methods:["POST"]);


// Add a Route with a unique ID and tags
$router->add(url: "page/contact", callback: "contact.php", id: 4, tags: ["main", "public"]);

// The ID can be used later to link to the Route again
$link_to_contacts = $router->base . $router->id(4); # Should be something like "./page/contact"
echo '<a href="'.$link_to_contacts.'">To the contact page</a>';


// Add a Route with Parameters in the URL
$router->add("video/<id>/comments/<comment>", function($params) {
    echo "Video ID: " . $params["id"]; 
    echo "Comment: " . $params["comment"]; 
});


// Add the 404 route if no matching route was found
$router->add_404(function() { echo "Error 404"; });


// Call the current callback
echo $router->route();
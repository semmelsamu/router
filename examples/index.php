<?php

    include("../src/semmelsamu/router/index.php");

    use \semmelsamu\Router;

    $router = new Router(
    [
        "htdocs_folder" => "htdocs/", // every file has this as prefix
        "error_document" => "404.php", // if no matching route was found, this file is included
        "enable_sitemap" => true, // outputs a sitemap of all visible routes
        "file_modifiers" => true, // e.g. jpegscaler
    ],
    [
        "file" => "index.php", // the file the route should include
        "id" => "start", // the unique id of the route
        "accept_arguments" => false, // if further parts of the url are given, still use this route and get the parts
        "visible" => true, // should be included in the sitemap?
        "routes" => [ // further sub-routes

            "blog" => [
            "file" => "blog.php",
            "id" => "blog",
            "accept_arguments" => true,
            "visible" => true,
            "routes" => [

                "edit" => [
                "file" => "edit_post.php",
                "id" => "edit_post",
                "accept_arguments" => false,
                "visible" => false, ]
                
            ]],

            "about" => [
            "file" => "about.php",
            "id" => "about",
            "visible" => false,
            "accept_arguments" => true ],

            "about-us" => [
                "goto" => "about" ] // url which has the same route with the id "about"

        ]
    ]
    );

    if(!$router->file()):

?>

<base href="<?= $router->base() ?>">

<ul>
    <li><a href="<?= $router->id("start") ?>">Start</a></li>
    <li><a href="<?= $router->id("blog") ?>">Blog</a></li>
    <li><a href="<?= $router->id("edit_post") ?>">Edit</a></li>
    <li><a href="<?= $router->id("about") ?>">About</a></li>
    <li><a href="sitemap.xml">/sitemap.xml</a></li>
</ul>

<hr>

<?php

    endif;

    $router->route();

?>

<hr>

<p>Here are Some useful router functions:</p>
<?php global $router; ?>

<p>args()</p>
<?php db($router->args()); ?>

<p>url()</p>
<?php db($router->url()); ?>

<p><a href="<?= $router->base() ?>">base()</a></p>
<?php db($router->base()); ?>

<p><a href="<?= $router->id("edit_post") ?>">id("edit_post")</a></p>
<?php db($router->id("edit_post")); ?>
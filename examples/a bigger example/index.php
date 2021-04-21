<?php

    include("../../index.php");

    use \semmelsamu\Router;
    use \semmelsamu\Route;

    $router = new Router();

    $router->add(new Route(id: "start"));
    $router->add(new Route(id: "blog", url: "blog", file: "blog.php"));
    $router->add(new Route(id: "about", url: "about", file: "about.php"));
    $router->add(new Route(url: "about-us", goto: "about"));
    $router->add(new Route(id: "edit_post", url: "/blog\/id\/([0-9]*)\/edit/", file: "edit_post.php", visible: false));

?>

<!-- Basic styling, nothing fancy -->
<style> body { padding: 32px; box-sizing: border-box; max-width: 1280px; } </style>

<base href="<?= $router->base() ?>">

<ul>
    <li><a href="<?= $router->id("start") ?>">Start</a></li>
    <li><a href="<?= $router->id("blog") ?>">Blog</a></li>
    <li><a href="<?= $router->id("about") ?>">About</a></li>
    <li><a href="sitemap.xml">/sitemap.xml</a></li>
</ul>

<hr>

<?php db($router->route()); ?>

<hr>

<p>Here are Some useful router functions:</p>
<?php global $router; ?>

<p>url()</p>
<?php db($router->url()); ?>

<p>base()</p>
<?php db($router->base()); ?>

<p><a href="<?= $router->id("blog") ?>">id("blog")</a></p>
<?php db($router->id("blog")); ?>
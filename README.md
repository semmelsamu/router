# Router

> Simple & fast PHP router

## Requirements

- PHP

## Installation

Copy the `src/semmelsamu/router` folder to a static location on your webserver.

## Setup

Create a `.htaccess` file which redirects all requests to one PHP file. The `.htaccess` file could look something like this:

```htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.+)$ index.php [QSA,L]
```

In the redirected PHP file, include the `index.php` file:

```php
include("router/index.php");
```

Then, you need to create a new `Router` class. This is the main class which handles all the routing and url managing.<br>
The `Router` class needs 2 arguments. The first is your index route (explained [here](#the-route-class)), the second is your 404 file, which gets included if you call the route function, but no matching route was found.

```php
$router = new Router(new Route([]), "404.php");
```

After that, call the routers main function, `route()`.

```php
$router->route();
```

Now you're all set up! It is now time to configure your routes.

## The Route class

Imagine the Route class as a virtual directory on your Server, which the user can get to with the corresponding url. <br>
The First parameter from your Router class is the starting directory, or the index route. From there, you can create sub-routes, which then correspond to the subdirectories in the url.

The Route class accepts the following parameters:

### file

- The file to which this route should link.

### accept_args

- Specifies if the route accepts further parts of the url as arguments or not.
- Default: `false`

### id

- The id which can then be linked to again.

### routes

- Specifies sub-routes or subdirectories. 
- Type: Array, which holds the further `Route` classes.

## Examples

To make sense of all that, here is an example, how the main file could look:

```php
<?php

    include("router/index.php");

    $router = new Router(new Route(["file" => "htdocs/index.php", "routes" => [
        "site" => new Route(["file" => "htdocs/site.php", "routes" => [
            "sub" => new Route(["file" => "htdocs/sub.php"]),
        ]])
    ]]), "htdocs/404.php");

    $router->route();

?>
```

## Other functions

The `Router` class provides 2 other functions, which help you to link your dynamic pages to static directories on your server.

### route_id

```php
$router -> route_id ( string $id ) : string
```

Returns the relative path to the route with the id `$id`.

This function is intended for getting the link hrefs for your site:

```php
<a href="<?= $router->route_id('index') ?>footer.css">Home</a>
```

### route_rel

```php
$router -> route_rel ( string $path = "" ) : string
```

Returns the relative path to the directory with the absolute path `$path`.

This function is intended for linking static files in your site, e.g. css, js or img files. If your css files are e.g. in `static/css`, you can use this function to dynamically get the relative path from the url to the file:

```php
<link rel="stylesheet" href="<?= $router->route_rel('static/css') ?>footer.css">
```
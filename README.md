# Router

> With this router you can create custom urls for your Website. Other features are a auto-generated sitemap, an image scaler and redirects.

## Requirements

- PHP
- Any webserver which can redirect requests to a specific file

## Installation

Copy the `src/semmelsamu/router` folder to a static location on your webserver.

## Setup

Create a `.htaccess` file which redirects all requests to one PHP file. Also, the htaccess file needs to be configured that every url ens with a trailing slash. The `.htaccess` file could look something like this:

```htaccess
RewriteEngine On
RewriteCond %{REQUEST_URI} !(/$|\.) 
RewriteRule (.*) %{REQUEST_URI}/ [R=301,L] 
RewriteRule . index.php [QSA,L]
```

In the redirected PHP file, include the `index.php` file:

```php
include("router/index.php");
```

We use namespacing, so use the namespace for the class:

```php
use \semmelsamu\Router;
```

Then, you need to create a new `Router` class. This is the main class which handles all the routing and url managing.<br>
The `Router` class accepts 2 arguments. The first argument is the [route tree](#the-route-tree), and the second argument is an array containing all your options.

```php
$router = new Router(["file" => ..., "routes" => [...]], ["option1" => "value1", "option2" => "value2", ...]);
```

After that, call the routers main function, `route()`.

```php
$router->route();
```

After that, you're all set up! It is now time to configure your router.

## The Route tree

Imagine the route tree as a virtual file system. Each route is a virtual directory, linked to a file. The user can then enter the url to the virtual directory and then will be redirected to the linked file.<br>
The route tree is an array, containing the index route and further sub-routes (like a folder containing files and sub-folders). Specify the route as followed:

### file

- Type: `string`
- Default: `"index.php"`
- The file to which this route should link.

### id

- Type: `string` or `int`
- Default: `0`
- The unique id which can be refered to.

### routes

- Type: `array`
- Default: `[]`
- Specifies sub-routes or subdirectories, which hold the further `Route` classes.

### accept_arguments

- Type: `bool`
- Default: `false`
- Specifies if the route accepts further parts of the url as arguments or not.

### visible

- Type: `bool`
- Default: `true`
- Specifies if this route should be shown in the sitemap.

### goto

- Type: `string` or `int` or `bool`
- Default: `false`
- If not false, specify to redirect to another route with the id of the value of goto

## Options

### htdocs_folder

- Type: `string`
- Default: `"htdocs/"`
- This prefix will be applied to all files `"file" => [...]` from the routes and the error document `"error_document" => [...]`.

### error_document

- Type: `string`
- Default: `"404.php"`
- This file will be included if no matching route was found.

### enable_sitemap

- Type: `boolean`
- Default: `true`
- Specifies if the [sitemap()](###sitemap) function should be on or not.

### file_modifiers

- Type: `boolean`
- Default: `true`
- Specifies if file modifiers should be activated or not.

With file modifiers you can control how files on your server should be outputted to the user. At the moment, the only file modifier is the image scaler:

It only supports JEPGs at the moment. Just append the desired image size at the end of the url:

```
/path/to/your/image.jpg?s=200
```

- `?s=` specifies the size of the smallest side of the image.
- `?w=` specifies the width of the image.
- `?w=` specifies the height of the image.

Only one option can be applied at the same time.

## Examples

To make sense of all that, here is an example, how the main file could look:

```php
<?php

    include("../src/semmelsamu/router/index.php");

    use \semmelsamu\Router;

    $router = new Router(
    [
        "file" => "index.php",
        "routes" => [

            "blog" => [
            "file" => "blog.php",
            "id" => "blog",
            "visible" => true,
            "routes" => [

                "edit" => [
                "file" => "edit_post.php",
                "visible" => false, ]
                
            ]],

            "about" => [
            "file" => "about.php",
            "id" => "about" ],

            "about-us" => [
                "goto" => "about" ]

        ]
    ],
    [
        "htdocs_folder" => "htdocs/",
        "error_document" => "404.php"
    ]
    );

    $router->route();

?>
```

A bigger example and an example for the most basic router can be found in the examples folder.

## Other functions

The `Router` class also provides other useful functions:

### id

```php
$router -> id ( string $id ) : string
```

Returns the url to the Route with the id `$id`

This function is intended for getting the link hrefs for your site:

```html
<a href="<?= $router->id('start') ?>">Home</a>
```

### base

```php
$router -> base ( void ) : string
```

Returns the relative path to the base/root directory

This function is intended for use in the HTML `<base>` tag:

```html
<base href="<?= $router->base() ?>">
```

### args

```php
$router -> args ( void ) : array
```
If the route accepts args (further parts of the url), those will be stored here.

### sitemap

```php
$router -> sitemap ( void ) : void
```

Print a basic sitemap of all visible sites mentioned in the route tree and terminate the script.

If `enable_sitemap` is set to `true` in the router options, this function will automatically be called when the user enters the url `/sitemap.xml`

### url

```php
$router -> url ( void ) : string
```

Return the relative URL from the router root directory, without the PHP parameters

### file

```php
$router -> file ( void ) : boolean
```

Return if the router will output a file and terminate the script when calling `route()`.

Intended for if you want to include HTML (e.g. the head of the document) before you call the route function. Example:

```php
if(!$router->file()):

    echo "<head>";
    [...]

endif;

$router->route();
```
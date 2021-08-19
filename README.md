# router

> Small PHP router with route linking

## Requirements

- PHP >= 8
- Any webserver which can redirect requests to a specific file (e.g. via .htaccess)

## Installation

Copy the repository to a static location on your webserver.

## Setup

Redirect all requests to one PHP file. A possible solution with the `.htaccess` file could look something like this:

```htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule . index.php [QSA,L]
```

In this case, we redirect all traffic to `index.php`.

In the redirected PHP file, include the router:

```php
include("src/router.php");
```
Use the namespace `semmelsamu`:

```php
use \semmelsamu\Router;
```

## Router

```php
new Router() : void
```

Create a new instance of the router.

### add

```php
Router::add(
    $url = "", 
    $callback,
    $methods = true,  
    $id = null, 
    $tags = null
) : void
```

Add a Route to the Router.

#### Parameters

- `$url`
    - The url the Route should match. Parameters can be defined using angle brackets: `<param>`. See more in the [example](#example) at the end.
    - Type: `string` or `regex`
    - Default: `""`
- `$callback`
    - The callback function or a file. If the file is a PHP file, it will be included. Else, it will just be sent to the browser as normal.
    - Type: `function` or `string`
- `$methods`
    - The accepted request methods. If set to `true`, all request methods will be accepted.
    - Type: `bool` or `array`
    - Default: `true`
- `$id`
    - The unique id of the Route.
    - Type: `int`
    - Default: `null`
- `$tags`
    - The tags of the Route.
    - Type: `array`
    - Default: `[]`

### add_404

```php
Router::add_404($callback) : void
```

Add the 404 callback to the Router.

#### Parameters

- `$callback`
    - The callback function or a file. If the file is a PHP file, it will be included. Else, it will just be sent to the browser as normal.
    - Type: `function` or `string`

### route

```php
Router::route() : void
```

Call the current callback.

### url

```php
Router::url : string
```

#### Return values

Returns the relative URL from the router root directory, without the PHP parameters

### base

```php
Router::base : string
```

#### Return values

Returns the relative path to the base/root directory

### id

```php
Router::id($id) : string
```

Return the relative path to the route with a specific id

#### Parameters

- `$id`
    - The id of the route.
    - Type: `bool`

#### Return values

Returns the relative path to the route. Returns nothing if the Route's url is a regular expression.

## Example

```php
// Set up the router
include("src/router.php");
use \semmelsamu\Router;

$router = new Router();


// Add a simple Route
$router->add("/", function() { echo "Hello!"; });


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
$router->add("video/<id>/comments/<comment>", function() { 
    
    // Get the Parameters
    global $router;
    echo "Video ID: " . $router->matches["id"]; 
    echo "Comment: " . $router->matches["comment"]; 
});


// Of course you can mix and match all the options above as you like and need! 
```
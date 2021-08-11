# router

> Small PHP router with route linking

## Requirements

- PHP
- Any webserver which can redirect requests to a specific file (e.g. via .htaccess)

## Installation

Copy the repository to a static location on your webserver.

## Setup

Redirect all requests to one PHP file (`index.php`). A possible solution with the `.htaccess` file could look something like this:

```htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule . index.php [QSA,L]
```

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
    $callback, 
    $url = "/.*/", 
    $methods = true,  
    $id = null, 
    $tags = null
) : void
```

Add a Route to the Router.

#### Parameters

- `$callback`
    - The callback function. Can also be a file which will be included if it is a PHP file or just be sent to the browser.
    - Type: `function`
- `$url`
    - The url the Route should match.
    - Type: `string|regex`
    - Default: `/.*/`
- `$methods`
    - The accepted request methods. If set to `true`, all request methods will be accepted.
    - Type: `bool|array`
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
    - The callback function. Can also be a file which will be included if it is a PHP file or just be sent to the browser.
    - Type: `function`

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
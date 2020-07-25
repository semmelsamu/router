# php-router
> Simple & fast PHP router

## Prerequisites

- PHP >= 7

## Installation

1. Copy the src folder into your server directory
2. Enter your route tree into `routes.php` (see [routes.php](#routes.php))
3. Insert the matching files into the [htdocs folder](#HTDOCS_PATH)
4. Create a htaccess file, which redirects every request to a php file, which then routes the request. (See examples)

## Usage

To route the request, you need to include the main.php file, create a new router object and run the route function:

```php
include("src/php-router/main.php");
$router = new Php_router($routes);
$router->route_include();
```

If you just want the path to the file and possible arguments, instead of `route_include()`, call [`route()`](#route())

## Functions

### route_include()

`route_include()` - Auto-Includes routed file

#### Syntax

```php
route_include([string $request_uri = null])
```

Includes the routed file if found, if no matching file is found, it includes the 404 file specified in the config.

#### Parameters

- `request_uri`: The request the router should process. If it is null, the default `$_SERVER["REQUEST_URI"]` will be used.

#### Return Values

If the route is found and it accepts arguments, those will be returned as array. If not, null will be returned.

### route()

`route()` - Searches route

#### Syntax

```php
route([string $request_uri = null])
```

Searches the route tree for a matching route.

#### Parameters

Same as [`route_include()`](#route_include())

#### Return Values

If the route is found, an array will be returned, containing the path to the route, and if the route accepts arguments, those will also be returned. An example for how the array could look like:

```
array(2) { 
    ["path"]=> string(9) "index.php" 
    ["args"]=> array(2) { 
        [0]=> string(8) "argument" 
        [1]=> string(16) "another_argument" 
    } 
} 
```

## Configuration

### .htaccess

You can use the .htaccess file to redirect every web request to one php file, which then automatically calls the router.

If you want the router to work only in a specific directory on your server, you need to specify this directory in the .htaccess file and the config.php file:

```htaccess
RewriteBase /path/to/your/directory
```

If the router should work everywhere, just use `/`

### config.php

#### BASE_PATH

```php
define("BASE_PATH", "/path/to/your/directory");
```

If you want the router to work only in a specific directory on your server, you need to specify this directory here.

#### HTDOCS_PATH

```php
define("HTDOCS_PATH", "/path/to/your/htdocs/folder");
```

This is the path where your htdocs folder is.

#### PATH_TO_404

```php
define("PATH_TO_404", "path/to/your/404/document.php");
```

This is the path in your htdocs folder where the 404 document is.

### routes.php

This php file containes the variable `$routes`, which is an array containing all routes. A route is an array with the following keys:

#### url

The url in the request. If the url is `*`, that route is the index route.

#### path

The path to the corresponding file in the htdocs folder.

#### args

Optional. If set to true, the route will accept arguments in the url.

#### suburls

Optional. Contains an array which contains further subroutes/urls.

## Examples

To get a better understanding of how the router works, i recommend looking into the examples folder.

## License

[MIT](https://opensource.org/licenses/MIT) © [Samuel Kroiß](https://github.com/semmelsamu)
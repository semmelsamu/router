# router

> Small PHP router with route linking and file redirects

## Requirements

- PHP
- Any webserver which can redirect requests to a specific file (e.g. via .htaccess)

## Installation

Copy the repository to a static location on your webserver.

## Setup

Redirect all requests to one PHP file (`index.php`). A possible solution with the `.htaccess` file could look something like this:

```htaccess
RewriteEngine On
RewriteRule . index.php [QSA,L]
```

In the redirected PHP file, include the router:

```php
include("src/router.php");
```
Use the namespace `semmelsamu`:

```php
use \semmelsamu\Router;
use \semmelsamu\Route;
```

## Router

```php
new Router([$htdocs_folder, $error_document]) : void
```

Create a new instance of the router.

#### Parameters

- `$htdocs_folder`
    - The folder where all your htdocs are.
    - Type: `string`
    - Default: `htdocs/`
- `$error_document`
    - The path to the 404 document
    - Type: `string`
    - Default: `404.php`

### add

```php
Router::add($routes) : void
```

Add one or multiple [Routes](#routeclass) to the Router.

#### Parameters

- `$routes`
    - The route(s).
    - Type: `Array`

### route

```php
Router::route([$id]) : Route
```

Set the current route.

#### Parameters

- `$id`
    - If specified, set the route with the id `$id`.
    - Type: `int|string`

#### Return values

Returns the current route.

### output

```php
Router::output() : void
```

Output the current route.

### url

```php
Router::url([$trailing_slashes]) : string
```

Return the relative URL from the router root directory, without the PHP parameters

#### Parameters

- `$trailing_slashes`
    - States if trailing slashes are allowed.
    - Type: `bool`

#### Return values

Returns the relative URL from the router root directory, without the PHP parameters

### base

```php
Router::base() : string
```

Return the relative path to the base/root directory

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

Returns the relative path to the route or `NULL` if the Route's url is a regular expression


<a id="routeclass"></a>
## Route

```php
new Route([$url, $file, $id, $goto]) : void
```

Create a new Route.

#### Parameters

- `$url`
    - The url to the route or a regular expression that matches the url.
    - Type: `string|regex`
    - Default: `/^$/`
- `$file`
    - The path to the file the route should refer to.
    - Type: `string`
    - Default: `index.php`
- `$id`
    - The unique id of the route.
    - Type: `string|int`
    - Default: `NULL`
- `$goto`
    - If not `false`, specifies the id of another Route this Route is an alias for.
    - Type: `bool|int|string`
    - Default: `false`

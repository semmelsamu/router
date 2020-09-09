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

In the redirected file, include the `index.php` file:

```php
include("router/index.php");
```

## Usage

To add routes, create an instance of the `Route` class. It accepts parameters as an array.

```php
$routes = new Route([]);
```

### file

- The file to which this route should link.

### args

- Specifies if the route accepts further parts of the uri as arguments or not.
- Default: `false`

### id

- The id which can then be linked to again.

### routes

- Sub-routes. Specified by an array, consisting out of further `Route` classes.

## Examples

```php
$routes = new Route([ # /
    "file" => "htdocs/index.php",
    "args" => false,
    "id" => "index",
    "routes" => [
        "about" => new Route([ # about/
            "file" => "htdocs/about.php",
            "id" => "about",
        ]),
        "contact" => new Route([ # contact/
            "file" => "htdocs/contact.php",
            "id" => "contact",
        ])
    ]
]);
```
<?php
# ROUTES.PHP - Change your root tree here:

$routes = [
    [
        "url" => "*",
        "path" => "index.html",
    ],
    [
        "url" => "site",
        "path" => "site.html",
        "suburls" => [
            [
                "url" => "sub",
                "path" => "sub.html",
            ],
        ]
    ],
];

?>
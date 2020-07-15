<?php
# ROUTES.PHP - Change your root tree here:

$routes = [
    [
        "url" => "*",
        "path" => "index.php",
    ],
    [
        "url" => "start",
        "path" => "index.php",
    ],
    [
        "url" => "site",
        "path" => "site.php",
        "suburls" => [
            [
                "url" => "sub",
                "path" => "sub.php",
            ],
        ]
    ],
];

?>
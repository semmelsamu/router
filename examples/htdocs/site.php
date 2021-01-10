<h1>Site</h1>

<?php global $router; ?>

<img src="<?= $router->route_rel("img") ?>pexels-zhaocan-li-1755243.jpg?s=400">
<p>Image with size 400 (at least 400 pixels in every dimension) ?s=400</p>
<br>
<img src="<?= $router->route_rel("img") ?>pexels-zhaocan-li-1755243.jpg?h=200">
<p>Same image with height 200 ?h=200</p>
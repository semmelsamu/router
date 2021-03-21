<?php

/**
 * db
 * Short debugging function
 * Outputs contents of a variable
 *
 * @author Samuel Kroiß
 * @version 0.1
 * 
 * @param string $var The variable
 * @return void
 */
function db($var) {echo "<pre>"; var_dump($var); echo "</pre>";}

?>
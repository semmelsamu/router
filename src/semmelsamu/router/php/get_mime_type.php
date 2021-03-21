<?php

namespace semmelsamu;

function get_mime_types($mime_file_url = "https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types") 
{

    $mime_file = file_get_contents($mime_file_url);
    

    $regex = "/^([^#].+\/[^\t]+)[\t]+(.+)$/m";

    preg_match_all($regex, $mime_file, $mime_array);


    $mime_types = [];

    foreach($mime_array[0] as $key => $value) {
        $mime_types[$mime_array[2][$key]] = $mime_array[1][$key];
    }

    return $mime_types;

}

function get_mime_type($filename) {

    $mime_types = get_mime_types();

    $extension = substr($filename, 0, strrpos($filename, ".")+1);

    if(array_key_exists($extension, $mime_types)) {
        return $mime_types[$extension];
    }
    else {
        return mime_content_type($filename);
    }

}

?>
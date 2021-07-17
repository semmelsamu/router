<?php

/**
 * Output a file to the user and end the script
 * @param string $file path to the file
 * @return void
 */
function output_file($file) 
{
    if(!file_exists($file)) return;

    // Return mime type ala mimetype extension
    $finfo = finfo_open(FILEINFO_MIME_TYPE); 
    $mime_type = finfo_file($finfo, $file);
    finfo_close($finfo);

    header("Content-Type: ".$mime_type);
    readfile($this->url());
    exit;
}

?>
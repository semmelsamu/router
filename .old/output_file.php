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
    if(!isset($this->mime_types))
        foreach(@explode("\n",@file_get_contents('http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types'))as $x)
                if(isset($x[0])&&$x[0]!=='#'&&preg_match_all('#([^\s]+)#',$x,$out)&&isset($out[1])&&($c=count($out[1]))>1)
                    for($i=1;$i<$c;$i++)
                        $s[]='&nbsp;&nbsp;&nbsp;\''.$out[1][$i].'\' => \''.$out[1][0].'\'';
    $this->mime_types = @sort($s)?'$mime_types = array(<br />'.implode($s,',<br />').'<br />);':false;
    

    header("Content-Type: ".$mime_type);
    readfile($this->url());
    exit;
}

?>
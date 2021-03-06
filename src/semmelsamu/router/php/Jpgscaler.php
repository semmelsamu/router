<?php

namespace semmelsamu;

class Jpgscaler {

    static function jpgscaled($filename) {

        header('Content-type: image/jpg');

        if(isset($_GET["s"]) || isset($_GET["w"]) || isset($_GET["h"])) {

            list($width, $height) = getimagesize($filename);

            if(isset($_GET["s"])) {

                if($height < $width) {
                    $new_height = $_GET["s"];
                    $new_width = ($new_height / $height) * $width;
                }
                else {
                    $new_width = $_GET["s"];
                    $new_height = ($new_width / $width) * $height;
                }

            }
            else if(isset($_GET["w"])) {
                $new_width = $_GET["w"];
                $new_height = ($new_width / $width) * $height;
            }
            else if(isset($_GET["h"])) {
                $new_height = $_GET["h"];
                $new_width = ($new_height / $height) * $width;
            }

            $image_p = imagecreatetruecolor($new_width, $new_height);
            $image = imagecreatefromjpeg($filename);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

            imagejpeg($image_p);
            exit;
        }
        else {

            readfile($filename);
            exit;
        }
    }
}

?>
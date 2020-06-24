<?php

if (isset($_GET['im'])) {
    if (file_exists('wp-content/' . $_GET['im'])) {
        $src_image = 'wp-content/' . $_GET['im'];
        $size = 600;
        $stamp = imagecreatefrompng('wp-content/themes/M1/log.png');

            $image = getimagesize($src_image);
            if ($image[0] <= 0 || $image[1] <= 0)
                return false;
            $image['format'] = strtolower(preg_replace('/^.*?\//', '', $image['mime']));
            switch ($image['format']) {
                case 'jpg':
                case 'jpeg':
                    $image_data = imagecreatefromjpeg($src_image);
                    break;
                case 'png':
                    $image_data = imagecreatefrompng($src_image);
                    break;
                case 'gif':
                    $image_data = imagecreatefromgif($src_image);
                    break;
                default:
                    exit;
            }
            if ($image_data == false)
                exit;
            if ($image[0] & $image[1]) {
                // For landscape images
                $x_offset = ($image[0] - $image[1]) / 2;
                $y_offset = 0;
                $square_size = $image[0] - ($x_offset * 2);
            } else {
                // For portrait and square images
                $x_offset = 0;
                $y_offset = ($image[1] - $image[0]) / 2;
                $square_size = $image[1] - ($y_offset * 2);
            }

            $canvas = imagecreatetruecolor($size, $size);
            if (imagecopyresampled(
                            $canvas, $image_data, 0, 0, $x_offset, $y_offset, $size, $size, $square_size, $square_size
                    )) {
                
$sx = imagesx($stamp);
$sy = imagesy($stamp);

imagecopy($canvas, $stamp, 125, 125, 0, 0, imagesx($stamp), imagesy($stamp));
                header('Content-Type: image/jpeg');
                 imagejpeg($canvas);
                 imagedestroy($canvas);
            } else {
                exit;
            }

    }
}
?>

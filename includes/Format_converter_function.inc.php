<?php

function Format_konvert(string $newFilename, string $pfad, string $outputformat, int $q):bool {

if (!file_exists($pfad)) {
    return false;
}

    $info = getimagesize($pfad);
    //ta($info);
    if ($info === false || !isset($info['mime'])) {
        return false;
    }

        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($pfad);
        } 
        elseif ($info['mime'] == 'image/webp') {
            $image = imagecreatefromwebp($pfad);
        } 
        elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($pfad);
        } 
        elseif ($info['mime'] == 'image/avif') {
            $image = imagecreatefromavif($pfad);
        }
        elseif ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($pfad);
        }
        else {
            // falsche bilddatei..
            return false;
        }

        if (!$image) {
            return false;
        }
        imagepalettetotruecolor($image);

        $ok = false;

            switch($outputformat) {
                case "jpeg":
                    $ok = imagejpeg($image,'./output_image/'. $newFilename,$q);
                    break;
                case "png":
                    $pngQuality = (int) round((100 - $q) / 100 * 9); 
                    $ok = imagepng($image,'./output_image/'. $newFilename, $pngQuality);
                    break;
                case "webp":
                    $ok = imagewebp($image,'./output_image/'. $newFilename,$q);
                    break;
                case "avif":
                    $ok = imageavif($image,'./output_image/'. $newFilename,$q);
                    break;
                    }
    
    imagedestroy($image);

    return $ok;

}
       
?>


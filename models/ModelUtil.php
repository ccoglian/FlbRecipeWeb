<?php
/**
 * @author ccoglianese
 */
class ModelUtil {
    public static function getValueOrNull($value, $key = NULL) {
        if ($key) {
            if (isset($value[$key])) {
                $value = $value[$key];
            } else {
                $value = NULL;
            }
        }

        if ($value == 0 || $value == NULL) {
            return NULL;
        }

        return $value;
    }

    public static function resizeImage($src, $dst, $maxDimension) {
        if (!list($w, $h) = getimagesize($src))
            throw new Exception("Unsupported picture type!");

        $ratio = min($maxDimension / $w, $maxDimension / $h);

        if ($ratio >= 1) {
            Slim::getInstance()->getLog()->info("No image resize needed. Copying $src to $dst.");
            copy($src, $dst);
            return true;
        }

        $width = $w * $ratio;
        $height = $h * $ratio;
        Slim::getInstance()->getLog()->info("Creating new image of $width x $height from $w x $h");
        $new = imagecreatetruecolor($width, $height);

        $type = strtolower(substr(strrchr($src, "."), 1));
        if ($type == 'jpeg')
            $type = 'jpg';
        switch ($type) {
            case 'bmp': $img = imagecreatefromwbmp($src);
                break;
            case 'gif': $img = imagecreatefromgif($src);
                break;
            case 'jpg': $img = imagecreatefromjpeg($src);
                break;
            case 'png': $img = imagecreatefrompng($src);
                break;
            default : throw new Exception("Unsupported picture type!");
        }

        imagecopyresampled($new, $img, 0, 0, 0, 0, $width, $height, $w, $h);

        Slim::getInstance()->getLog()->info("Writing to $dst");
        switch ($type) {
            case 'bmp': imagewbmp($new, $dst);
                break;
            case 'gif': imagegif($new, $dst);
                break;
            case 'jpg': imagejpeg($new, $dst);
                break;
            case 'png': imagepng($new, $dst);
                break;
        }

        return true;
    }

}

?>

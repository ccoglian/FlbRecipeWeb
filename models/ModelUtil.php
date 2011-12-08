<?php
/**
 * @author ccoglianese
 */
class ModelUtil {
    public static function getValueOrNull($value) {
        if ($value == 0 || $value == NULL) {
            return NULL;
        }

        return $value;
    }
}
?>

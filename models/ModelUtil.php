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
}
?>

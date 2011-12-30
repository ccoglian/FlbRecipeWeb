<?php
/**
 * @author ccoglianese
 */
class RecipeAddImageModel {
    private $errors = array();
    private $results = array();

    public function __construct($recipe_id) {
        try {
            $filename = $this->saveFile($recipe_id);

            if ($filename) {
                $recipe = new Recipe($recipe_id);
                $recipe->setImageFilename($filename);
                $recipe->store();
            }
        } catch (Exception $e) {
            $this->errors['exception'] = $e->getMessage();
            Slim::getInstance()->getLog()->error($e);
        }

        foreach ($this->errors as $key => $value) {
            Slim::getInstance()->getLog()->warn("$key: $value");
        }
    }

    private function saveFile($recipe_id) {
        $filename = NULL;
        
        if ($_FILES["image"]["type"] == "image/gif"
                || $_FILES["image"]["type"] == "image/jpeg"
                || $_FILES["image"]["type"] == "image/png"
                || $_FILES["image"]["type"] == "image/pjpeg") {
            if ($_FILES["image"]["error"] > 0) {
                $this->errors["Return Code"] = $_FILES["image"]["error"];
            } else {
                Slim::getInstance()->getLog()->info("Upload: " . $_FILES["image"]["name"]);
                Slim::getInstance()->getLog()->info("Type: " . $_FILES["image"]["type"]);
                Slim::getInstance()->getLog()->info("Size: " . ($_FILES["image"]["size"] / 1024) . " Kb");
                Slim::getInstance()->getLog()->info("Temp file: " . $_FILES["image"]["tmp_name"]);

                $i = 1;
                $dir = RecipeModel::getRecipeImageDir();
                $filename = $_FILES["image"]["name"];
                $path_parts = pathinfo($filename);
                $ext = $path_parts['extension'];
                $timestamp = new fTimestamp();
                $timestamp_str = $timestamp->format("YmdHis");
                $filename = "$recipe_id.$timestamp_str.$ext";
                if (file_exists($dir . $filename)) {
                    $filename = $path_parts['filename'] . "-" . $i . "." . $ext;
                    while (file_exists($dir . $filename)) {
                        $i++;
                        $filename = $path_parts['filename'] . "-" . $i . "." . $ext;
                    }
                }

                $src = $_FILES["image"]["tmp_name"];
                $dst = "$src.$ext";
                move_uploaded_file($src, $dst);
                $result = ModelUtil::resizeImage($dst, $dir . $filename, 300);
                unlink($dst);
                Slim::getInstance()->getLog()->info("Stored in: " . $dir . $filename);
            }
        } else {
            $this->errors["filetype"] = "Unknown file type: "  . $_FILES["image"]["type"];
        }

        return $filename;
    }

    // Mimic flourish ActiveRecord
    public function toJSON() {
        $all_values = array('success' => !$this->errors,
                            'errors' => $this->errors,
                            'results' => $this->results,
                            );
        
        return json_encode($all_values);
    }
}
?>

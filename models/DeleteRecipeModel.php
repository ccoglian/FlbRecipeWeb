<?php
/**
 * @author ccoglianese
 */
class DeleteRecipeModel {
    private $errors = array();

    public function __construct($id) {
        try {
            $recipe = new Recipe($id);
            $recipe->setActive(0);
            $recipe->store();
        } catch (Exception $e) {
            $this->errors['exception'] = $e->getMessage();
            Slim::getInstance()->getLog()->error($e);
        }

        foreach ($this->errors as $key => $value) {
            Slim::getInstance()->getLog()->warn("$key: $value");
        }
    }

    // Mimic flourish ActiveRecord
    public function toJSON() {
        $all_values = array('success' => !$this->errors,
                            'errors' => $this->errors);
        
        return json_encode($all_values);
    }
}
?>

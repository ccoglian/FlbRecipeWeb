<?php
/**
 * @author ccoglianese
 */
class RecipeSearchModel {
    private $errors = array();
    private $results = array();

    public function __construct($recipe_id) {
        try {
            $recipe_search = new RecipeSearch($recipe_id);
            $this->results = $recipe_search->getValues();
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
                            'errors' => $this->errors,
                            'results' => $this->results,
                            );
        
        return json_encode($all_values);
    }
}
?>

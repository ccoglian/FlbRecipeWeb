<?php
/**
 * @author ccoglianese
 */
class MakesModel {
    private $errors = array();
    private $results = array();

    public function __construct($user_id) {
        try {
            $filter = array('user_id=' => $user_id);
            $order_by = array('local_time' => 'desc');
            $records = fRecordSet::build('ScheduledMake', $filter, $order_by);
            $records->precreateRecipes();

            foreach ($records as $record) {
                $recipe = $record->createRecipe();
                
                $this->results[] = array("recipe_id" => $recipe->getRecipeId(),
                                         "title" => $recipe->getTitle(),
                                         "scheduled_make_id" => $record->getScheduledMakeId(),
                                         "local_time" => "" . $record->getLocalTime());
            }
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
                            'results' => $this->results);
        
        return json_encode($all_values);
    }
}
?>

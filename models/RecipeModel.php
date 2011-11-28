<?php
/**
 * @author ccoglianese
 */
class RecipeModel {
    private $recipe;
    private $items = array();
    private $reminders = array();

    public function __construct($recipe_id) {
        $this->recipe = new Recipe($recipe_id);
        
        $records = fRecordSet::build('RecipeItem', array('recipe_id=' => $recipe_id));
        $records->precreateUnits();
        foreach ($records as $record) {
            $values = $record->getValues();
            $values['unit'] = $record->createUnit()->getValues();
            $this->items[] = $values;
        }

        $records = fRecordSet::build('RecipeReminder', array('recipe_id=' => $recipe_id));
        foreach ($records as $record) {
            $this->reminders[] = $record->getValues();
        }
    }

    // Mimic flourish ActiveRecord
    public function toJSON() {
        $recipe_values = $this->recipe->getValues();
        $recipe_values['image_url'] = 'http://' . $_SERVER['SERVER_NAME'] . "/" . $this->recipe->getImageFilename();

        $all_values = array('recipe' => $recipe_values,
                            'recipe_items' => $this->items,
                            'recipe_reminders' => $this->reminders);
        
        return json_encode($all_values);
    }
}
?>

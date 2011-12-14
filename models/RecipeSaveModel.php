<?php
/**
 * @author ccoglianese
 */
class RecipeSaveModel {
    private $errors = array();
    private $results = array();

    public function __construct() {
        try {
            global $db;

            $user_id = fRequest::get('user_id');

            $recipe = new Recipe(ModelUtil::getValueOrNull(fRequest::get('recipe_id')));
            $recipe->setCreatedBy($user_id);
            $recipe->populate();
            $this->errors = $recipe->validate(TRUE, TRUE);

            if (!$this->errors) {
                $recipe->store();
                $recipe_id = $recipe->getRecipeId();
                $recipe_items = fRequest::get('recipe_items', NULL, array());
                $ingredient_list_text = '';
                $recipe_item_ids = array();

                foreach ($recipe_items as $item) {
                    if (isset($item['recipe_item_id']))
                        $recipe_item_ids[] = $item['recipe_item_id'];
                }

                $db->execute("DELETE
                              FROM   flb.recipe_items
                              WHERE  recipe_id = %i "
                              . ($recipe_item_ids ? "AND    recipe_item_id NOT IN (%i)" : ""), $recipe_id, $recipe_item_ids);

                $i = 0;
                foreach ($recipe_items as $item) {
                    $recipeItem = new RecipeItem(ModelUtil::getValueOrNull($item, 'recipe_item_id'));
                    $recipeItem->setRecipeId($recipe_id);
                    $recipeItem->setQuantity($item['quantity']);
                    $recipeItem->setUnitId($item['unit_id']);
                    $recipeItem->setItemName($item['item_name']);
                    $recipeItem->setComments($item['comments']);
                    $recipeItem->setOrderKey($i++);
                    $errors = $recipeItem->validate(TRUE, TRUE);

                    if (!$errors) {
                        $recipeItem->store();
                    } else {
                        $this->errors[] = $errors;
                    }

                    $unit = new Unit($recipeItem->getUnitId());
                    $ingredient_list_text .= $recipeItem->getQuantity()
                            . " " . ($recipeItem->getQuantity() > 1 ? $unit->getUnitNamePlural() : $unit->getUnitName())
                            . " " . $recipeItem->getItemName()
                            . " " . $recipeItem->getComments()
                            . "\n";
                }

                $db->execute("DELETE FROM flb.recipe_search WHERE recipe_id = %i", $recipe_id);

                $searchEntry = new RecipeSearch();
                $searchEntry->setRecipeId($recipe_id);

                $searchEntry->setFullRecipeText(<<<EOD
{$recipe->getTitle()}

{$recipe->getDescription()}

$ingredient_list_text
{$recipe->getInstructions()}

{$recipe->getServes()}
EOD
);
                $errors = $searchEntry->validate(TRUE, TRUE);

                if (!$errors) {
                    $searchEntry->store();
                } else {
                    $this->errors[] = $errors;
                }

                $recipe_reminders = fRequest::get('recipe_reminders', NULL, array());
                $recipe_reminder_ids = array();

                foreach ($recipe_reminders as $reminder) {
                    if (isset($reminder['recipe_reminder_id']))
                        $recipe_reminder_ids[] = $reminder['recipe_reminder_id'];
                }

                $db->execute("DELETE 
                              FROM   flb.recipe_reminders
                              WHERE  recipe_id = %i "
                              . ($recipe_reminder_ids ? "AND    recipe_reminder_id NOT IN (%i)" : ""), $recipe_id, $recipe_reminder_ids);

                foreach ($recipe_reminders as $reminder) {
                    $recipeReminder = new RecipeReminder(ModelUtil::getValueOrNull($reminder, 'recipe_reminder_id'));
                    $recipeReminder->setRecipeId($recipe_id);
                    $recipeReminder->setDescription($reminder['description']);
                    $recipeReminder->setHoursAhead($reminder['hours_ahead']);
                    $errors = $recipeReminder->validate(TRUE, TRUE);

                    if (!$errors) {
                        $recipeReminder->store();
                    } else {
                        $this->errors[] = $errors;
                    }
                }
                
                $this->results['recipe_id'] = $recipe_id;
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
                            'results' => $this->results,
                            );
        
        return json_encode($all_values);
    }
}
?>

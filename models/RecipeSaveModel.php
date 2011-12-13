<?php
/**
 * @author ccoglianese
 */
class RecipeSaveModel {
    private $errors = array();

    public function __construct() {
        try {
            $user_id = fRequest::get('user_id');

            $app = Slim::getInstance();
            $log = $app->getLog();
            
            $recipe_id = fRequest::get('recipe_id');

            // don't delete existing recipes so as to not mess up
            // people's shopping lists, my makes, etc?
            if ($recipe_id) {
                $recipe = new Recipe($recipe_id);
                $recipe->setActive(0);
                $recipe->store();
            }

            $recipe = new Recipe();
            $recipe->setCreatedBy($user_id);
            $recipe->populate();
            $recipe->setRecipeId(0);
            $this->errors = $recipe->validate(TRUE, TRUE);

            if (!$this->errors) {
                $recipe->store();
                $recipe_id = $recipe->getRecipeId();
                $recipe_items = fRequest::get('recipe_items');
                $ingredient_list_text = '';

                foreach ($recipe_items as $item) {
                    $recipeItem = new RecipeItem();
                    $recipeItem->setRecipeId($recipe_id);
                    $recipeItem->setQuantity($item['quantity']);
                    $recipeItem->setUnitId($item['unit_id']);
                    $recipeItem->setItemName($item['item_name']);
                    $recipeItem->setComments($item['comments']);
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

                $recipe_reminders = fRequest::get('recipe_reminders');

                foreach ($recipe_reminders as $reminder) {
                    $recipeReminder = new RecipeReminder();
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
            }
        } catch (Exception $e) {
            $this->errors['exception'] = $e->getMessage();
        }
    }

    // Mimic flourish ActiveRecord
    public function toJSON() {
        $all_values = array('success' => !$this->errors,
                            'errors' => $this->errors,
                            );
        
        return json_encode($all_values);
    }
}
?>

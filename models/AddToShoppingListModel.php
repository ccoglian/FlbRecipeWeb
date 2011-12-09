<?php
/**
 * @author ccoglianese
 */
class AddToShoppingListModel {
    private $errors = array();

    public function __construct($recipe_id) {
        try {
            $items = fRecordSet::build('RecipeItem', array('recipe_id=' => $recipe_id));
            $user = new User(array('email' => fRequest::get('email')));
            $user_id = $user->getUserId();
            foreach ($items as $item) {
                $shoppingListItem = new ShoppingListItem();
                $shoppingListItem->setRecipeItemId($item->getRecipeItemId());
                $shoppingListItem->setUserId($user_id);
                $shoppingListItem->store();
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

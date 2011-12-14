<?php
/**
 * @author ccoglianese
 */
class AddToShoppingListModel {
    private $errors = array();

    public function __construct($recipe_id) {
        try {
            $items = fRecordSet::build('RecipeItem', array('recipe_id=' => $recipe_id));
            $user_id = fRequest::get('user_id');
            foreach ($items as $item) {
                $shoppingListItem = new ShoppingListItem();
                $shoppingListItem->setUserId($user_id);
                $shoppingListItem->setQuantity($item->getQuantity());
                $shoppingListItem->setUnitId($item->getUnitId());
                $shoppingListItem->setItemName($item->getItemName());
                $shoppingListItem->store();
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
                            );
        
        return json_encode($all_values);
    }
}
?>

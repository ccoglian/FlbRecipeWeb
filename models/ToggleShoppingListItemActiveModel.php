<?php
/**
 * @author ccoglianese
 */
class ToggleShoppingListItemActiveModel {
    private $errors = array();

    public function __construct($shopping_list_item_id) {
        try {
            $item = new ShoppingListItem($shopping_list_item_id);
            $item->setActive($item->getActive() ? 0 : 1);
            $item->store();
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

<?php
/**
 * @author ccoglianese
 */
class ToggleExtraShoppingListItemActiveModel {
    private $errors = array();

    public function __construct($extra_shopping_list_item_id) {
        try {
            $item = new ExtraShoppingListItem($extra_shopping_list_item_id);
            $item->setActive($item->getActive() ? 0 : 1);
            $item->store();
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

<?php
/**
 * @author ccoglianese
 */
class AddExtraToShoppingListModel {
    private $errors = array();
    private $results = array();

    public function __construct() {
        try {
            $user_id = fRequest::get('user_id');
            $item_name = fRequest::get('item_name');
            $extraItem = new ExtraShoppingListItem();
            $extraItem->setItemName($item_name);
            $extraItem->setUserId($user_id);
            $extraItem->store();
            $this->results[] = $extraItem->getValues();
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

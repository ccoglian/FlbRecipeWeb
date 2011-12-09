<?php
/**
 * @author ccoglianese
 */
class AddExtraToShoppingListModel {
    private $errors = array();
    private $results = array();

    public function __construct() {
        try {
            $email = fRequest::get('email');
            $user = new User(array('email' => $email));
            $user_id = $user->getUserId();
            $item_name = fRequest::get('item_name');
            $extraItem = new ExtraShoppingListItem();
            $extraItem->setItemName($item_name);
            $extraItem->setUserId($user_id);
            $extraItem->store();
            $this->results[] = $extraItem->getValues();
        } catch (Exception $e) {
            $this->errors['exception'] = $e->getMessage();
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

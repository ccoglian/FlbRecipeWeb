<?php
/**
 * @author ccoglianese
 */
class ClearShoppingListModel {
    private $errors = array();

    public function __construct($user_id) {
        try {
            global $db;

            $db->execute(
                    "DELETE
                     FROM   shopping_list_items
                     WHERE  user_id = %i", $user_id);

            $db->execute(
                    "DELETE
                     FROM   extra_shopping_list_items
                     WHERE  user_id = %i", $user_id);
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

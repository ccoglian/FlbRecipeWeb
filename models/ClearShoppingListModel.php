<?php
/**
 * @author ccoglianese
 */
class ClearShoppingListModel {
    private $errors = array();

    public function __construct($user_id) {
        try {
            global $mysql_db;

            $mysql_db->execute(
                    "DELETE
                     FROM   shopping_list_items
                     WHERE  user_id = %i", $user_id);

            $mysql_db->execute(
                    "DELETE
                     FROM   extra_shopping_list_items
                     WHERE  user_id = %i", $user_id);
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

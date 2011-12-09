<?php
/**
 * @author ccoglianese
 */
class ClearShoppingListModel {
    private $errors = array();

    public function __construct($email) {
        try {
            global $mysql_db;

            $user = new User(array('email' => $email));

            $mysql_db->execute(
                    "DELETE
                     FROM   shopping_list_items
                     WHERE  user_id = %i", $user->getUserId());
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

<?php
/**
 * @author ccoglianese
 */
class ShoppingListModel {
    private $errors = array();
    private $results = array();

    public function __construct($email) {
        try {
            global $mysql_db;

            $resultSet = $mysql_db->query(
                    "SELECT ri.item_name, ri.unit_id, sli.active,
                            MAX(n.unit_name) as unit_name, MAX(n.unit_name_plural) as unit_name_plural,
                            SUM(ri.quantity) as quantity,
                            GROUP_CONCAT(sli.shopping_list_item_id) as shopping_list_item_ids
                     FROM   shopping_list_items sli, recipe_items ri, users u, units n
                     WHERE  sli.user_id = u.user_id
                     AND    u.email = %s
                     AND    sli.recipe_item_id = ri.recipe_item_id
                     AND    ri.unit_id = n.unit_id
                     GROUP BY ri.item_name, ri.unit_id, sli.active", $email);

        foreach ($resultSet as $obj) {
            $this->results[] = $obj;
        }
/*
            $user = new User(array('email' => $email));
            $filter = array('user_id=' => $user->getUserId());
            $records = fRecordSet::build('ShoppingListItem', $filter);
            $records->precreateRecipeItems();

            foreach ($records as $record) {
                $values = $record->getValues();
                $recipe_item = $record->createRecipeItem();
                $values['recipe_item'] = $recipe_item->getValues();
                $values['recipe_item']['unit'] = $recipe_item->createUnit()->getValues();
                $this->results[] = $values;
            }
 *
 */
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

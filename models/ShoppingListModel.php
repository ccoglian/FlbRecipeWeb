<?php
/**
 * @author ccoglianese
 */
class ShoppingListModel {
    private $errors = array();
    private $results = array();

    public function __construct($user_id) {
        try {
            global $mysql_db;

            $resultSet = $mysql_db->query(
                    "SELECT ri.item_name, ri.unit_id, sli.active,
                            MAX(n.unit_name) as unit_name, MAX(n.unit_name_plural) as unit_name_plural,
                            SUM(ri.quantity) as quantity,
                            GROUP_CONCAT(sli.shopping_list_item_id) as shopping_list_item_ids
                     FROM   shopping_list_items sli, recipe_items ri, units n
                     WHERE  sli.user_id = %i
                     AND    sli.recipe_item_id = ri.recipe_item_id
                     AND    ri.unit_id = n.unit_id
                     GROUP BY ri.item_name, ri.unit_id, sli.active", $user_id);

            $shoppingListItems = array();
            foreach ($resultSet as $obj) {
                $shoppingListItems[] = $obj;
            }
            $this->results['shoppingListItems'] = $shoppingListItems;

            $filter = array('user_id=' => $user_id);
            $orderBy = array('extra_shopping_list_item_id' => 'desc');
            $items = fRecordSet::build('ExtraShoppingListItem', $filter, $orderBy);
            $extraShoppingListItems = array();
            foreach ($items as $item) {
                $extraShoppingListItems[] = $item->getValues();
            }
            $this->results['extraShoppingListItems'] = $extraShoppingListItems;
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

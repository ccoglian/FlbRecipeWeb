<?php
/**
 * @author ccoglianese
 */
class ShoppingListModel {
    private $errors = array();
    private $results = array();

    public function __construct($user_id) {
        try {
            global $db;

            $resultSet = $db->query(
                    "SELECT sli.item_name, sli.unit_id, sli.active,
                            MAX(n.unit_name) as unit_name, MAX(n.unit_name_plural) as unit_name_plural,
                            SUM(sli.quantity) as quantity,
                            GROUP_CONCAT(sli.shopping_list_item_id) as shopping_list_item_ids
                     FROM   shopping_list_items sli, units n
                     WHERE  sli.user_id = %i
                     AND    sli.unit_id = n.unit_id
                     GROUP BY sli.item_name, sli.unit_id, sli.active", $user_id);

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

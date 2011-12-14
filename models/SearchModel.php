<?php
/**
 * @author ccoglianese
 */
class SearchModel {
    private $errors = array();
    private $results = array();

    public function __construct($key) {
        try {
            global $db;

            $resultSet = $db->query(
                    "SELECT r.*
                     FROM   flb.recipes r
                            JOIN flb.recipe_search s ON r.recipe_id = s.recipe_id
                     WHERE  r.active = 1
                     AND    MATCH (full_recipe_text) AGAINST (%s IN BOOLEAN MODE)", $key);

            foreach ($resultSet as $obj) {
                $this->results[] = array("id" => $obj["recipe_id"], "title" => $obj["title"]);
            }
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

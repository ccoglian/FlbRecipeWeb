<?php
/**
 * @author ccoglianese
 */
class SearchModel {
    private $results = array();

    public function __construct($key) {
        global $mysql_db;

        $resultSet = $mysql_db->query(
                "SELECT r.*
                 FROM   flb.recipes r
                        JOIN flb.recipe_search s ON r.recipe_id = s.recipe_id
                 WHERE  r.active = 1
                 AND    MATCH (full_recipe_text) AGAINST (%s IN BOOLEAN MODE)", $key);

        foreach ($resultSet as $obj) {
            $this->results[] = array("id" => $obj["recipe_id"], "title" => $obj["title"]);
        }
    }

    // Mimic flourish ActiveRecord
    public function toJSON() {
        return json_encode($this->results);
    }
}
?>

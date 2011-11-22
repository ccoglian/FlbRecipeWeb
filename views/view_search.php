<?php
global $mysql_db;

$results = array();
$resultSet = $mysql_db->query("SELECT r.*
                               FROM   flb.recipes r
                                      JOIN flb.recipe_search s ON r.recipe_id = s.recipe_id
                               WHERE MATCH (full_recipe_text)
                               AGAINST (%s IN BOOLEAN MODE)", $key);

foreach ($resultSet as $obj) {
    $results[] = array("id" => $obj["recipe_id"], "title" => $obj["title"]);
}

echo json_encode($results);
?>
<?php
global $mysql_db;

$results = array();
$resultSet = $mysql_db->query("SELECT *
                               FROM flb.recipes
                               WHERE MATCH (title, body)
                               AGAINST (%s IN BOOLEAN MODE)", $key);

foreach ($resultSet as $obj) {
    $results[] = array("id" => $obj["recipe_id"], "title" => $obj["title"]);
}

echo json_encode($results);
?>
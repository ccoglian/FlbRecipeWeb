<?php
    if (in_array('toJSON', get_class_methods($obj))) {
        echo $obj->toJSON();
    } else {
        echo json_encode($obj);
    }
?>
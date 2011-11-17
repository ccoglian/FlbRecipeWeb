<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/inc/init.php');
fAuthorization::requireLoggedIn();

echo DateUtil::getAllMondayOptions();
?>

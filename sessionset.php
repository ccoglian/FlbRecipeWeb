<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/inc/init.php');
fAuthorization::requireLoggedIn();

foreach ($_POST as $key => $value) {
    fSession::set($key, $value);
}
?>
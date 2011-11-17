<?php
include($_SERVER['DOCUMENT_ROOT'] . '/inc/config.php');

function login(User $user, $action) {
    fAuthorization::setUserToken($user->getUserId());
    
    fURL::redirect(fAuthorization::getRequestedURL(TRUE, "index?action=$action"));
}

function get_logged_in_staff_member() {
    $user_id = fAuthorization::getUserToken();
    
    return new StaffMember(array('user_id' => $user_id));
}

function is_user_logged_in() {
    return fAuthorization::getUserToken();
}
?>

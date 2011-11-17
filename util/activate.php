<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/inc/init.php');
fAuthorization::requireLoggedIn();

$id = fRequest::get('id', NULL, 0);
$type = fRequest::get('type', NULL, 0);
$active = fRequest::get('active', NULL, 0);
$show_student = fRequest::get('show_student', NULL, 0);

if ($id) {
    $record = new $type($id);
    $record->setActive($active);
    $record->store();
    $student = NULL;

    if ($show_student) {
        $student = new Student($record->getStudentId());
    }

    fJSON::output(array('id' => "{$type}_{$id}", 'row' => $record->createRow($student)));
}

exit();
?>

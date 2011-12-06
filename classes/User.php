<?php
/**
 * Description of user
 *
 * @author ccoglianese
 */
class User extends MyActiveRecord {
    protected function configure()
    {
        parent::configure();
        
        fORM::registerHookCallback($this, 'post::validate()', 'User::validatePassword');
        fORM::registerHookCallback($this, 'post::validate()', 'User::validateEmail');
    }

    static public function validatePassword($object, &$values, &$old_values, &$related_records, &$cache, &$validation_messages)
    {
        // If a new password was set, it came through the request and does not match the field password confirmation, add an error message
        if (fRequest::get('password') && fRequest::get('password') != fRequest::get('confirm_password')) {
            $validation_messages['password'] = 'The value entered does not match Password Confirmation';
        }
    }

    static public function validateEmail($object, &$values, &$old_values, &$related_records, &$cache, &$validation_messages)
    {
        if (!preg_match('/^[^@]+@.+\..+$/', fRequest::get('email'))) {
            $validation_messages['email'] = 'The email address is not valid (e.g. user@domain.com)';
        }
    }
}
?>

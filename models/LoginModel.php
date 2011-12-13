<?php
/**
 * @author ccoglianese
 */
class LoginModel {
    private $errors = array();
    private $results = array();

    public function __construct() {
        try {
            $user = new User(array('email' => fRequest::get('email')));
            if (fCryptography::checkPasswordHash(fRequest::get('password'), $user->getPassword())) {
                $this->results = $user->getValues();
                return;
            }
        } catch (Exception $e) {
            $this->errors['exception'] = $e->getMessage();
        }

        $this->errors['email'] = 'Email address or password incorrect. Please try again.';
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

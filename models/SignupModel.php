<?php
/**
 * @author ccoglianese
 */
class SignupModel {
    private $errors = array();

    public function __construct() {
        $user = new User();
        $user->populate(); // = $app->request()->params();
        $this->errors = $user->validate(TRUE, TRUE);

        if (!$this->errors) {
            $user->setPassword(fCryptography::hashPassword($user->getPassword()));
            $user->store();
        }
    }

    // Mimic flourish ActiveRecord
    public function toJSON() {
        $all_values = array('success' => !$this->errors,
                            'errors' => $this->errors);
        
        return json_encode($all_values);
    }
}
?>

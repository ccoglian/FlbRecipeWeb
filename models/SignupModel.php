<?php
/**
 * @author ccoglianese
 */
class SignupModel {
    private $errors = array();
    private $results = array();

    public function __construct() {
        try {
            $user = new User();
            $user->populate(); // = $app->request()->params();
            $this->errors = $user->validate(TRUE, TRUE);

            if (!$this->errors) {
                $user->setPassword(fCryptography::hashPassword($user->getPassword()));
                $user->store();
                $this->results = $user->getValues();
            }
        } catch (Exception $e) {
            $this->errors['exception'] = $e->getMessage();
            Slim::getInstance()->getLog()->error($e);
        }

        foreach ($this->errors as $key => $value) {
            Slim::getInstance()->getLog()->warn("$key: $value");
        }
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

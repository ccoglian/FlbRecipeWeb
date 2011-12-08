<?php
/**
 * @author ccoglianese
 */
class DeleteMakesModel {
    private $errors = array();

    public function __construct($id) {
        try {
            $make = new ScheduledMake($id);
            $make->delete();
        } catch (Exception $e) {
            $this->errors['exception'] = $e->getMessage();
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

<?php
/**
 * @author ccoglianese
 */
class UnitsModel {
    private $errors = array();
    private $results = array();

    public function __construct() {
        try {
            $records = fRecordSet::build('Unit', array(), array('unit_name' => 'asc'));
            foreach ($records as $record) {
                $this->results[] = $record->getValues();
            }
        } catch (Exception $e) {
            $this->errors['exception'] = $e->getMessage();
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

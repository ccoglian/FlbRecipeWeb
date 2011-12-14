<?php
/**
 * @author ccoglianese
 */
class UnitAddModel {
    private $errors = array();
    private $results = array();

    public function __construct() {
        try {
            $unit_name = fRequest::get('unit_name');
            $unit_name_plural = fRequest::get('unit_name_plural');
            $unit = new Unit();
            $unit->setUnitName($unit_name);
            $unit->setUnitNamePlural($unit_name_plural);
            $unit->store();
            $this->results[] = $unit->getValues();
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

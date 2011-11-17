<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of myactiverecord
 *
 * @author ccoglianese
 */
class MyActiveRecord extends fActiveRecord {
    protected function configure() {
        fORMDate::configureDateCreatedColumn($this, 'from_date');
//        fORMDate::configureDateUpdatedColumn($this, 'last_updated');
//        fORM::registerHookCallback($this, 'pre::validate()', 'MyActiveRecord::validateActive');
    }

//    static public function validateActive($object, &$values, &$old_values, &$related_records, &$cache, &$validation_messages)
//    {
//        $is_active = $values['active'];
//
//        if ('on' == $is_active) {
//            $is_active = $values['active'] = 1;
//        } elseif ('off' == $is_active) {
//            $is_active = $values['active'] = 0;
//        }
//
//        $is_active = $values['active'];
//        $was_active = isset($old_values['active']) && count($old_values['active']) == 1 && $old_values['active'][0];
//
//        if (!$is_active && $was_active) {
//            $values['thru_date'] = new fTimestamp('now');
//        } elseif ($is_active && !$was_active) {
//            $values['thru_date'] = NULL;
//        }
//    }

    public function getValue($key) {
        return $this->values[$key];
    }

    public function getValues() {
        return $this->values;
    }
    
    public function __toString() {
        $ref = new ReflectionObject($this);
        $str = $ref->getName() . ': {';

        foreach ($this->values as $key => $value) {
            if (fUTF8::sub($str, -1, 1) != '{')
                $str .= ', ';

            $str .= "$key=$value";
        };

        $str .= '}';

        return $str;
    }
}
?>

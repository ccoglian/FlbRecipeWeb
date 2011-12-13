<?php
/**
 * @author ccoglianese
 */
class Recipe extends MyActiveRecord {
    protected function configure() {
        fORMDate::configureDateCreatedColumn($this, 'date_created');
        fORMDate::configureDateUpdatedColumn($this, 'date_updated');
    }
}
?>

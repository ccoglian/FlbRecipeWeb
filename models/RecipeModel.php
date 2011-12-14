<?php
/**
 * @author ccoglianese
 */
class RecipeModel {
    private $errors = array();
    private $results = array();

    public function __construct($recipe_id) {
        try {
            $recipe = new Recipe($recipe_id);
            $recipe_values = $recipe->getValues();
            if ($recipe->getImageFilename())
                $recipe_values['image_url'] = 'http://' . $_SERVER['SERVER_NAME'] . "/" . $recipe->getImageFilename();
            $this->results['recipe'] = $recipe_values;

            $filter = array('recipe_id=' => $recipe_id);
            $order_by = array('order_key' => 'asc');
            $records = fRecordSet::build('RecipeItem', $filter, $order_by);
            $records->precreateUnits();
            $items = array();
            foreach ($records as $record) {
                $values = $record->getValues();
                $values['unit'] = $record->createUnit()->getValues();
                $items[] = $values;
            }
            $this->results['recipe_items'] = $items;

            $filter = array('recipe_id=' => $recipe_id);
            $order_by = array('hours_ahead' => 'desc', 'recipe_reminder_id' => 'asc');
            $records = fRecordSet::build('RecipeReminder', $filter, $order_by);
            $default_reminders = array();
            foreach ($records as $record) {
                $default_reminders[] = $record->getValues();
            }
            $this->results['default_reminders'] = $default_reminders;

            $user_id = fRequest::get('user_id');
            $filter = array('user_id=' => $user_id, 'recipe_id=' => $recipe_id, 'server_time>=' => new fTimestamp());
            $order_by = array('local_time' => 'asc');
            $limit = 1;
            $records = fRecordSet::build('ScheduledMake', $filter, $order_by, $limit);
            $scheduled_make = array();

            if ($records->count()) {
                $record = $records[0];
                $values = $record->getValues();
                $values['local_time'] = "" . $values['local_time'];
                $values['server_time'] = "" . $values['server_time'];
                $scheduled_make['make'] = $values;
                $filter = array('scheduled_make_id=' => $record->getScheduledMakeId());
                $reminderRecords = fRecordSet::build('ScheduledReminder', $filter);
                $scheduledReminders = array();
                foreach ($reminderRecords as $reminderRecord) {
                    $values = $reminderRecord->getValues();
                    $values['local_time'] = "" . $values['local_time'];
                    $values['server_time'] = "" . $values['server_time'];
                    $scheduledReminders[] = $values;
                }
                $scheduled_make['reminders'] = $scheduledReminders;
                $this->results['scheduled_make'] = $scheduled_make;
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

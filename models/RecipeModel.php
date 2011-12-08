<?php
/**
 * @author ccoglianese
 */
class RecipeModel {
    private $errors = array();
    private $recipe;
    private $items = array();
    private $default_reminders = array();
    private $scheduled_make = array();

    public function __construct($recipe_id) {
        try {
            $this->recipe = new Recipe($recipe_id);

            $records = fRecordSet::build('RecipeItem', array('recipe_id=' => $recipe_id));
            $records->precreateUnits();
            foreach ($records as $record) {
                $values = $record->getValues();
                $values['unit'] = $record->createUnit()->getValues();
                $this->items[] = $values;
            }

            $records = fRecordSet::build('RecipeReminder', array('recipe_id=' => $recipe_id));
            foreach ($records as $record) {
                $this->default_reminders[] = $record->getValues();
            }

            $user = new User(array('email' => fRequest::get('email')));
            $filter = array('user_id=' => $user->getUserId(), 'server_time>=' => new fTimestamp());
            $order_by = array('local_time' => 'asc');
            $limit = 1;
            $records = fRecordSet::build('ScheduledMake', $filter, $order_by, $limit);

            if ($records->count()) {
                $record = $records[0];
                $values = $record->getValues();
                $values['local_time'] = "" . $values['local_time'];
                $values['server_time'] = "" . $values['server_time'];
                $this->scheduled_make['make'] = $values;
                $filter = array('scheduled_make_id=' => $record->getScheduledMakeId());
                $reminderRecords = fRecordSet::build('ScheduledReminder', $filter);
                $scheduledReminders = array();
                foreach ($reminderRecords as $reminderRecord) {
                    $values = $reminderRecord->getValues();
                    $values['local_time'] = "" . $values['local_time'];
                    $values['server_time'] = "" . $values['server_time'];
                    $scheduledReminders[] = $values;
                }
                $this->scheduled_make['reminders'] = $scheduledReminders;
            }
        } catch (Exception $e) {
            $this->errors['exception'] = $e->getMessage();
        }
    }

    // Mimic flourish ActiveRecord
    public function toJSON() {
        $recipe_values = $this->recipe->getValues();
        $recipe_values['image_url'] = 'http://' . $_SERVER['SERVER_NAME'] . "/" . $this->recipe->getImageFilename();

        $all_values = array('success' => !$this->errors,
                            'errors' => $this->errors,
                            'recipe' => $recipe_values,
                            'recipe_items' => $this->items,
                            'default_reminders' => $this->default_reminders,
                            'scheduled_make' => $this->scheduled_make,
                            );
        
        return json_encode($all_values);
    }
}
?>

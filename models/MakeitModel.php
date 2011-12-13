<?php
/**
 * @author ccoglianese
 */
class MakeitModel {
    private $errors = array();

    public function __construct() {
        try {
            $user_id = fRequest::get('user_id');
            $make = new ScheduledMake(ModelUtil::getValueOrNull(fRequest::get('scheduled_make_id')));
            $make->populate();
            $make->setUserId($user_id);
            $this->errors = $make->validate(TRUE, TRUE);

            if (!$this->errors) {
                $make->store();

                $reminders = fRequest::get('reminders');
                foreach ($reminders as $reminder) {
                    $scheduledReminder = new ScheduledReminder(ModelUtil::getValueOrNull($reminder['scheduled_reminder_id']));
                    $scheduledReminder->setScheduledMakeId($make->getScheduledMakeId());
                    $scheduledReminder->setRecipeReminderId($reminder['recipe_reminder_id']);
                    $scheduledReminder->setLocalTime($reminder['local_time']);
                    $scheduledReminder->setServerTime($reminder['server_time']);
                    $scheduledReminder->store();
                }
            }
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

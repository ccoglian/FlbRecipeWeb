<?php
/**
 * Description of recipe
 *
 * @author ccoglianese
 */
class Recipe extends MyActiveRecord {
    public static function search($key) {
        // full-text search needed
        $filter = array('key=' => $key);

        if (fRequest::get('grade_level_id')) {
            $filter['grade_level_id='] = fRequest::get('grade_level_id');
        }

        if ($student_group_id == 0) {
            $student_group_id = fRequest::get('student_group_id');
        }

        if ($student_group_id > 0) {
            $filter['student_groups.student_group_id='] = $student_group_id;
        }

        $students = fRecordSet::build('Student', $filter, array('first_name' => 'asc', 'last_name' => 'asc'));

        return $students;
    }

    public static function getAllOptions() {
        global $TEXT;
        $str = '';

        if ('addtogroup' == fRequest::get('header')) {
            $str .= "<option value='0'>Add a new {$TEXT['student']} to this group</option>";
        } elseif ('select' == fRequest::get('header')) {
            $str .= "<option value='0'>Select a {$TEXT['student']}</option>";
        }

        $students = Student::getAll();
        $selid = fRequest::get('selid');

        foreach ($students as $student) {
            $str .= "<option value='{$student->getStudentId()}'" . ($selid == $student->getStudentId() ? ' selected' : '')
                    . ">{$student->getFirstName()} {$student->getLastName()}</option>";
        }

        return $str;
    }

    public function getDisplayName($grade_level = NULL) {
        return $this->getFirstName() . ' ' . $this->getLastName() . ($grade_level ? ' (' . $grade_level->getDisplayName() . ')' : '');
    }
}
?>

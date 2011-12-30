<?php
/**
 * Automatically includes classes
 * 
 * @throws Exception
 * 
 * @param  string $class_name  Name of the class to load
 * @return void
 */
spl_autoload_register('project_autoloader');
function project_autoloader($class_name) {
    $inc_path = array($_SERVER['DOCUMENT_ROOT'] . "/classes/$class_name.php",
        $_SERVER['DOCUMENT_ROOT'] . "/models/$class_name.php",
        $_SERVER['DOCUMENT_ROOT'] . "/inc/flourish/$class_name.php");

    foreach ($inc_path as $file) {
        if (file_exists($file)) {
            include $file;
            return;
        }
    }
    
    //throw new Exception('The class ' . $class_name . ' could not be loaded');
}

function populate_form($isUpdate, $errors, $activeRecord, $key, $value = '') {
    if ($isUpdate) {
        if (!isset($errors[$key])) {
            $value = fRequest::get($key);
        }
    } elseif ($activeRecord) {
        $value = $activeRecord->getValue($key);
    }

    return $value;
}

function populate_form_from_record($errors, $activeRecord, $key, $value = '') {
    if ($activeRecord && !isset($errors[$key])) {
        $value = $activeRecord->getValue($key);
    }

    return $value;
}

function populate_form_hint($errors, $key, $title = '', $msg = '') {
    if (isset($errors[$key])) {
        return get_status_box("Error:", $errors[$key], 'error');
    } elseif ($msg) {
        return get_status_box($title, $msg);
    }

    return '';
}

function get_status_box($title, $msg, $type = 'info', $class = 'status-small') {
    $extra_style = 'background: #D8E4F1; border: solid thin #69C;';
    $icon = 'ui-icon-info';
    $ui_state = 'ui-state-highlight';

    if ($type == 'error') {
        $icon = 'ui-icon-alert';
        $ui_state = 'ui-state-error';
        $extra_style  = '';
    }

    return "<div class='$class ui-widget'>
            <div class='$ui_state ui-corner-all' style='padding: 0 .7em; $extra_style'>
                    <p class='$class'><span class='ui-icon $icon' style='float: left; margin-right: .3em;'></span>
                    <strong>$title</strong> $msg</p>
            </div>
    </div>";
}

function get($key) {
    $val = fRequest::get($key, NULL, 0);

    if (!$val) {
        $val = fSession::get($key, 0);
    }

    return $val;
}
function replace_keywords($str) {
    global $TEXT;
    
    foreach ($TEXT as $key => $value) {
        $search = "%$key%";
        $str = fUTF8::replace($str, $search, $value);
    }

    return $str;
}

function no_break($str) {
    $str = fUTF8::replace($str, ' ', '&nbsp;');
    $str = fUTF8::replace($str, '-', '&#x2011;');

    return $str;
}

fAuthorization::setLoginPage('/login/');
fTimestamp::setDefaultTimezone('UTC');

// MySQL setup
$db  = new fDatabase('mysql', 'flb', 'flb', 'WelcomeFlbSinger', 'localhost');
//$db->enableDebugging(TRUE);
fORMDatabase::attach($db);
fORM::mapClassToTable('RecipeSearch', 'recipe_search');
fORMJSON::extend(); // adds the toJSON method to fActiveRecord
?>
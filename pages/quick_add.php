<?php

require_once('core.php');
require_api('authentication_api.php');
require_api('bug_api.php');
require_api('category_api.php');
require_api('config_api.php');
require_api('date_api.php');
require_api('email_api.php');
require_api('error_api.php');
require_api('form_api.php');
require_api('gpc_api.php');
require_api('helper_api.php');
require_api('project_api.php');

auth_ensure_user_authenticated();

form_security_validate('quick_add');

$f_project_id = gpc_get_int('project_id');
$f_category_id = gpc_get_int('category_id');
$f_summary = gpc_get_string('summary');
$f_description = gpc_get_string('description');
$f_due_date = gpc_get_string('due_date');
$f_due_time = gpc_get_string('due_time');

if (is_blank($f_summary)) {
    error_parameters(lang_get('summary'));
    trigger_error(ERROR_EMPTY_FIELD, ERROR);
}

if (is_blank($f_description)) {
    $f_description = '.';
}

$t_due_date_timestamp = null;
if (!is_blank($f_due_date) && !is_blank($f_due_time)) {
    $t_due_date_timestamp = strtotime($f_due_date . ' ' . $f_due_time);
}
if ($t_due_date_timestamp === false || $t_due_date_timestamp === null) {
    $t_due_date_timestamp = strtotime('today 12:00');
}

$t_user_id = auth_get_current_user_id();

global $g_project_override;
$g_project_override = $f_project_id;

access_ensure_project_level(config_get('report_bug_threshold'), $f_project_id);

$t_bug = new BugData;
$t_bug->project_id = $f_project_id;
$t_bug->category_id = $f_category_id;
$t_bug->reporter_id = $t_user_id;
$t_bug->handler_id = $t_user_id;
$t_bug->summary = $f_summary;
$t_bug->description = $f_description;
if (access_has_project_level(config_get('due_date_update_threshold'), $f_project_id)) {
    $t_bug->due_date = $t_due_date_timestamp;
}
$t_bug->priority = config_get('default_bug_priority');
$t_bug->severity = config_get('default_bug_severity');
$t_bug->reproducibility = config_get('default_bug_reproducibility');
$t_bug->status = config_get('bug_submit_status');
$t_bug->view_state = config_get('default_bug_view_status');
$t_bug->eta = config_get('default_bug_eta');
$t_bug->projection = config_get('default_bug_projection');

$t_issue_id = $t_bug->create();

email_bug_added($t_issue_id);

form_security_purge('quick_add');

print_header_redirect_view($t_issue_id);

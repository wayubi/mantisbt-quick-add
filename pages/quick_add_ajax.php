<?php

require_once('core.php');
require_api('authentication_api.php');
require_api('category_api.php');
require_api('config_api.php');
require_api('gpc_api.php');
require_api('project_api.php');

auth_ensure_user_authenticated();

$f_project_id = gpc_get_int('project_id');

$t_user_id = auth_get_current_user_id();

if (!access_has_project_level(config_get('report_bug_threshold', null, $t_user_id, $f_project_id), $f_project_id, $t_user_id)) {
    header('Content-Type: application/json');
    echo json_encode(array('categories' => array()));
    exit;
}

$t_categories = category_get_all_rows($f_project_id);

$t_cat_list = array();
foreach ($t_categories as $t_cat) {
    $t_cat_list[] = array(
        'id' => (int)$t_cat['id'],
        'name' => $t_cat['name'],
    );
}

header('Content-Type: application/json');
echo json_encode(array('categories' => $t_cat_list));

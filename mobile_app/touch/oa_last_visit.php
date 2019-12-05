<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/3/30
 * Time: 9:27
 */
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

$visit_user_id = (int)$yue_login_id;
$location_id   = $_GET['location_id']?$_GET['location_id']:101029001;

if($visit_user_id)
{
    $user_obj = POCO::singleton('pai_user_class');
    $role = $user_obj->check_role($visit_user_id);
    if($role == 'model')
    {
        $time = time();
        $oa_obj = POCO::singleton('pai_oa_import_order_class');
        $oa_obj->set_oa_last_visit($visit_user_id, $time, $location_id);
    }
}

?>
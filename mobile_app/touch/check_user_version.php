<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/8/19
 * Time: 9:56
 * ����û��汾������SendServer�ύ���û���Ϣ��������⣬�����ж�Ӧ�ķ��ͽŲ����ж��ŷ���
 */


include_once "../../poco_app_common.inc.php";

//��־
pai_log_class::add_log($_GET, 'submit_order_after', 'check_user_version');

$user_id = (int)$_REQUEST['yue_user_id'];

if($user_id)
{
    $user_obj = POCO::singleton('pai_user_class');
    $phone_num = $user_obj->get_phone_by_user_id($user_id);
    $date_time = date('Y-m-d');

    $sql_str = "INSERT IGNORE INTO pai_log_db.check_user_version_tbl(date_time, user_id, user_tel)
                VALUES ('{$date_time}', '{$user_id}', '{$phone_num}')";
    db_simple_getdata($sql_str,TRUE,101);
}





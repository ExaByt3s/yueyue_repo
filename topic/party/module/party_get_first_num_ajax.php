<?php
/** /* 
 * ��ȡʵʱ������λ����ѡ��λ������
 * 
 * author ����
 * 
 * 2015-3-26
 */

include_once("../party_common.inc.php");
require_once('/disk/data/htdocs232/poco/event/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
//ȡ��Ӧ�ò�������ʵ��
define("G_DB_GET_REALTIME_DATA",1);
$enroll_obj     = POCO::singleton('event_enroll_class');
$event_table_obj = POCO::singleton('event_table_class');

$ajax_status = 1;

$table_id = (int)$_INPUT['table_id'];
$event_id = (int)$_INPUT['event_id'];

if(empty($event_id))
{
    $ajax_status = 0;
}


if(empty($table_id))
{
    $ajax_status = 0;
}

if($ajax_status==1)
{
    //��ѯ������ѡ����������
    $first_total_count = $event_table_obj->get_table_num($event_id, $table_id);
    
    
    //��ѯ������ѡ������������
    $first_enroll_count = $enroll_obj->get_enroll_count_by_event_id_v2($event_id,array(0),$table_id);
    
    $can_enroll_first_count = ((int)$first_total_count)-((int)$first_enroll_count);
    if($can_enroll_first_count<=0)
    {
        $can_enroll_first_count = 0;
    }
    
}




$res_arr = array(
"ajax_status"=>$ajax_status,
"first_total_count"=>$first_total_count,
"first_enroll_count"=>$first_enroll_count,
"can_enroll_first_count"=>$can_enroll_first_count,
);
echo json_encode($res_arr);


?>
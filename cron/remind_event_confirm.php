<?php
/*
 * �����12Сʱ��������֯��ȷ�ϻ
 */
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$hour_12_later = date("Y-m-d H:i",time()-3600*12);

$details_obj = POCO::singleton ( 'event_details_class' );
$code_obj = POCO::singleton ( 'pai_activity_code_class' );

$sql = "select * from event_db.event_details_tbl where new_version=2 and event_status='0' and FROM_UNIXTIME(end_time,'%Y-%m-%d %H:%i')='{$hour_12_later}';";

$event_arr = db_simple_getdata ( $sql,false,23 );

//������֯��
foreach ( $event_arr as $val )
{
	$user_id = $val ['user_id'];
	$event_id = $val ['event_id'];
	$title = $val ['title'];
	$yue_id = get_relate_yue_id($user_id);
	
	if($yue_id)
	{
		$content = $title.'�ʱ�������24Сʱ���������κβ�����ϵͳ�Զ�ȷ�ϻ����';
		send_message_for_10002 ( $yue_id, $content );
	}
}


$date = date ( "Y-m-d H:i:s" );
echo '���ѳɹ�' . $date;
?>
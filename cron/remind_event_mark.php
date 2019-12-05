<?php
/*
 * ��ʱ��1Сʱ������֯��ǩ��
 * ��ʱ��1Сʱ���Ѳ�����ǩ��
 */
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$one_hour_later = date("Y-m-d H:i",time()+3600);

$details_obj = POCO::singleton ( 'event_details_class' );
$code_obj = POCO::singleton ( 'pai_activity_code_class' );
$table_obj = POCO::singleton ( 'event_table_class' );
$enroll_obj = POCO::singleton('event_enroll_class');

$sql = "select * from event_db.event_table_tbl where FROM_UNIXTIME(begin_time,'%Y-%m-%d %H:%i')='{$one_hour_later}' and is_open=1;";

$table_arr = db_simple_getdata ( $sql,false,23 );

//������֯��
foreach ( $table_arr as $val )
{
	$event_id = $val ['event_id'];
	$table_id = $val ['id'];
	
	$event_info = $details_obj->get_event_by_event_id ( $event_id );
	
	if($event_info['new_version']==2 && $event_info['event_status']==='0')
	{
	
		$yue_id = get_relate_yue_id($event_info['user_id']);	
		
		$table_arr = $table_obj->get_event_table_num_array($event_id);
		$num = $table_arr[$table_id];
	
		if($yue_id)
		{
			$content = $event_info['title'].'���'.$num.'����ʼʱ�����˺�����ɨһɨ��ά����߱�������������ǩ�������òŻ�ȷ�ϵ�����˻�';
			send_message_for_10002 ( $yue_id, $content );
		}
		
		$where_str = "table_id = {$table_id} and event_id={$event_id} and pay_status=1";
		
		$enroll_list = $enroll_obj->get_enroll_list($where_str, false, '0,1000');
		
		//���ֲ�����
		foreach($enroll_list as $code_val)
		{
			$enroll_user_id = get_relate_yue_id($code_val['user_id']);
			if($enroll_user_id)
			{
				$content = $event_info['title'].'���'.$num.'������ǰ�����˳�ʾ��ά�����֯�߽���ǩ��ȷ�ϡ�';
				send_message_for_10002 ( $enroll_user_id, $content );
			}
		}
	}
}


$date = date ( "Y-m-d H:i:s" );
echo '����ǩ���ɹ�' . $date;
?>
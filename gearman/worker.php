<?php
/**
 * ��Ϣ����worker
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

//��־ 
pai_log_class::add_log(array(), 'worker started', 'worker');

//��ȡworker���� 
$pai_gearman_obj = POCO::singleton('pai_gearman_class');
$worker_obj = $pai_gearman_obj->get_gearman_worker_obj();
if( is_null($worker_obj) )
{
	die('get_gearman_worker_obj fail!');
}

//ִ��
$worker_obj->addFunction('receive_cmd', array('pai_gearman_class', 'receive_cmd'));

//����ʱ��
$run_time = time();
while( @$worker_obj->work() || $worker_obj->returnCode()==GEARMAN_IO_WAIT || $worker_obj->returnCode()==GEARMAN_NO_JOBS )
{
	if( $worker_obj->returnCode()==GEARMAN_SUCCESS ) continue;
	
	if( !@$worker_obj->wait() )
	{
		if( $worker_obj->returnCode()==GEARMAN_NO_ACTIVE_FDS )
		{
			sleep(5);
			continue;
		}
		break;
	}
	else
	{
		$cur_time = time();
		if( $cur_time-$run_time>60 ) break;
	}
}

<?php

/**
 * ��ȡԼ�ĵ���Ϣ
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(!$yue_login_id)
{
	$output_arr['code'] = 500;
	$output_arr['msg'] = 'û��Ȩ�ޣ����ȵ�¼';
	mobile_output($output_arr,false);
	exit;
}


/**
 * ҳ����ղ���
 */
$date_id = intval($_INPUT['date_id']);

$ret = get_date_by_date_id($date_id);

if($ret['from_date_id']!=$yue_login_id)
{
	$output_arr['code'] = 404;
	$output_arr['msg'] = '�����ڸö���';
	mobile_output($output_arr,false);
	exit;
}

if(!$ret['date_address'])
{	
	$ret['date_address'] = mb_convert_encoding('����', 'gbk','utf-8');		
}

if(!$ret['date_type'])
{
	$ret['date_type'] = mb_convert_encoding('����', 'gbk','utf-8');		
}

$output_arr['code'] = count($ret)?1:0;
$output_arr['data'] = $ret;
$output_arr['msg'] = 'get date info';

if($yue_login_id == 100029)
{
	//sleep(11);	
}

mobile_output($output_arr,false);

?>
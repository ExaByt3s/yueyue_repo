<?php 
/**
* @file sync_wx_user_base_info.php
* @synopsis ΢���û�������Ϣͬ��������
* @author wuhy@yueus.com
* @version null
* @date 2015-07-22
 */

ignore_user_abort(true);
set_time_limit(3600);//��ʱʱ��, ����Ƶ�� Ӧ����Ϊ���ʵ�ֵ, ����������̼߳���Ӵ�
ini_set('memory_limit', '512M');

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$op = trim($_INPUT['op']);
if( $op!='run')
{
	die('op error!');
}

$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');

$week_number = date('w', time());

$bind_list = $weixin_helper_obj->get_bind_list_by_search('', false, '0,99999', 'bind_id DESC', 'bind_id');
$bind_id_arr = array_map('array_shift', $bind_list);

foreach( $bind_id_arr as $bind_id )
{
    $rst = $weixin_helper_obj->sync_user_base_info($bind_id,"WEEKDAY(FROM_UNIXTIME(subscribe_time))={$week_number}","0,9999999999");
    echo $bind_id.' ΢���û���Ϣͬ������� '. var_export($rst, true) . ' ' . date("Y-m-d H:i:s")."\r\n";
}

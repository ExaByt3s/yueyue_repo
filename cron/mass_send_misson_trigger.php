<?php 
/**
* @file mass_send_misson_trigger.php
* @Synopsis ΢��Ⱥ�����񴥷���
* @author wuhy@yueus.com
* @version null
* @date 2015-07-17
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

//��ȡ�������������б�
$mission_list = $weixin_helper_obj->get_mass_send_mission_list(0, false, 'status=0 AND bind_id=2', 'mission_id ASC', '0,1');
if( !is_array($mission_list) || empty($mission_list) )
{
	echo 'û�С�������������';
	echo date("Y-m-d H:i:s");
	exit();
}

//����Ƿ����С������С�������
$mission_count = $weixin_helper_obj->get_mass_send_mission_list(0, true, 'status=1 AND bind_id=2');
if( $mission_count>0 )
{
	echo '���С������С�������';
	echo date("Y-m-d H:i:s");
	exit();
}

$mission_info = $mission_list[0];
$rst = $weixin_helper_obj->mass_send($mission_info);
echo '΢��Ⱥ�������'. var_export($rst, true) . ' ' . date("Y-m-d H:i:s");

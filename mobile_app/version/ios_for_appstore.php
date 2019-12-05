<?php
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$osver  	= $_REQUEST['osver'];
$appver 	= $_REQUEST['appver'];
$modelver	= $_REQUEST['modelver'];
$iswifi 	= $_REQUEST['iswifi'];
$user_id    = $_REQUEST['uid'];

$pai_obj    = POCO::singleton('pai_user_class');
$role       = $pai_obj->check_role($user_id);

$latest_version		= '3.2.10';   //���°汾
$latest_down_url	= 'https://itunes.apple.com/cn/app/yueyue/id935185009?l=zh&ls=1&mt=8'; //�汾���ص�ַ
//$latest_notice		= '��ҳȫ�¸ı����������Ƹ����||������Ӱ���񣬸�����Ӱ��ѵ����ױʦ��Ӱ����פ';

$latest_notice		= '1�������ղع��ܣ��̼�/��������ղ��ˣ���ȥ����ղذ�||2�������������ܣ����Բ鿴����Ĵ�����Ϣ���������Ż�||3������ϸ���Ż���������ҳ������ɸѡ��������ͨ���е�';

$lowest_version     = '3.2.10';
$lowest_notice      = '�����°汾���޸���ios9��һЩ���⣺||��ʱ���շ����̻ظ���Ϣ���޸�����΢�ŵ�ƽ̨���񣬵�����ϸ��¡�';

$array_result['result']     = "0000";         
$array_result['update']     = 0;     


//if($user_id == 100008) {
	if(version_compare($appver, $lowest_version, '<'))
	{
		//�汾������Ͱ汾��ǿ�Ƹ���
		$array_result['result']     = "0000";
		$array_result['update']     = 2;
		$array_result['version']    = $latest_version;
		$array_result['app_url']    = $latest_down_url;
		$array_result['detail']     = iconv('GBK', 'UTF-8', $latest_notice);
	}elseif(version_compare($appver, $lowest_version, '>=') && version_compare($appver, $latest_version, '<')){
		//�汾�������°汾��������Ͱ汾���������
		$array_result['result']     = "0000";
		$array_result['update']     = 1;
		$array_result['version']    = $latest_version;
		$array_result['app_url']    = $latest_down_url;
		$array_result['detail']     = iconv('GBK', 'UTF-8', $lowest_notice);
	}else{
		//����Ҫ����
		$array_result['result']     = "0000";
		$array_result['update']     = 0;
	}
/*}else{
	//����Ҫ����
	$array_result['result']     = "0000";
	$array_result['update']     = 0;
}*/


echo json_encode($array_result);
?>
<?php
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$osver		= $_REQUEST['osver'];
$appver		= $_REQUEST['appver'];
$modelver	= $_REQUEST['modelver'];
$iswifi		= $_REQUEST['iswifi'];
$user_id    = $_REQUEST['uid'];

//�汾������ȥ������
$array_str  = explode('_', $appver);
$appver     = $array_str[0];

//$pai_obj = POCO::singleton('pai_user_class');
//$role = $pai_obj->check_role($user_id);


$latest_version		= '3.2.0';   //���°汾
$latest_down_url	= 'http://c.poco.cn/y201510281703.apk'; //�汾���ص�ַ


$latest_notice		= '1�������ղع��ܣ��̼�/��������ղ��ˣ���ȥ����ղذ�
2�������������ܣ����Բ鿴����Ĵ�����Ϣ���������Ż�
3������ϸ���Ż���������ҳ������ɸѡ��������ͨ���е�';

$lowest_version     = '3.2.0';
$lowest_notice      = '�㻹���Լ���ʹ�ã���������µ����°汾';

$array_result['result']     = "0000";
$array_result['update']     = 0;


//if($user_id == 100008) {
    if ($appver) {
        if (version_compare($appver, $lowest_version, '<')) {
            //�汾������Ͱ汾��ǿ�Ƹ���
            $array_result['result'] = "0000";
            $array_result['update'] = 2;
            $array_result['version'] = $lowest_version;
            $array_result['app_url'] = $latest_down_url;
            $array_result['detail'] = iconv('GBK', 'UTF-8', $latest_notice);
        } elseif (version_compare($appver, $lowest_version, '>=') && version_compare($appver, $latest_version, '<')) {
            //�汾�������°汾��������Ͱ汾���������
            $array_result['result'] = "0000";
            $array_result['update'] = 1;
            $array_result['version'] = $lowest_version;
            $array_result['app_url'] = $latest_down_url;
            $array_result['detail'] = iconv('GBK', 'UTF-8', $lowest_notice);
        } else {
            //����Ҫ����
            $array_result['result'] = "0000";
            $array_result['update'] = 0;
        }
    }
/*}else{
    //����Ҫ����
    $array_result['result'] = "0000";
    $array_result['update'] = 0;
}*/

echo json_encode($array_result);
?>
<?php
$osver      = $_REQUEST['osver'];
$appver     = $_REQUEST['appver'];
$modelver	= $_REQUEST['modelver'];
$iswifi     = $_REQUEST['iswifi'];
$user_id    = $_REQUEST['uid'];

$latest_version		= '3.2.10';   //���°汾
$latest_down_url	= 'http://yp.yueus.com/mall/user/topic/index.php?topic_id=762&online=1'; //�汾���ص�ַ
//$latest_down_url = 'itms-services:///?action=download-manifest&url=https%3a%2f%2fypays.yueus.com%2frelease%2fios%2fyueyue_dist_ent_v18.plist';

$latest_notice		= '1�������ղع��ܣ��̼�/��������ղ��ˣ���ȥ����ղذ�||2�������������ܣ����Բ鿴����Ĵ�����Ϣ���������Ż�||3������ϸ���Ż���������ҳ������ɸѡ��������ͨ���е�';

$lowest_version     = '3.2.10';
$lowest_notice      = '�㻹���Լ���ʹ�ã���������µ����°汾';

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
<?php
$osver  = $_REQUEST['osver'];
$appver = $_REQUEST['appver'];
$iswifi = $_REQUEST['iswifi'];


$latest_version = '1.0.3';   //���°汾
$down_url       = 'http://app.yueus.com/'; //�汾���ص�ַ
$notice         = 'ϵͳ������ʾ��||1��Լ����Ϣ�������Ͻ��˸�����Ϣ���������ѯ��||��  2������΢��֧�����ܡ������¼��չ��ܡ�||�� 3������۸�λ��������Ϊ xԪ/2Сʱ �� xԪ/4Сʱ��'; //

if($appver < $latest_version)
{
    $array_result['result']     = "0000";         
    $array_result['update']     = 1;            
    $array_result['version']    = $latest_version;
    $array_result['app_url']    = $down_url;
    $array_result['detail']     = iconv('GBK', 'UTF-8', $notice);    
}else{
    $array_result['result']     = "0000";         
    $array_result['update']     = 0;              
}

/**
$array_result['result']     = "0000";         //Ĭ�ϳɹ�����
$array_result['update']     = 0;            //0��1��Ϊ1��ʾ�ͻ�����Ҫ��ʾ����
$array_result['version']    = '1.0';           //version��app_url��Ӧ�İ汾�ţ���ȷ����app_url��Ӧ�İ汾��һ�£������һֱ��ʾ����
$array_result['app_url']    = 'http://y.poco.cn/a/';           //app_url����׿ֱ����������ص�ַ��ios������ҳ�������
$array_result['detail']     = 'BUG�޸�';           //detail��������ʾ��Ϣ
$array_result['detail']     = iconv('GBK', 'UTF-8', $array_result['detail']);
**/



echo json_encode($array_result);
?>

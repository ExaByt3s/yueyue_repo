<?php
$osver  = $_REQUEST['osver'];
$appver = $_REQUEST['appver'];
$iswifi = $_REQUEST['iswifi'];

$request_str = serialize($_REQUEST) ;
$sql_str = "INSERT INTO pai_log_db.update_log_tbl(request_str) VALUES ('{$request_str}')";
db_simple_getdata($sql_str, TRUE, 101);

$latest_version = '1.0.5';   //���°汾
$down_url       = 'http://y.poco.cn/a/y201502121619.apk'; //�汾���ص�ַ
$notice         = 'ϵͳ������ʾ��
1��Լ����Ϣ�������Ͻ��˸�����Ϣ���������ѯ��
2������΢��֧�����ܡ������¼��չ��ܡ�
3������۸�λ��������Ϊ xԪ/2Сʱ �� xԪ/4Сʱ��'; //

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
/**    $array_result['result']     = "0000";         
    $array_result['update']     = 0;    
**/
/**
$array_result['result']     = "0000";         //Ĭ�ϳɹ�����
$array_result['update']     = 0;            //0��1��Ϊ1��ʾ�ͻ�����Ҫ��ʾ����
$array_result['version']    = '1.0';           //version��app_url��Ӧ�İ汾�ţ���ȷ����app_url��Ӧ�İ汾��һ�£������һֱ��ʾ����
$array_result['app_url']    = 'http://y.poco.cn/a/';           //app_url����׿ֱ����������ص�ַ��ios������ҳ�������
$array_result['detail']     = 'BUG�޸�';           //detail��������ʾ��Ϣ
$array_result['detail']     = iconv('GBK', 'UTF-8', $array_result['detail']);
**/

if($_REQUEST['uid'] == 100008)
{
    $latest_notice = '1����ҳȫ�¸İ棬��Ӱʦ���Է����Լ������ˡ�
2��ģ�ؿ��������Ƽ��Լ������뱨���������ᡣ
3�������Ż���';
    //�汾������Ͱ汾��ǿ�Ƹ���
    $array_result['result']     = "0000";
    $array_result['update']     = 2;
    $array_result['version']    = '1.1.0';
    $array_result['app_url']    = 'http://y.poco.cn/a';
    $array_result['detail']     = iconv('GBK', 'UTF-8', $latest_notice);
}

echo json_encode($array_result);
?>

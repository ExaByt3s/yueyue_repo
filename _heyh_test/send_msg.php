<?php
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$content = '�ף�˽���û�����ɧ����Ϣ��ô�죿ԼԼ�°��Ƴ���һ�����Ρ����ܣ�������ҳ������Ļ���Ͻ�ͼ�Σ����롰������Ϣ���á���ѡ�����δ�����Ϣ������������ɧ����Ϣ�������£��°�������app.yueus.com лл��';
//send_message_for_10002(100008, $content);  

$sql_str = "SELECT `user_id` FROM `pai_db`.`pai_user_tbl` WHERE role='model'";
$result  = db_simple_getdata($sql_str, FALSE, 101);
foreach($result AS $key=>$val)
{
    send_message_for_10002($val[user_id], $content);    
}

?>


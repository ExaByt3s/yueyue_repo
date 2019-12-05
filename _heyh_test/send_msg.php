<?php
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$content = '亲：私聊用户发布骚扰信息怎么办？约约新版推出“一键屏蔽”功能，在聊天页面点击屏幕右上角图形，进入“聊天信息设置”，选择“屏蔽此人消息”，立即屏蔽骚扰信息！快试下！新版升级：app.yueus.com 谢谢！';
//send_message_for_10002(100008, $content);  

$sql_str = "SELECT `user_id` FROM `pai_db`.`pai_user_tbl` WHERE role='model'";
$result  = db_simple_getdata($sql_str, FALSE, 101);
foreach($result AS $key=>$val)
{
    send_message_for_10002($val[user_id], $content);    
}

?>


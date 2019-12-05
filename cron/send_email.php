<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/23
 * Time: 13:55
 */
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once ('/disk/data/htdocs232/poco/pai/system_service/include/class.email.inc.php');

$sql_str = "SELECT * FROM pai_email_db.email_send_queue_tbl LIMIT 0, 100";
$result = db_simple_getdata($sql_str, FALSE, 101);

//$result[0]['email_title'] = '约约提醒你有一个新的生意机会';
//$result[0]['email_add']   = "yaohua_he@163.com";
//$result[0]['email_html']   = ' 约约提醒你有一个新的生意机会。<br /><br />koko需要一个摄影棚<br />需求如下：<br />你希望影棚的面积有多大？<br />A、50平米以下<br />你需要拍摄的类型是什么？<br />B、商业模特<br />你希望影棚的背景是怎么样的？<br />B、实景<br />你希望影棚的灯光设备是怎样的？<br />A、基础灯光配置<br />B、智能灯光系统<br />你需要拍摄的时间是？<br />2015-4-23<br />5小时<br />是不是可以接受其他时间？<br />A、接受<br />你对影棚配套的要求<br />C、WIFI<br />D、停车场<br />E、无<br /><br />点击 <a href="http://www.yueus.com/task/lead_detail.php?lead_id=8769" target="_blank">进入查看详情</a>';

foreach($result AS $key=>$val)
{

    //##########################################
    $smtpserver         = "mail.poco.cn";//SMTP服务器
    $smtpserverport     =25;//SMTP服务器端口
    $smtpusermail       = "no-reply@yueus.com";//SMTP服务器的用户邮箱
    $smtpuser           = "no-reply@yueus.com";//SMTP服务器的用户帐号
    $smtppass           = "83jcrpt84d";//SMTP服务器的用户密码
    $mailsubject        = $val['email_title'];//邮件主题
    $smtpemailto        = $val['email_add'];//发送给谁
    $mailbody           = $val['email_html'];//邮件内容
    $mailtype           = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
    ##########################################

    $smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
    $smtp->debug = FALSE;//是否显示发送的调试信息
    $smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);

    $sql_str = "INSERT INTO pai_email_db.have_send_email_log_tbl(email_add, email_title, email_html, send_time)
                VALUES (:x_email_add, :x_email_title, :x_email_html, :x_send_time)";
    sqlSetParam($sql_str, 'x_email_add', $val['email_add']);
    sqlSetParam($sql_str, 'x_email_title', $val['email_title']);
    sqlSetParam($sql_str, 'x_email_html', $val['email_html']);
    sqlSetParam($sql_str, 'x_send_time', date('Y-m-d H:i:s'));
    db_simple_getdata($sql_str, TRUE, 101);

    $sql_str = "DELETE FROM pai_email_db.email_send_queue_tbl WHERE id={$val['id']}";
    $result = db_simple_getdata($sql_str, TRUE, 101);
}

?>

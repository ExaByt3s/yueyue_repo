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

//$result[0]['email_title'] = 'ԼԼ��������һ���µ��������';
//$result[0]['email_add']   = "yaohua_he@163.com";
//$result[0]['email_html']   = ' ԼԼ��������һ���µ�������ᡣ<br /><br />koko��Ҫһ����Ӱ��<br />�������£�<br />��ϣ��Ӱ�������ж��<br />A��50ƽ������<br />����Ҫ�����������ʲô��<br />B����ҵģ��<br />��ϣ��Ӱ��ı�������ô���ģ�<br />B��ʵ��<br />��ϣ��Ӱ��ĵƹ��豸�������ģ�<br />A�������ƹ�����<br />B�����ܵƹ�ϵͳ<br />����Ҫ�����ʱ���ǣ�<br />2015-4-23<br />5Сʱ<br />�ǲ��ǿ��Խ�������ʱ�䣿<br />A������<br />���Ӱ�����׵�Ҫ��<br />C��WIFI<br />D��ͣ����<br />E����<br /><br />��� <a href="http://www.yueus.com/task/lead_detail.php?lead_id=8769" target="_blank">����鿴����</a>';

foreach($result AS $key=>$val)
{

    //##########################################
    $smtpserver         = "mail.poco.cn";//SMTP������
    $smtpserverport     =25;//SMTP�������˿�
    $smtpusermail       = "no-reply@yueus.com";//SMTP���������û�����
    $smtpuser           = "no-reply@yueus.com";//SMTP���������û��ʺ�
    $smtppass           = "83jcrpt84d";//SMTP���������û�����
    $mailsubject        = $val['email_title'];//�ʼ�����
    $smtpemailto        = $val['email_add'];//���͸�˭
    $mailbody           = $val['email_html'];//�ʼ�����
    $mailtype           = "HTML";//�ʼ���ʽ��HTML/TXT��,TXTΪ�ı��ʼ�
    ##########################################

    $smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//�������һ��true�Ǳ�ʾʹ�������֤,����ʹ�������֤.
    $smtp->debug = FALSE;//�Ƿ���ʾ���͵ĵ�����Ϣ
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

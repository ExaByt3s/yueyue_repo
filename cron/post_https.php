<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/22
 * Time: 21:06
 */
include_once ("../poco_app_common.inc.php");

//模拟登陆，测试HTTPS登陆情况
auto_login();

//检查全文
check_fulltest();

//检查图片上传
check_imgup();

function auto_login()
{

    $login_url = 'https://ypays.yueus.com/app/login.php';

    $param = array('phone' =>13560000000,'pwd' => '123456');
    $post_data = array(
        'version'   => '1.0.6',
        'os_type'   => 'ios',
        'ctime'     => time(),
        'app_name'  => 'poco_yuepai_android',
        'sign_code' => substr(md5('poco_'.json_encode($param).'_app'), 5, -8),
        'is_enc'    => 0,
        'param'     => $param,
    );
    $post_data = json_encode($post_data);
    $post_data = iconv('GBK', 'UTF-8', $post_data);

    $post_fields = 'req=' . $post_data;

    $ch = curl_init($login_url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $arr = curl_exec($ch);
    curl_close($ch);

    $date_time = date('Y-m-d H:i:s', time());
    $array_result = json_decode($arr, TRUE);

    $arr_log = iconv('UTF-8', 'GBK', $arr);


    if($array_result[code] == 200)
    {
        $sql_str = "INSERT INTO `monitor_service_for_yueyue_db`.`mon_log_tbl`(log_time, task_id, mon_type, server_ip, server_port,ok_status, response_time_ms, log_message, log_content)
                VALUES ('{$date_time}', 9465, 'HTTP', 'None',0,'OK', 0, 'HTTPS登陆正常', '{$arr_log}')";
    }else{
        $sql_str = "INSERT INTO `monitor_service_for_yueyue_db`.`mon_log_tbl`(log_time, task_id, mon_type, server_ip, server_port,ok_status, response_time_ms, log_message, log_content)
                VALUES ('{$date_time}', 9465, 'HTTP', 'None',0,'ERROR', 0, 'HTTPS登陆错误', '{$arr_log}')";
    }
    db_simple_getdata($sql_str, TRUE, 6);
}

function check_fulltest()
{
    include_once ('../event_act.php');
    $event_result = event_fulltext_search('','','',TRUE);

    $date_time = date('Y-m-d H:i:s', time());

    if(gettype($event_result) == 'integer')
    {
        $sql_str = "INSERT INTO `monitor_service_for_yueyue_db`.`mon_log_tbl`(log_time, task_id, mon_type, server_ip, server_port,ok_status, response_time_ms, log_message, log_content)
                VALUES ('{$date_time}', 9467, 'HTTP', 'None',0,'OK', 0, '全文正常', '{$event_result}')";
    }else{
        $sql_str = "INSERT INTO `monitor_service_for_yueyue_db`.`mon_log_tbl`(log_time, task_id, mon_type, server_ip, server_port,ok_status, response_time_ms, log_message, log_content)
                VALUES ('{$date_time}', 9467, 'HTTP', 'None',0,'ERROR', 0, '全文出错', '{$event_result}')";
    }
    db_simple_getdata($sql_str, TRUE, 6);
}

function check_imgup()
{
    $url = 'http://sendmedia-wifi.yueus.com:8079/upload.cgi';
    //$data ['data'] = '{"poco_id":"100008","opus":"http://www.poco.cn/module/images/prew1_new.jpg"}';

    $data ['poco_id'] = 100008;
    $data ['opus'] = '@/disk/data/htdocs232/poco/pai/prew1_new.jpg';

    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_POST, 1 );
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch, CURLOPT_COOKIE, $matches [1] );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
    $result = curl_exec ( $ch );
    curl_close ( $ch );

    $date_time = date('Y-m-d H:i:s', time());

    $array_result = json_decode($result, TRUE);
    if($array_result[code] == 0)
    {
        $sql_str = "INSERT INTO `monitor_service_for_yueyue_db`.`mon_log_tbl`(log_time, task_id, mon_type, server_ip, server_port,ok_status, response_time_ms, log_message, log_content)
                VALUES ('{$date_time}', 9468, 'HTTP', 'None',0,'OK', 0, '图片上传正常', '{$result}')";
    }else{
        $sql_str = "INSERT INTO `monitor_service_for_yueyue_db`.`mon_log_tbl`(log_time, task_id, mon_type, server_ip, server_port,ok_status, response_time_ms, log_message, log_content)
                VALUES ('{$date_time}', 9468, 'HTTP', 'None',0,'ERROR', 0, '图片上传错误', '{$result}')";
    }
    db_simple_getdata($sql_str, TRUE, 6);
}

?>
<?php
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/MobileAPP/key.inc.php');

$send_id    = $_GET['from_send_id'];
$sendee_id  = $_GET['to_send_id'];

$code = 0;
$msg  = 'Õý³£';
        
if($send_id && $sendee_id)
{
    $sql_str = "SELECT * FROM pai_log_db.pai_date_info_log_tbl 
                WHERE from_send_id=$send_id AND to_send_id=$sendee_id 
                GROUP BY date_id ORDER BY id DESC";
    $result = db_simple_getdata($sql_str, FALSE, 101);
    foreach($result AS $key=>$val)
    {
        $result_array['value'][$key]['str']         = iconv('gbk', 'utf-8', $val['card_title']);
        $result_array['value'][$key]['url']        = 'http://yp.yueus.com/mobile/app?from_app=1#mine/consider_details_camera/' . $val['date_id'];
        $result_array['value'][$key]['wifi_url']   = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#mine/consider_details_camera/' . $val['date_id'];
    }
}

json_msg($code, $msg, $result_array);

?>
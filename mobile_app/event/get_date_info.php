<?php
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/MobileAPP/key.inc.php');

$send_id    = $_REQUEST['from_send_id'];
$sendee_id  = $_REQUEST['to_send_id'];

$code = 0;
$msg  = '����';
        
if($send_id && $sendee_id)
{
    $sql_str = "SELECT tt.date_id AS date_id, tt.card_title AS card_title, tt.add_time AS add_time FROM 
(SELECT date_id, card_title, add_time FROM pai_log_db.pai_date_info_log_tbl WHERE from_send_id=$sendee_id AND to_send_id=$send_id ORDER BY date_id, id DESC) AS tt GROUP BY date_id;";
//echo $sql_str;
    $result = db_simple_getdata($sql_str, FALSE, 101);
    $result_array = '';
    $num = 0;
    foreach($result AS $key=>$val)
    {
        if($val['card_title'] == '�鿴Լ������') continue;
        if($val['card_title'] == '�ȴ�ģ��ȷ��') continue;
        if($val['card_title'] == '�ȴ�ģ�ؽ���') continue;
        if($val['card_title'] == 'ǿ���˿�') continue;
        
        if($val['card_title'] == 'ȷ�����')
        {
            $add_time = strtotime($val['add_time']);
            if(time()-$add_time < 5) continue;
        }
        
        if($val['date_id'])
        {
            if(pai_check_date_stauts($val['date_id']))
            {
                $result_array['value'][$num]['str']         = iconv('gbk', 'utf-8', $val['card_title']);
                $result_array['value'][$num]['url']        = 'http://yp.yueus.com/mobile/app?from_app=1#mine/consider_details_camera/' . $val['date_id'];
                $result_array['value'][$num]['wifi_url']   = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#mine/consider_details_camera/' . $val['date_id'];
                $num++;
            }
        }
    }
}

function pai_check_date_stauts($date_id)
{
    //return TRUE;
    if($date_id)
    {
        $sql_str = "SELECT * FROM event_db.event_date_tbl 
                    WHERE date_id = $date_id";
        $result = db_simple_getdata($sql_str, TRUE);
        if($result['date_status'] == 'cancel' || $result['date_status'] == 'cancel_date' || $result['date_status'] == 'refund')
        {
            return FALSE;
        }
        return TRUE;
    }
    return TRUE;
}

//print_r($result_array);
json_msg($code, $msg, $result_array);

?>
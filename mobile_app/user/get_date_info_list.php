<?php

//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id        = $client_data['data']['param']['user_id'];

$sql_str = "SELECT tt.date_id AS date_id, tt.from_send_id AS from_send_id, tt.card_title AS card_title, tt.add_time AS add_time FROM 
            (SELECT date_id, from_send_id, card_title, add_time FROM pai_log_db.pai_date_info_log_tbl WHERE to_send_id=$user_id ORDER BY date_id, id DESC) AS tt GROUP BY date_id ORDER BY add_time DESC LIMIT 0,50;";
//    echo $sql_str;
$result = db_simple_getdata($sql_str, FALSE, 101);
//    $num = 0;
foreach($result AS $key=>$val)
{
    if($val['card_title'] == '查看约拍详情') continue;
    if($val['card_title'] == '等待模特接受') continue;
    if($val['card_title'] == '等待模特确认') continue;


    if($val['date_id'])
    {
        $result_array = array();
        if(pai_check_date_stauts($val['date_id']))
        {
            //$result_array[$num]['user_id']    = $val['from_send_id'];
            $result_array['str']        = $val['card_title'];
            $result_array['url']        = 'http://yp.yueus.com/mobile/app?from_app=1#mine/consider_details_camera/' . $val['date_id'];
            $result_array['wifi_url']   = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#mine/consider_details_camera/' . $val['date_id'];
//                $num++;

            $list_user[$val['from_send_id']][]  = $result_array;                
        }
    }
}
foreach($list_user AS $key=>$val)
{
    $data_list['user'] = $key;
    $data_list['msg_list'] = $val;
    
    $options['data'][] = $data_list;
}

$cp->output($options);

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
?>
<?php

//define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

$user_id        = $client_data['data']['param']['user_id'];
$type           = $client_data['data']['param']['type'];
$limit          = $client_data['data']['param']['limit'];

$data['mid'] = "1220092";
$data['nickname'] = get_user_nickname_by_user_id($user_id);
$data['icon'] = get_user_icon($user_id);
$data['user_id'] = "{$user_id}";
$data['title'] = "二维码签到券";
$ticket_arr = get_act_ticket(false,$user_id,$limit);

$ticket_key = 0;

foreach($ticket_arr as $k=>$val)
{
    $new_ticket_arr[$ticket_key]['name'] = $val['event_info']['title'];

    if($val['event_info']['type_icon']=='yuepai_app')
    {
        $new_ticket_arr[$ticket_key]['type'] = "2";
        $new_ticket_arr[$ticket_key]['time'] = "时间:".$val['event_info']['start_time'];
    }
    else
    {
        $new_ticket_arr[$ticket_key]['type'] = "1";
        $new_ticket_arr[$ticket_key]['time'] = "活动时间:".$val['event_info']['start_time'];
    }


    foreach($val['code_arr'] as $bk=>$bval)
    {
        $code = $bval['code'];
        $event_id = $bval['event_id'];
        $enroll_id = $bval['enroll_id'];
        $hash = qrcode_hash ( $event_id, $enroll_id, $code );	
        $jump_url = "http://yp.yueus.com/mobile/action/check_qrcode.php?event_id={$event_id}&enroll_id={$enroll_id}&code={$code}&hash={$hash}";

        $num = $bk+1;
        $new_code_arr[$bk]['name'] = "{$num}.数字密码：";
        $new_code_arr[$bk]['number_code'] = "{$code}";
        $new_code_arr[$bk]['qrcode'] = $jump_url;
    }

    $new_ticket_arr[$ticket_key]['tickets'] = $new_code_arr ? $new_code_arr : array();

    $ticket_key++;
    unset($new_code_arr);
}

$data['list'] = $new_ticket_arr ? $new_ticket_arr :array();

//print_r($data_array);
$options['data'] = $data ?  $data : array();

$cp->output($options);
?>
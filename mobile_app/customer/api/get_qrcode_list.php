<?php

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once("../../protocol_common.inc.php");

// 获取客户端的数据
$cp = new poco_communication_protocol_class();
// 获取用户的授权信息
$client_data = $cp->get_input();

$user_id = $client_data['data']['param']['user_id'];
$access_token = $client_data['data']['param']['access_token'];
$type = $client_data['data']['param']['type'];
$limit = $client_data['data']['param']['limit'];
if (empty($limit) || !preg_match('/^\d+,{1}\d+$/', $limit)) {
    $page = intval($client_data['data']['param']['page']);  // 第几页
    $rows = intval($client_data['data']['param']['rows']); // 每页限制条数(5-100之间)
    
    $page = $page < 1 ? 1 : $page;
    $rows = $rows < 5 ? 5 : ( $rows > 100 ? 100 : $rows);
    $limit = ($page - 1) * $rows . ',' . $rows;
}

$code_obj = POCO::singleton('pai_activity_code_class');
$ret = $cp->check_access_token_is_matching($user_id, $access_token);

if ($ret) {
    $data['mid'] = "1220092";
    $data['nickname'] = get_user_nickname_by_user_id($user_id);
    $data['icon'] = get_user_icon($user_id);
    $data['user_id'] = "{$user_id}";
    $data['title'] = "二维码签到券";

    $where_str = "enroll_user_id={$user_id}";
    $ticket_arr = $code_obj->get_new_act_ticket($user_id, $limit);

    $ticket_key = 0;

    foreach ($ticket_arr as $k => $val) {
        $new_ticket_arr[$ticket_key]['name'] = $val['title'];

        if ($val['type'] == 'mall_code') {
            $new_ticket_arr[$ticket_key]['type'] = "2";
            $new_ticket_arr[$ticket_key]['time'] = "时间:" . $val['start_time'];
        } else {
            $new_ticket_arr[$ticket_key]['type'] = "1";
            $new_ticket_arr[$ticket_key]['time'] = "活动时间:" . $val['start_time'];
        }


        foreach ($val['code_arr'] as $bk => $bval) {
            $code = $bval['code'];
            $event_id = $bval['event_id'];
            $enroll_id = $bval['enroll_id'];
            $hash = qrcode_hash($event_id, $enroll_id, $code);
            $jump_url = "http://yp.yueus.com/mobile/action/check_qrcode.php?event_id={$event_id}&enroll_id={$enroll_id}&code={$code}&hash={$hash}";

            $num = $bk + 1;
            $new_code_arr[$bk]['name'] = "{$num}.数字密码：";
            $new_code_arr[$bk]['number_code'] = "{$code}";
            $new_code_arr[$bk]['qrcode'] = $jump_url;
        }

        $new_ticket_arr[$ticket_key]['tickets'] = $new_code_arr ? $new_code_arr : array();

        $ticket_key++;
        unset($new_code_arr);
    }

    $data['list'] = $new_ticket_arr ? $new_ticket_arr : array();
}

//print_r($data_array);
$options['data'] = $data ? $data : array();

$cp->output($options);
?>
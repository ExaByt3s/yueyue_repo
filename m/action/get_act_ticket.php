<?

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

/*
 * 获取活动券
 * @param bool $b_select_count 
 * @param int $user_id
 */
 $ret = get_act_ticket(false,$yue_login_id);

 $_ticket_list = $ret;

// 数据构造
// ------------------------------
$ticket_list = array();

foreach ($_ticket_list as $key => $ticket_info) {
    $icon = $ticket_info['event_info']['type_icon'];
    if($icon == "yuepai"){
        $icon = 'hd';
    }elseif($icon == "yuepai_app"){
        $icon = 'time';
    }
	
	foreach($ticket_info['code_arr'] as $tk=>$val){
		$new_ticket[$tk]['code'] = $val['code'];
		$new_ticket[$tk]['qr_code'] = $ticket_info['qr_code'][$tk];
	}
	

    $tmp_arr = array(
        'ticket' => $new_ticket,
        'time' => (string)$ticket_info['event_info']['start_time'],
        'title' => (string)$ticket_info['event_info']['title'],
        'icon' => (string)$icon,
    );

    $ticket_list['list'][] = $tmp_arr;

	unset($new_ticket);

}
 
 mobile_output($ticket_list,false);
?>
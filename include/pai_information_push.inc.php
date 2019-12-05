<?php
class pai_information_push extends POCO_TDG
{
	/**
	 * 构造函数
	 *
	 */
	public function __construct() {
	   	$this->setServerId ( 101 );
		$this->setDBName ( 'pai_nfc_db' );
		$this->setTableName ( 'yuepai_mobile_info_tbl' );
    }

/**
 * @$send_data[media_type]  (text:文字; notify:系统通知; card:卡片)
 * @$send_data[card_style]  (1:中间有金额那个模板; 2:只有一条文字那个模板)
 * @$send_data[card_text1]  (服务media_type=card的，最上标题)
 * @$send_data[card_text2]  (服务media_type=card的，中间内容)
 * @$send_data[card_title]  (服务media_type=card的，底部)
 * @$send_data[content]     (服务media_type=text/notify)
 * @$send_data[link_url]     (服务media_type=notify/card, 跳转链接);
 * @$send_data[wifi_url]     (服务media_type=notify/card, 跳转链接);
 * 
 * @$to_send_data[media_type]  (text:文字; notify:系统通知; card:卡片)
 * @$to_send_data[card_style]  (1:中间有金额那个模板; 2:只有一条文字那个模板)
 * @$to_send_data[card_text1]  (服务media_type=card的，最上标题)
 * @$to_send_data[card_text2]  (服务media_type=card的，中间内容)
 * @$to_send_data[card_title]  (服务media_type=card的，底部)
 * @$to_send_data[content]     (服务media_type=text/notify)
 * @$to_send_data[link_url]     (服务media_type=notify/card, 跳转链接);
 * @$to_send_data[wifi_url]     (服务media_type=notify/card, 跳转链接);
 * 
 * */
    public function send_msg_data($send_user_id, $to_user_id, $send_data, $to_send_data, $date_id, $is_send = 1)
    {        
        $send_data[send_user_id]    = $to_user_id;
        $send_data[to_user_id]      = $send_user_id;
        $send_data[is_me]           = 1;
        if($date_id) $send_data[link_url]        = 'http://yp.yueus.com/mobile/app?from_app=1#mine/consider_details_camera/' . $date_id;
        if($date_id) $send_data[wifi_url]        = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#mine/consider_details_camera/' . $date_id;
        
        $to_send_data[send_user_id] = $send_user_id;
        $to_send_data[to_user_id]   = $to_user_id;
        $to_send_data[is_me]        = 0;
        if($date_id) $to_send_data[link_url]     = 'http://yp.yueus.com/mobile/app?from_app=1#mine/consider_details_camera/' . $date_id;
        if($date_id) $to_send_data[wifi_url]     = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#mine/consider_details_camera/' . $date_id;

/**
//记录LOG
$add_time = date('Y-m-d H:i:s');
$sql_str = "INSERT INTO pai_log_db.pai_date_info_log_tbl(from_send_id, to_send_id, date_id, card_title, add_time) 
            VALUES ('{$send_data[send_user_id]}', '{$send_data[to_user_id]}', $date_id, '{$send_data[card_title]}', '{$add_time}')";
db_simple_getdata($sql_str, TRUE, 101); 
$from_send_log_id = db_simple_get_insert_id();

$sql_str = "INSERT INTO pai_log_db.pai_date_info_log_tbl(from_send_id, to_send_id, date_id, card_title, add_time) 
            VALUES ('{$to_send_data[send_user_id]}', '{$to_send_data[to_user_id]}', $date_id, '{$to_send_data[card_title]}', '{$add_time}')";
db_simple_getdata($sql_str, TRUE, 101); 
$to_send_log_id = db_simple_get_insert_id();
**/
if($is_send != 10)
{
    $from_send_log_id = $this->seve_log($date_id, $send_data);
    $to_send_log_id   = $this->seve_log($date_id, $to_send_data);
}

        
        //内容整理与过滤
        foreach($send_data AS $key=>&$val)
        {
            $val = (string)iconv ( 'gbk', 'utf-8', $val );
        }
        
        //内容整理与过滤
        foreach($to_send_data AS $key=>&$val)
        {
            $val = (string)iconv ( 'gbk', 'utf-8', $val );
        }
 

        //$array_pass = array(100106, 100052, 100260, 100012, 100000, 100004, 100005, 100006, 100007, 100107, 100008, 100013, 100086, 100016, 100021, 100024, 100031, 100045, 100260, 100003, 100020, 100028, 100087, 100001, 100029, 100005, 100091, 100107);
        
        if($is_send == 1 || $is_send == 2)
        {
              //if(in_array($to_user_id, $array_pass))
              //{
                $notice_array = $this->send_msg($send_data);
                if((int)$notice_array['notice_id'])
                {
                    $notice_id = (int)$notice_array['notice_id'];
                }else{
                    $notice_id = 404;
                }
                $this->update_log_state($from_send_log_id, $notice_id);
                
                $notice_array = $this->send_msg($to_send_data);
                if((int)$notice_array['notice_id'])
                {
                    $notice_id = (int)$notice_array['notice_id'];
                }else{
                    $notice_id = 404;
                }
                $this->update_log_state($to_send_log_id, $notice_id);
              //}
              //else{
              //  $this->update_log_state($from_send_log_id, 402);
              //  $this->update_log_state($to_send_log_id, 402);
              //}
        }elseif($is_send == 10){
            //print_r($send_data);
            $notice_array = $this->send_msg($send_data);
            //var_dump($notice_array);
            //print_r($to_send_data);
            $notice_array = $this->send_msg($to_send_data);
            //var_dump($notice_array);
        }else {
            $this->update_log_state($from_send_log_id, 403);
            $this->update_log_state($to_send_log_id, 403);
        }        
        return TRUE;
    }

    
    public function send_msg($post_data)
    {
        if(YUEYUE_HEYH_TEST == 1)
        {
            $log_data = serialize($post_data);
            $sendserver = '';
            $add_time = date('Y-m-d H:i:s');
            $source = 'pai_information_push:send_msg';

            $sql_str = "INSERT INTO pai_log_db.server_switching_information_log_tbl(log_data, sendserver, add_time, source)
                        VALUES ('{$log_data}', '{$sendserver}', '{$add_time}', '{$source}')";
            db_simple_getdata($sql_str, TRUE, 101);
        }

        $post_data = json_encode ( $post_data );
    	$data ['data'] = $post_data;


        //$data ['data'] = '{"send_user_id":"10002","to_user_id":"100008","media_type":"card","card_style":"1","card_text1":"1111111111","card_text2":"2222222222","card_title":"33333333333"}';
        //$data ['data'] = '{"send_user_id":"100002","to_user_id":"100008","media_type":"card","card_style":"1","card_text1":"1111111111","card_text2":"2222222222","card_title":"33333333333","is_me":"1"}';    
        //$url = 'http://172.18.5.211:8080/sendserver.cgi';
        $url = 'http://172.18.5.211:8080/sendmessage.cgi';
        $ch = curl_init ();
    	curl_setopt ( $ch, CURLOPT_POST, 1 );
    	curl_setopt ( $ch, CURLOPT_URL, $url );
    	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    	curl_setopt ( $ch, CURLOPT_COOKIE, $matches [1] );
    	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
    	$result = curl_exec ( $ch );
    	curl_close ( $ch );
        return json_decode($result, TRUE);
    }


    public function seve_log($date_id, $send_data)
    {
        $send_data_str = serialize($send_data);
        $add_time = date('Y-m-d H:i:s');
        $sql_str = "INSERT INTO pai_log_db.pai_date_info_log_tbl(from_send_id, to_send_id, date_id, card_title, add_time, url, send_data_str) 
                    VALUES ('{$send_data[send_user_id]}', '{$send_data[to_user_id]}', $date_id, '{$send_data[card_title]}', '{$add_time}', '{$send_data[link_url]}', '{$send_data_str}')";
        db_simple_getdata($sql_str, TRUE, 101); 
        return db_simple_get_insert_id();
    }
    
    public function update_log_state($log_id, $notice_id)
    {
        $log_id = (int) $log_id;
        $notice_id = (int) $notice_id;
        
        if($log_id && $notice_id)
        {
            $sql_str = "UPDATE `pai_log_db`.`pai_date_info_log_tbl` 
                        SET return_state = $notice_id
                        WHERE id = $log_id
                        ";
            db_simple_getdata($sql_str, true, 101);            
        }

    }

    public function send_msg_for_system($send_user_id, $to_user_id,  $send_data)
    {
        $notice_id = 0;
        if((int)$send_user_id && (int)$to_user_id)
        {
            if($send_user_id < 100000)
            {
                $send_data[send_user_id]    = $send_user_id;
                $send_data[to_user_id]      = $to_user_id;
                $send_data[is_me]            = 0;

                //内容整理与过滤

                foreach($send_data AS $key=>&$val)
                {
                    $val = (string)iconv ( 'gb2312', 'utf-8', $val );
                }

                $notice_array = $this->send_msg($send_data);

                if((int)$notice_array['notice_id'])
                {
                    $notice_id = (int)$notice_array['notice_id'];
                }
            }
        }
        return $notice_id;
    }

    public function send_msg_for_system_v2($send_user_id, $to_user_id,  $send_data)
    {
        $notice_id = 0;
        if((int)$send_user_id && (int)$to_user_id)
        {
            if($send_user_id < 100000)
            {
                $send_data[send_user_id]    = $send_user_id;
                $send_data[to_user_id]      = $to_user_id;
                $send_data[is_me]            = 0;

                //内容整理与过滤
                foreach($send_data AS $key=>&$val)
                {
                    $val = (string)iconv ( 'gb2312', 'utf-8', $val );
                }

                $notice_array = $this->send_msg($send_data);
                
                if((int)$notice_array['notice_id'])
                {
                    $notice_id = (int)$notice_array['notice_id'];
                }
            }
        }
        return $notice_id;
    }

    /**
 * @$send_data[media_type]  (text:文字; notify:系统通知; card:卡片)
 * @$send_data[card_style]  (1:中间有金额那个模板; 2:只有一条文字那个模板)
 * @$send_data[card_text1]  (服务media_type=card的，最上标题)
 * @$send_data[card_text2]  (服务media_type=card的，中间内容)
 * @$send_data[card_title]  (服务media_type=card的，底部)
 * @$send_data[content]     (服务media_type=text/notify)
 *
 * @$to_send_data[media_type]  (text:文字; notify:系统通知; card:卡片)
 * @$to_send_data[card_style]  (1:中间有金额那个模板; 2:只有一条文字那个模板)
 * @$to_send_data[card_text1]  (服务media_type=card的，最上标题)
 * @$to_send_data[card_text2]  (服务media_type=card的，中间内容)
 * @$to_send_data[card_title]  (服务media_type=card的，底部)
 * @$to_send_data[content]     (服务media_type=text/notify)
 * */
    public function card_send_for_order($send_user_id, $send_user_role, $to_user_id, $to_user_role, $send_data, $to_send_data, $order_sn, $order_type)
    {
        //if(!$test_off) return TRUE;

        $array_log['send_user_id']      = $send_user_id;
        $array_log['send_user_role']    = $send_user_role;
        $array_log['to_user_id']        = $to_user_id;
        $array_log['to_user_role']      = $to_user_role;
        $array_log['send_data']         = $send_data;
        $array_log['to_send_data']      = $to_send_data;
        $array_log['order_sn']          = $order_sn;
        $array_log['order_type']        = $order_type;
        pai_log_class::add_log($array_log, 'send_card', 'send_card');


        $send_data[send_user_id]    = $send_user_id;
        $send_data[to_user_id]      = $to_user_id;
        $send_data[is_me]           = 0;
        $send_data[send_user_role]  = $send_user_role;
        if($order_sn)
        {
            if($order_type == 'payment')
            {
                $url        = "http://yp.yueus.com/mall/wallet/yue_pay/detail.php?order_sn=" . $order_sn;
                $wifi_url   = "http://yp-wifi.yueus.com/mall/wallet/yue_pay/detail.php?order_sn=" . $order_sn;
                if($send_data[send_user_role] == 'yuebuyer')
                {
                    $send_data['link_url'] = 'yueseller://goto?type=inner_web&url=' . urlencode($url) . "&wifi_url=" . urlencode($wifi_url)  . '&showtitle=1';
                    $send_data['wifi_url'] = $send_data['link_url'];
                }else{
                    $send_data['link_url'] = 'yueyue://goto?type=inner_web&url=' . urlencode($url) . "&wifi_url=" . urlencode($wifi_url)  . '&showtitle=1';
                    $send_data['wifi_url'] = $send_data['link_url'];
                }

            }else{
                if($send_data[send_user_role] == 'yuebuyer')
                {
                    $send_data['link_url'] = 'yueseller://goto?order_sn=' . $order_sn . '&pid=1250022&type=inner_app';
                    $send_data['wifi_url'] = 'yueseller://goto?order_sn=' . $order_sn . '&pid=1250022&type=inner_app';
                }else{
                    $url        = "http://yp.yueus.com/mall/user/order/detail.php?order_sn=" . $order_sn;
                    $wifi_url   = "http://yp-wifi.yueus.com/mall/user/order/detail.php?order_sn=" . $order_sn;
                    $send_data['link_url'] = 'yueyue://goto?type=inner_web&url=' . urlencode($url) . "&wifi_url=" . urlencode($wifi_url)  . '&showtitle=1';
                    $send_data['wifi_url'] = $send_data['link_url'];
                }

            }



        }

        //if($date_id) $send_data[link_url]        = 'http://yp.yueus.com/mobile/app?from_app=1#mine/consider_details_camera/' . $date_id;
        //if($date_id) $send_data[wifi_url]        = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#mine/consider_details_camera/' . $date_id;

        $to_send_data[send_user_id]     = $to_user_id;
        $to_send_data[to_user_id]       = $send_user_id;
        $to_send_data[is_me]            = 1;
        $to_send_data[send_user_role]   = $to_user_role;
        if($order_sn)
        {
            if($order_type == 'payment')
            {
                $url        = "http://yp.yueus.com/mall/wallet/yue_pay/detail.php?order_sn=" . $order_sn;
                $wifi_url   = "http://yp-wifi.yueus.com/mall/wallet/yue_pay/detail.php?order_sn=" . $order_sn;
                if($to_send_data[send_user_role] == 'yuebuyer')
                {
                    $to_send_data['link_url'] = 'yueseller://goto?type=inner_web&url=' . urlencode($url) . "&wifi_url=" . urlencode($wifi_url)  . '&showtitle=1';
                    $to_send_data['wifi_url'] = $to_send_data['link_url'];
                }else{
                    $to_send_data['link_url'] = 'yueyue://goto?type=inner_web&url=' . urlencode($url) . "&wifi_url=" . urlencode($wifi_url)  . '&showtitle=1';
                    $to_send_data['wifi_url'] = $to_send_data['link_url'];
                }
            }else {

                if ($to_send_data[send_user_role] == 'yuebuyer') {
                    $to_send_data['link_url'] = 'yueseller://goto?order_sn=' . $order_sn . '&pid=1250022&type=inner_app';
                    $to_send_data['wifi_url'] = 'yueseller://goto?order_sn=' . $order_sn . '&pid=1250022&type=inner_app';
                } else {
                    $url = "http://yp.yueus.com/mall/user/order/detail.php?order_sn=" . $order_sn;
                    $wifi_url = "http://yp-wifi.yueus.com/mall/user/order/detail.php?order_sn=" . $order_sn;
                    $to_send_data['link_url'] = 'yueyue://goto?type=inner_web&url=' . urlencode($url) . "&wifi_url=" . urlencode($wifi_url) . '&showtitle=1';
                    $to_send_data['wifi_url'] = $to_send_data['link_url'];
                }
            }

        }
        //if($date_id) $to_send_data[link_url]     = 'http://yp.yueus.com/mobile/app?from_app=1#mine/consider_details_camera/' . $date_id;
        //if($date_id) $to_send_data[wifi_url]     = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#mine/consider_details_camera/' . $date_id;

        //内容整理与过滤
        foreach($send_data AS $key=>&$val)
        {
            $val = (string)iconv ( 'gbk', 'utf-8', $val );
        }

        //内容整理与过滤
        foreach($to_send_data AS $key=>&$val)
        {
            $val = (string)iconv ( 'gbk', 'utf-8', $val );
        }
        //print_r($send_data);
        //echo json_encode($send_data);
        //print_r($to_send_data);
        //echo json_encode($to_send_data);

        $return_array[send_redis_id]    = $this->send_msg_for_order($send_data);
        $return_array[to_send_redis_id] = $this->send_msg_for_order($to_send_data);

        pai_log_class::add_log($send_data, 'send_card', 'send_card');
        pai_log_class::add_log($to_send_data, 'send_card', 'send_card');


        $send_log_array = $return_array;
        $send_log_array['send_data'] = $send_data;
        $send_log_array['to_send_data'] = $to_send_data;

        //pai_log_class::add_log($send_log_array, 'send_card', 'send_card');

        return $return_array;
    }

    /**
     * @$send_data[media_type]  (text:文字; notify:系统通知; card:卡片 merchandise:系统)
     * @$send_data[card_style]  (1:中间有金额那个模板; 2:只有一条文字那个模板)
     * @$send_data[card_text1]  (服务media_type=card的，最上标题)
     * @$send_data[card_text2]  (服务media_type=card的，中间内容)
     * @$send_data[card_title]  (服务media_type=card的，底部)
     * @$send_data[content]     (服务media_type=text/notify)
     *
     * @$to_send_data[media_type]  (text:文字; notify:系统通知; card:卡片)
     * @$to_send_data[card_style]  (1:中间有金额那个模板; 2:只有一条文字那个模板)
     * @$to_send_data[card_text1]  (服务media_type=card的，最上标题)
     * @$to_send_data[card_text2]  (服务media_type=card的，中间内容)
     * @$to_send_data[card_title]  (服务media_type=card的，底部)
     * @$to_send_data[content]     (服务media_type=text/notify)
     * */
    public function card_send_for_goods($send_user_id, $send_user_role, $to_user_id, $to_user_role, $send_data, $to_send_data, $goods_id, $goods_type)
    {
        //if(!$test_off) return TRUE;
        $send_data[send_user_id]    = $send_user_id;
        $send_data[to_user_id]      = $to_user_id;
        $send_data[is_me]           = 0;
        $send_data[send_user_role]  = $send_user_role;
        if($goods_id)
        {
            if($goods_type == 42)   //活动特殊处理
            {
                if($send_data[send_user_role] == 'yuebuyer')
                {
                    $send_data['link_url'] = 'yueseller://goto?goods_id=' . $goods_id . '&pid=1250044&type=inner_app';
                    $send_data['wifi_url'] = 'yueseller://goto?goods_id=' . $goods_id . '&pid=1250044&type=inner_app';
                }else{
                    $send_data['link_url'] = 'yueyue://goto?goods_id=' . $goods_id . '&pid=1220152&type=inner_app';
                    $send_data['wifi_url'] = 'yueyue://goto?goods_id=' . $goods_id . '&pid=1220152&type=inner_app';
                }
            }else{
                if($send_data[send_user_role] == 'yuebuyer')
                {
                    $send_data['link_url'] = 'yueseller://goto?goods_id=' . $goods_id . '&pid=1250007&type=inner_app';
                    $send_data['wifi_url'] = 'yueseller://goto?goods_id=' . $goods_id . '&pid=1250007&type=inner_app';
                }else{
                    $send_data['link_url'] = 'yueyue://goto?goods_id=' . $goods_id . '&pid=1220102&type=inner_app';
                    $send_data['wifi_url'] = 'yueyue://goto?goods_id=' . $goods_id . '&pid=1220102&type=inner_app';
                }
            }

        }

        //if($date_id) $send_data[link_url]        = 'http://yp.yueus.com/mobile/app?from_app=1#mine/consider_details_camera/' . $date_id;
        //if($date_id) $send_data[wifi_url]        = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#mine/consider_details_camera/' . $date_id;

        $to_send_data[send_user_id]     = $to_user_id;
        $to_send_data[to_user_id]       = $send_user_id;
        $to_send_data[is_me]            = 1;
        $to_send_data[send_user_role]   = $to_user_role;
        if($goods_id)
        {
        	if($goods_type == 42)   //活动特殊处理
        	{
        		if($to_send_data[send_user_role] == 'yuebuyer'){
		                $to_send_data['link_url'] = 'yueseller://goto?goods_id=' . $goods_id . '&pid=1250044&type=inner_app';
		                $to_send_data['wifi_url'] = 'yueseller://goto?goods_id=' . $goods_id . '&pid=1250044&type=inner_app';
		        }else{
		                $to_send_data['link_url'] = 'yueyue://goto?goods_id=' . $goods_id . '&pid=1220152&type=inner_app';
		                $to_send_data['wifi_url'] = 'yueyue://goto?goods_id=' . $goods_id . '&pid=1220152&type=inner_app';
		         }
        	}else{
        		if($to_send_data[send_user_role] == 'yuebuyer'){
		                $to_send_data['link_url'] = 'yueseller://goto?goods_id=' . $goods_id . '&pid=1250007&type=inner_app';
		                $to_send_data['wifi_url'] = 'yueseller://goto?goods_id=' . $goods_id . '&pid=1250007&type=inner_app';
		        }else{
		                $to_send_data['link_url'] = 'yueyue://goto?goods_id=' . $goods_id . '&pid=1220102&type=inner_app';
		                $to_send_data['wifi_url'] = 'yueyue://goto?goods_id=' . $goods_id . '&pid=1220102&type=inner_app';
		         }
        	}
            

        }
        //if($date_id) $to_send_data[link_url]     = 'http://yp.yueus.com/mobile/app?from_app=1#mine/consider_details_camera/' . $date_id;
        //if($date_id) $to_send_data[wifi_url]     = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#mine/consider_details_camera/' . $date_id;

        //内容整理与过滤
        foreach($send_data AS $key=>&$val)
        {
            $val = (string)iconv ( 'gbk', 'utf-8', $val );
        }

        //内容整理与过滤
        foreach($to_send_data AS $key=>&$val)
        {
            $val = (string)iconv ( 'gbk', 'utf-8', $val );
        }
        //print_r($send_data);
        //echo json_encode($send_data);
        //print_r($to_send_data);
        //echo json_encode($to_send_data);

        $return_array[send_redis_id]    = $this->send_msg_for_order($send_data);
        if($to_send_data['media_type'] != 'merchandise' || $test_off) $return_array[to_send_redis_id] = $this->send_msg_for_order($to_send_data);


        $send_log_array = $return_array;
        $send_log_array['send_data'] = $send_data;
        $send_log_array['to_send_data'] = $to_send_data;

        pai_log_class::add_log($send_log_array, 'send_card', 'send_card');

        return $return_array;
    }

    /**
     * 系统信息发送
     * @$send_data[media_type]  (text:文字; notify:系统通知; card:卡片)
     * @$send_data[card_style]  (1:中间有金额那个模板; 2:只有一条文字那个模板)
     * @$send_data[card_text1]  (服务media_type=card的，最上标题)
     * @$send_data[card_text2]  (服务media_type=card的，中间内容)
     * @$send_data[card_title]  (服务media_type=card的，底部)
     * @$send_data[content]     (服务media_type=text/notify)
     *
     * @$app_role               (发送者的yuebuyer:消费者版；yueseller：商家版);
     **/
    public function message_sending_for_system($user_id, $send_data, $sys_id = 10000, $app_role = 'yuebuyer')
    {
        //return TRUE;
        //echo $app_role;
        if($sys_id > 99999) return '错误！发送ID不是系统保留ID';

        if((int)$user_id)
        {
            $send_data[send_user_id]    = $sys_id;
            $send_data[to_user_id]      = $user_id;

            //角色转化，把发送者的角色匹配更改为目标者的角色
            if($app_role == 'yuebuyer')
            {
                $app_role = 'yueseller';
            }else{
                $app_role = 'yuebuyer';
            }

            $send_data[send_user_role]  = $app_role;
            //print_r($send_data);
            //内容整理与过滤
            foreach($send_data AS $key=>&$val)
            {
                $val = (string)iconv ( 'gb2312', 'utf-8', $val );
            }

            return $this->send_msg($send_data);
        }
    }

    public function send_msg_for_order($post_data)
    {
        if(YUEYUE_HEYH_TEST == 1)
        {
            var_dump($post_data);
            return TRUE;
        }

        $post_data = json_encode ( $post_data );
    	$data ['data'] = $post_data;
        //$data ['data'] = '{"send_user_id":"10002","to_user_id":"100008","media_type":"card","card_style":"1","card_text1":"1111111111","card_text2":"2222222222","card_title":"33333333333"}';
        //$data ['data'] = '{"send_user_id":"100002","to_user_id":"100008","media_type":"card","card_style":"1","card_text1":"1111111111","card_text2":"2222222222","card_title":"33333333333","is_me":"1"}';
        $url = 'http://172.18.5.211:8080/sendmessage.cgi';
        $ch = curl_init ();

    	curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 10);
    	curl_setopt ( $ch, CURLOPT_URL, $url );
    	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    	curl_setopt ( $ch, CURLOPT_COOKIE, $matches [1] );
    	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
    	$result = curl_exec ( $ch );
    	curl_close ( $ch );
        $return_array =  json_decode($result, TRUE);
        if($return_array['code'])
        {
            return $return_array;
        }else{
            $add_time = date('Y-m-d H:i:s');
            $sql_str = "INSERT INTO pai_log_db.pai_sendlog_tbl(json_content, add_time)
                        VALUES (:x_json_content, '{$add_time}')";
            sqlSetParam($sql_str, 'x_json_content', $post_data);
            db_simple_getdata($sql_str, TRUE, 101);
        }
    }
}
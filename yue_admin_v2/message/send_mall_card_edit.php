<?php
/**
 * @desc:   发送商家或者卖家的信息
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/5
 * @Time:   14:24
 * version: 1.0
 */
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '512M');
include_once('common.inc.php');
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/yue_function.php");//常量函数
$send_message_log_v2_obj = POCO::singleton( 'pai_send_message_log_v2_class' ); //发送商家和买家的类
$user_obj = POCO::singleton( 'pai_user_class' );
//校验用户角色,非商家报错
$mall_obj = POCO::singleton('pai_mall_seller_class');

$tpl = new SmartTemplate("send_mall_card_edit.tpl.htm");

$act = trim($_INPUT['act']);
$is_multi = intval($_INPUT['is_multi']);
$prov  = intval($_INPUT['province']);
$location_id = intval($_INPUT['location_id']);
$role = trim($_INPUT['role']);
$type_id = intval($_INPUT['type_id']);
$only_send_online = intval($_INPUT['only_send_online']);
$start_time = trim($_INPUT['start_time']);
$end_time = trim($_INPUT['end_time']);
$start_interval = trim($_INPUT['start_interval']);
$end_interval = trim($_INPUT['end_interval']);
$send_uid = intval($_INPUT['send_uid']) ? intval($_INPUT['send_uid']) : 10002;
$user_str = trim($_INPUT['user_str']);
$user_str = trim(str_replace(array(',','<br rel=auto>','<br/>','<br>'), ',', $user_str),",");
$card_style = intval($_INPUT['card_style']) ? intval($_INPUT['card_style']) :  2; //卡片类型
$card_text1 = trim($_INPUT['card_text1']); //卡片描述
$card_text1 = str_replace('<br rel=auto>', "\r\n", $card_text1);
$card_text2 = trim($_INPUT['card_text2']); //金额
$card_title = trim($_INPUT['card_title']);
$link_url  = trim($_INPUT['link_url']);
$wifi_url = trim($_INPUT['wifi_url']);

if($act == 'insert') {//插入数据，正式发送
    $send_code = trim($_INPUT['send_code']);//发送人员验证码
    $market_code = trim($_INPUT['market_code']);//市场负责人验证码
    $technical_code = trim($_INPUT['technical_code']);//技术负责人验证码
    $ret = $send_message_log_v2_obj->get_code_info($yue_login_id);//获取验证码

    if(strlen($role) <1) js_pop_msg_v2('请选择角色');
    if(!in_array($role,$send_message_log_v2_obj->get_role_arr())) js_pop_msg_v2('非法操作');
    //验证必填字段
    if (strlen($start_time) < 1) js_pop_msg_v2("开始日期不能为空");
    if (strlen($end_time) < 1) js_pop_msg_v2("结束日期不能为空");
    if (strlen($start_interval) < 1) js_pop_msg_v2("开始时间不能为空");
    if (strlen($end_interval) < 1) js_pop_msg_v2("结束时间不能为空");
    if (strlen($card_text1) < 1) js_pop_msg_v2("卡片描述不能为空");
    if (strlen($card_title) < 1) js_pop_msg_v2("卡片标题不能为空");

    //验证码判断
    if (empty($ret) || !is_array($ret)) js_pop_msg_v2("验证码有误");
    if ($send_code == '' || $send_code != $ret['send_code'] || $market_code == '' || $market_code != $ret['market_code'] || $technical_code == '' || $technical_code != $ret['technical_code'] || $ret['expires_time'] < time()) js_pop_msg_v2("验证码失效");

    $user_arr = array();
    //群发开始
    if ($is_multi == 1)
    {
        if($role == 'yuebuyer')
        {
            $user_arr = $send_message_log_v2_obj->get_mall_buyer_list($location_id,$user_str);
        }
        elseif($role == 'yueseller')
        {
            $user_arr = $send_message_log_v2_obj->get_mall_seller_list($type_id,$location_id,$user_str);
        }
    }else
    {
        $user_arr = $send_message_log_v2_obj->get_sign_mall_list($role,$user_str);
    }
    if (empty($user_arr)) js_pop_msg_v2("得到用户的个数为零");
    $type = "card";
    $is_me = 0;
    $option = array();
    $option['only_send_online'] = $only_send_online;
    $option['auto_send'] = 1;//改为自动发送
    $option['is_me'] = $is_me;
    $option['card_style'] = $card_style;
    $option['card_title'] = $card_title;
    $option['card_text1'] = $card_text1;
    $option['card_text2'] = $card_text2;
    $option['link_url'] = $link_url;
    $option['wifi_url'] = $wifi_url;
    //入库给看的
    $option['location_id'] = $location_id;
    $option['is_multi'] = $is_multi;
    $option['type_id'] = $type_id;//分类ID
    $ret = $send_message_log_v2_obj->add_message_all_v2($type,$role,$start_interval,$end_interval,$start_time,$end_time,$user_arr,$send_uid,$option);
    $retID = intval($ret['result']);
    if($retID>0) js_pop_msg_v2("添加信息成功",false,"send_mall_message_list.php");

    js_pop_msg_v2("添加信息失败");
    exit;
}
elseif($act == 'send_sole')//发送单条
{
    if(strlen($role) <1) js_pop_msg_v2('请选择角色');
    if(!in_array($role,$send_message_log_v2_obj->get_role_arr())) js_pop_msg_v2('非法操作');
    //验证必填字段
    if (strlen($start_time) < 1) js_pop_msg_v2("开始日期不能为空");
    if (strlen($end_time) < 1) js_pop_msg_v2("结束日期不能为空");
    if (strlen($start_interval) < 1) js_pop_msg_v2("开始时间不能为空");
    if (strlen($end_interval) < 1) js_pop_msg_v2("结束时间不能为空");
    if (strlen($card_text1) < 1) js_pop_msg_v2("卡片描述不能为空");
    if (strlen($card_title) < 1) js_pop_msg_v2("卡片标题不能为空");

    $type  = "card";
    $user_arr     = array('100041','111172','100002','100293','100013','100001');
    //$user_arr     = array('100293','100293','100293');
    //MD5部分
    $auto_send  = 1;//1表示自动发送
    $option['auto_send'] = $auto_send;
    $option['card_style'] = $card_style;
    $option['card_title'] = $card_title;
    $option['card_text2'] = $card_text2;
    $option['link_url'] = $link_url;
    $option['wifi_url'] = $wifi_url;
    $option['type_id'] = $type_id;//这个测试需要
    $insert_data = array();
    $i = 0;
    foreach ($user_arr as $user_id)
    {
        $code        = getStr(4);
        $user_id     = intval($user_id);
        if($role == 'yueseller' && $user_id>0)
        {
            $seller_info = $mall_obj->get_seller_info($user_id,2);
            $seller_name=$seller_info['seller_data']['name'];
            if(strlen($seller_name)>0)
            {
                $user_id = "yueseller/{$user_id}";
            }
        }elseif($role == "yuebuyer" && $user_id >0)
        {
            $user_id = "yuebuyer/{$user_id}";
        }
        $user_id     = array("{$user_id}");
        $tmp_text    = $card_text1.' 验证码:'.$code;
        $option['card_text1'] = trim($tmp_text);
        $arr = $send_message_log_v2_obj->add_message_all_v2($type,$role,0,0,$start_time,$end_time,$user_id,$send_uid,$option);
        if($i == 0)
        {
            $insert_data['send_code'] = $code;
        }
        if($i == 1)
        {
            $insert_data['market_code'] = $code;
        }
        if($i == 2)
        {
            $insert_data['technical_code'] = $code;
        }
        $i++;
        unset($option['card_text1']);
    }
    //过期时间
    $insert_data['expires_time'] = time()+60*60;
    $retID = $send_message_log_v2_obj->add_message_code($insert_data);
    var_dump($retID);
    $retID = intval($retID);
    if($retID >0) js_pop_msg_v2("发送验证码成功!");
    js_pop_msg_v2("发送验证码失败!");
    exit;
}
elseif ($act == 'checkcode')//验证码验证
{
    $send_code       = trim($_INPUT['send_code']);
    $market_code     = trim($_INPUT['market_code']);
    $technical_code  = trim($_INPUT['technical_code']);
    $ret = $send_message_log_v2_obj->get_code_info($yue_login_id);
    if ( empty($ret) || !is_array($ret))
    {
        echo  0;
        exit;
    }
    if($send_code == '' || $send_code != $ret['send_code'] || $market_code == '' || $market_code != $ret['market_code'] || $technical_code == '' || $technical_code != $ret['technical_code'] || $ret['expires_time'] < time())
    {
        echo 1;
        exit;
    }
    else
    {
        echo 2;
        exit;
    }
}

$tpl->output();

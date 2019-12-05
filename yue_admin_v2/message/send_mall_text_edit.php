<?php
/**
 * @desc:   发送
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/6
 * @Time:   14:03
 * version: 1.0
 */
ignore_user_abort(ture);
ini_set("max_execution_time", 3600);
ini_set('memory_limit', '512M');
include("common.inc.php");
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/yue_function.php");//常量函数
$send_message_log_v2_obj = POCO::singleton( 'pai_send_message_log_v2_class' ); //发送商家和买家的类
$user_obj = POCO::singleton( 'pai_user_class' );
//校验用户角色,非商家报错
$mall_obj = POCO::singleton('pai_mall_seller_class');
$tpl = new SmartTemplate("send_mall_text_edit.tpl.htm");

$act = trim($_INPUT['act']);

$data_url = trim($_INPUT['data_url']);
$is_multi = intval($_INPUT['is_multi']);
$prov = intval($_INPUT['province']);
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
$type = trim($_INPUT['type']);
$content = trim($_INPUT['content']);
$content = str_replace('<br rel=auto>', "\r\n", $content);
$link_url = trim($_INPUT['link_url']);
$wifi_url = trim($_INPUT['wifi_url']);

if($act == 'insert')//发送批量的
{
    //验证码
    $send_code = trim($_INPUT['send_code']);
    $market_code = trim($_INPUT['market_code']);
    $technical_code = trim($_INPUT['technical_code']);
    $ret = $send_message_log_v2_obj->get_code_info($yue_login_id);

    if(strlen($role) <1) js_pop_msg_v2('请选择角色');
    if(!in_array($role,$send_message_log_v2_obj->get_role_arr())) js_pop_msg_v2('非法操作');
    if (strlen($start_time) <1) js_pop_msg_v2("开始日期不能为空");
    if (strlen($end_time) <1) js_pop_msg_v2("结束日期不能为空");
    if (strlen($start_interval) <1) js_pop_msg_v2("开始时间不能为空");
    if (strlen($end_interval) <1) js_pop_msg_v2("结束时间不能为空");
    if (strlen($content)<1) js_pop_msg_v2("内容不能为空");
    if ( empty($ret) || !is_array($ret))js_pop_msg_v2("验证码有误");
    //验证码判断
    if (empty($ret) || !is_array($ret)) js_pop_msg_v2("验证码有误");
    if ($send_code == '' || $send_code != $ret['send_code'] || $market_code == '' || $market_code != $ret['market_code'] || $technical_code == '' || $technical_code != $ret['technical_code'] || $ret['expires_time'] < time()) js_pop_msg_v2("验证码失效");
    $user_arr = array();//初始化数组

    //群发开始
    if ($is_multi == 1) {
        if($role == 'yuebuyer')
        {
            $user_arr = $send_message_log_v2_obj->get_mall_buyer_list($location_id,$user_str);
        }
        elseif($role == 'yueseller')
        {
            $user_arr = $send_message_log_v2_obj->get_mall_seller_list($type_id,$location_id,$user_str);
        }
    }
    else
    {
        $user_arr = $send_message_log_v2_obj->get_sign_mall_list($role,$user_str);
    }
    if (count($user_arr) < 1) js_pop_msg_v2("得到用户的个数为零");
    $is_me = 0;
    $option = array();
    $option['only_send_online'] = $only_send_online;
    $option['auto_send'] = 1;//改为自动发送
    $option['content']  = $content;
    $option['link_url'] = $link_url;
    $option['wifi_url'] = $wifi_url;
    //入库给看的
    $option['is_multi']    = $is_multi;
    $option['type_id'] = $type_id;//分类ID
    $option['location_id'] = $location_id;

    //分两条发
    /*$user_arr1 = array();
    $user_arr2 = array();
    $i = 0;
    foreach($user_arr as $v)
    {
        if($i >=50000)
        {
            $user_arr1[] = $v;
        }else
        {
            $user_arr2[] = $v;
        }
        $i++;
    }
    $ret1 = $send_message_log_v2_obj->add_message_all_v2($type,$role,$start_interval,$end_interval,$start_time,$end_time,$user_arr1,$send_uid,$option);
    $ret = $send_message_log_v2_obj->add_message_all_v2($type,$role,$start_interval,$end_interval,$start_time,$end_time,$user_arr2,$send_uid,$option);*/

    $ret = $send_message_log_v2_obj->add_message_all_v2($type,$role,$start_interval,$end_interval,$start_time,$end_time,$user_arr,$send_uid,$option);
    $retID = intval($ret['result']);
    if($retID>0) js_pop_msg_v2("添加信息成功",false,"send_mall_message_list.php");
    js_pop_msg_v2("添加信息失败");
    exit;
}
elseif($act == 'send_sole')//发送验证码
{
    if(strlen($role) <1) js_pop_msg_v2('请选择角色');
    if(strlen($role) <1) js_pop_msg_v2('请选择角色');
    echo $role;
    if(!in_array($role,$send_message_log_v2_obj->get_role_arr())) js_pop_msg_v2('非法操作');
    if (strlen($start_time) <1) js_pop_msg_v2("开始日期不能为空");
    if (strlen($end_time) <1) js_pop_msg_v2("结束日期不能为空");
    if (strlen($start_interval) <1) js_pop_msg_v2("开始时间不能为空");
    if (strlen($end_interval) <1) js_pop_msg_v2("结束时间不能为空");
    if (strlen($content)<1) js_pop_msg_v2("内容不能为空");
    $user_arr     = array('100041','111172','100002','100293','100013','100001');
    //$user_arr     = array('100293','100293','100293','100293');
    //MD5部分
    $auto_send  = 1;//1表示自动发送
    $option['auto_send'] = $auto_send;
    $option['link_url'] = $link_url;
    $option['wifi_url'] = $wifi_url;
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
        $tmp_text    = $content.' 验证码:'.$code;
        $option['content'] = trim($tmp_text);
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
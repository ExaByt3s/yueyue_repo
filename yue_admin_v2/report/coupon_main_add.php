<?php
/**
 * @desc:   添加优惠券码
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/2
 * @Time:   15:16
 * version: 1.0
 */
include_once('common.inc.php');
check_auth($yue_login_id,'coupon_main_add');//权限控制
include_once(YUE_ADMIN_V2_CLASS_ROOT.'pai_add_coupon_class.inc.php');
$coupon_sn_obj = new pai_add_coupon_class();

$tpl = new SmartTemplate( REPORT_TEMPLATES_ROOT.'coupon_main_add.tpl.htm');

$act = trim($_INPUT['act']);
$begin_time = trim($_INPUT['begin_time']);
$end_time = trim($_INPUT['end_time']);
$coupon = trim($_INPUT['coupon']);
$coupon = trim(str_replace(array(',','<br rel=auto>','<br/>','<br>'), ',', $coupon),",");


//插入数据开始
if($act == 'insert')
{
    if(preg_match("/\d\d\d\d-\d\d-\d\d/", $begin_time) || preg_match("/\d\d\d\d\d\d\d\d/", $begin_time))
    {
        $begin_time = (int)strtotime($begin_time);
    }
    else
    {
        js_pop_msg_v2('开始时间不能为空');
    }
    if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_time) || preg_match("/\d\d\d\d\d\d\d\d", $end_time))
    {
        $end_time = (int)strtotime($end_time);
    }
    else
    {
        js_pop_msg_v2('结束时间不能为空');
    }
    if(strlen($coupon) <1) js_pop_msg_v2('优惠码不能为空');
    $ret = $coupon_sn_obj->coupon_main_add_info($coupon,$begin_time,$end_time);
    if(!is_array($ret)) $ret = array();
    $code = (int)$ret['code'];
    $id = (int)$ret['id'];
    if($code > 0) js_pop_msg_v2('保存成功',false,"coupon_main_list.php",true);
    if($id >0)
    {
        $err = trim($ret['err']);
        js_pop_msg_v2($err);
    }
    js_pop_msg_v2("保存失败");

}


$tpl->output();
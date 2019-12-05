<?php
/**
 * @desc:   管理员手动拉黑用户
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/29
 * @Time:   14:23
 * version: 2.0
 */
include('common.inc.php');
check_auth($yue_login_id,'inform_list');//权限控制
include_once(YUE_ADMIN_V2_CLASS_ROOT.'pai_log_inform_v2_class.inc.php');
$pai_log_inform_v2_obj = new pai_log_inform_v2_class(); //举报黑名单类

$tpl = new SmartTemplate( TEMPLATES_ROOT."admin_add_shield.tpl.htm" );

$act = trim($_INPUT['act']);
$user_id = (int)$_INPUT['user_id'];
$data_str = trim($_INPUT['data_str']);

//拉黑用户
if($act == 'shield')
{
    if($user_id < 1) js_pop_msg_v2('用户ID不能为空');
    if($user_id < 100000) js_pop_msg_v2('您无法拉黑系统用户');
    if(strlen($data_str)<1) js_pop_msg_v2('请填写拉黑原因');
    $ret = $pai_log_inform_v2_obj->admin_add_user_id_into_blacklist($user_id,$data_str);
    print_r($ret);
    if(!is_array($ret)) $ret = array();
    $status = (int)$ret['code'];
    if($status >0) js_pop_msg_v2('手动拉黑成功',false,"?act=007",true);
    js_pop_msg_v2('手动拉黑失败,可能改用户已经被拉黑过了');
    exit;
}



$tpl->output();
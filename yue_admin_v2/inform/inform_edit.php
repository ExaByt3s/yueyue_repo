<?php
/**
 * @desc:   举报控制器详情
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/28
 * @Time:   17:19
 * version: 2.0
 */
include('common.inc.php');
check_auth($yue_login_id,'inform_edit');//权限控制
include_once(YUE_ADMIN_V2_CLASS_ROOT.'pai_log_inform_v2_class.inc.php');
$pai_log_inform_v2_obj = new pai_log_inform_v2_class(); //举报黑名单类
$user_obj = POCO::singleton('pai_user_class'); //用户类
$user_level_obj = POCO::singleton('pai_user_level_class');//摄影师等级类
$score_rank_obj = POCO::singleton('pai_score_rank_class'); //模特等级类

$tpl = new SmartTemplate( TEMPLATES_ROOT.'inform_edit.tpl.htm' );

//接受参数
$act = trim($_INPUT['act']);
$id = (int)$_INPUT['id'];
if($id <1) js_pop_msg_v2("非法操作",true);

if ($act == 'shield')//把举报者加入黑名单
{
    $reason = trim($_INPUT['reason']);
    $ret = $pai_log_inform_v2_obj->update_inform_by_id($id,$reason);
    $status = (int)$ret['code'];
    if($status >0) js_pop_msg_v2("操作成功",false,"inform_list.php");
    js_pop_msg_v2("操作失败",false,"inform_list.php");
    exit;
}

//举报详情数据
$ret = $pai_log_inform_v2_obj->get_inform_info($id);
if (is_array($ret))
{

    //被举报者基本信息
    $ret['status'] = $pai_log_inform_v2_obj->get_info_by_to_informer_id($ret['to_informer']);//被举报人状态
    $to_informer_data['to_informer_name'] = get_user_nickname_by_user_id($ret['to_informer']);
    $by_informer_data['to_cellphone'] = $user_obj->get_phone_by_user_id($ret['to_informer']);
    $to_informer_data['to_be_count_v2'] = $pai_log_inform_v2_obj->get_inform_list(true,0,$ret['to_informer']); //被举报次数
    $to_informer_data['by_to_count_v2'] = $pai_log_inform_v2_obj->get_inform_list(true,$ret['to_informer']); //举报别人次数


    //举报人基本信息
    $by_informer_data['by_informer_name'] = get_user_nickname_by_user_id($ret['by_informer']); //举报人信息结合
    $by_informer_data['by_cellphone'] = $user_obj->get_phone_by_user_id($ret['by_informer']);
    $by_informer_data['to_be_count'] = $pai_log_inform_v2_obj->get_inform_list(true,0,$ret['by_informer']); //被举报次数
    $by_informer_data['by_to_count'] = $pai_log_inform_v2_obj->get_inform_list(true,$ret['by_informer']); //举报别人次数
}
$tpl->assign($ret);
$tpl->assign($by_informer_data);
$tpl->assign($to_informer_data);
$tpl->output ();
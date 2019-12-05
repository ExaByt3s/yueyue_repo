<?php
/**
 * 私人订单
 */
include_once('../common.inc.php');
$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );

$lower_limit = intval($_INPUT["lower_limit_v"]);
$ceiling = intval($_INPUT["ceiling_v"]);

// 权限检查
mall_check_user_permissions($yue_login_id);

if($lower_limit>=$ceiling)
{
    $output_arr['code'] = 0;
    $output_arr['title'] =  '提交失败';
    $output_arr['msg'] =  '预算下限等于或超过上限';
    mall_mobile_output($output_arr,false);
    die();
}

$user_obj = POCO::singleton ( 'pai_user_class' );
$user_info = $user_obj->get_user_info($yue_login_id);//手机号码

$nickname  = get_user_nickname_by_user_id($yue_login_id);//根据用户id获取昵称
$insert_data['cameraman_phone'] = $user_info['cellphone'];//提交人手机
$insert_data['cameraman_nickname'] = $user_info['nickname'];//提交人昵称
$insert_data['location_id'] = intval($_INPUT['location_id']) ? intval($_INPUT['location_id']) : 101029001 ;//地区
$insert_data['date_remark'] = iconv('utf-8','gbk',$_INPUT['remark']);//备注
$insert_data['date_time'] = $_INPUT['time_text']; //时间
$insert_data['hour'] = intval($_INPUT['time_span']);//时长
$insert_data['model_num'] = intval($_INPUT['order_num']);//数量
$insert_data['about_budget'] = $lower_limit."-".$ceiling;//预算下限
$insert_data['audit_status'] = 'wait';//写死
$insert_data['order_status'] = 'wait';//写死
$insert_data['type_id_str'] = $_INPUT['type_id_str'];//服务ID
$insert_data['source'] = 5;//

$ret = $model_oa_order_obj->add_order($insert_data);

$output_arr['code'] = $ret ? 1 :0;
$output_arr['title'] = $ret ? '提交成功' : '提交失败';
$output_arr['msg'] = "感谢你的支持，我们将会24小时内与你联系！按确定将返回首页~";
mall_mobile_output($output_arr,false);

?>
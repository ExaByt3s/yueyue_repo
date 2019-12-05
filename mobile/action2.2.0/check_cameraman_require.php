<?php
/**
 * 检查摄影师是否达到要求
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');





$output_arr['code'] = 0;
$output_arr['msg'] = "抱歉，亲，模特邀约功能已暂停使用";
$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');
$output_arr['list'] = true;
$output_arr['model_level_require'] = '1';
mobile_output($output_arr,false);
exit();


if(!$yue_login_id)
{
//	die('no login');
    $output_arr['code'] = 0;
    $output_arr['msg'] = "尚未登录，请登录";
    $output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');
    $output_arr['list'] = true;
    $output_arr['model_level_require'] = '1';
    mobile_output($output_arr,false);
    exit();
}

//注意 肥华加 开始
$user_obj = POCO::singleton('pai_user_class');
$role  = $user_obj->check_role($yue_login_id);
if($role == 'model')
{
    $output_arr['code'] = 0;
    $output_arr['msg'] = "模特不能约模特";
    $output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');
    $output_arr['list'] = true;
    $output_arr['model_level_require'] = '1';
    mobile_output($output_arr,false);
    exit();
}
//注意 肥华加 结束

$model_user_id = $_INPUT["model_user_id"];

$btn_type = $_INPUT['btn_type'];

$obj = POCO::singleton ( 'pai_model_card_class' );

$ret = $obj->check_cameraman_level_require($yue_login_id, $model_user_id,$btn_type);

$model_level_require = $obj->get_model_level_require($model_user_id);

/*
 * 检查摄影师信用等级是否达到模特要求
 * @param int $cameraman_user_id
 * @param int $model_user_id
 * 
 * return bool
 */
//$ret = $obj->check_cameraman_level_require($yue_login_id, $model_user_id);

$output_arr['code'] = $ret?1:0;
$output_arr['msg'] = $ret ? '成功' : "这位模特需要达到V{$model_level_require}认证级别才能约拍，赶紧来升级你的信用等级吧。";
$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');
$output_arr['list'] = $ret;
$output_arr['model_level_require'] = $model_level_require;

mobile_output($output_arr,false);

?>
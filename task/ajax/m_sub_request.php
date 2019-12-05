<?php
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once("/disk/data/htdocs232/poco/pai/mobile/include/output_function.php");
global $yue_login_id;


/*‘
 * 提交问卷 汤圆
 * @param int $quote_id
 * @return int
 */
$user_id = $yue_login_id;

$data = $_REQUEST['data'];
$user_id = $yue_login_id;
$service_id = (int)$data['service_id'];

$data = poco_iconv_arr($data,'UTF-8', 'GBK');

////////////////////////////////////////////////////提交问卷调查代码
function sub_request($user_id,$service_id,$question_list)

//$user_id用户ID $service_id问卷类型 $question_list答案数据格式参照http://113.107.204.251/wiki/index.php/首页#.E6.8F.90.E4.BA.A4.E9.97.AE.E5.8D.B7.E4.BF.A1.E6.81.AF.EF.BC.8C.E7.94.9F.E6.88.90.E9.9C.80.E6.B1.82
{
    $user_id = (int)$user_id;
    $service_id = (int)$service_id;
    //获取服务信息
    $task_service_obj = POCO::singleton('pai_task_service_class');
    $service_info = $task_service_obj->get_service_info($service_id);
    $title = trim($service_info['service_name']);
    //获取用户信息
    $pai_user_obj = POCO::singleton('pai_user_class');
    $user_info = $pai_user_obj->get_user_info($user_id);
    $cellphone = trim($user_info['cellphone']);
    //整理资料
    $more_info = array(
        'title' => $title,
        'cellphone' => $cellphone,
        'email' => '',
    );
    $task_request_obj = POCO::singleton('pai_task_request_class');
    $submit_ret = $task_request_obj->submit_request($user_id, $service_id, $more_info, $question_list);
    //$submit_ret返回值为array,元素有以下
    //$submit_ret['result'];//result返回1为成功
    //$submit_ret['message'];
    //$submit_ret['request_id'];
    return $submit_ret;
}


$re = sub_request($user_id,$service_id,$data);
mobile_output($re,false);


?>
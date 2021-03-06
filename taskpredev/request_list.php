<?php
/** 
 * 
 * tt
 * 汤圆
 * 2015-4-11
 * 
 */
 
 include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
 
/**
 * 引用资源文件定位，注意！确保引用路径争取
 */
$file_dir = dirname(__FILE__);

include_once($file_dir.'/./task_common.inc.php');

// 权限文件
include_once($file_dir.'/./task_auth_common.inc.php');

include_once($file_dir. '/./webcontrol/head.php');




include_once($file_dir. '/./webcontrol/footer.php');
 
$tpl = $my_app_pai->getView('request_list.tpl.htm');

// 消费者 必须引入
include_once($file_dir.'/./consumers_require.php');

$tpl->assign('time', time());  //随机数

// 公共样式和js引入
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_task_top', $pc_global_top);

$pc_global_nav = _get_wbc_top_nav(array('cur_page'=>'request_list'));
$tpl->assign('pc_global_nav', $pc_global_nav);

// 底部
$footer_html = _get_wbc_footer();
$tpl->assign('footer_html', $footer_html);


global $yue_login_id;

//获取参数
$user_id = 100028;
$service_id = 1;

//初始化对象
$task_request_obj = POCO::singleton('pai_task_request_class');
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$task_service_obj = POCO::singleton('pai_task_service_class');

//整理需求单数据
$hired_quotes_info = array();
$request_list_tmp = $task_request_obj->get_request_detail_list_by_user_id($user_id);
$request_count = count($request_list_tmp);
$request_list = array();
foreach($request_list_tmp as $request_info_tmp)
{
    $status_code_tmp = trim($request_info_tmp['status_code']);
    $quotes_count_tmp = trim($request_info_tmp['quotes_count']);
    $quotes_list_tmp = $request_info_tmp['quotes_list'];
    if( !is_array($quotes_list_tmp) ) $quotes_list_tmp = array();
    $service_id_tmp = trim($request_info_tmp['service_id']);
    
    //获取服务信息
    $service_info_tmp = $task_service_obj->get_service_info($service_id_tmp);
    
    //整理报价单数据
    $quotes_list = array();
    foreach($quotes_list_tmp as $quotes_info_tmp)
    {
        $quotes_id_tmp = trim($quotes_info_tmp['quotes_id']);
        
        //状态颜色
        $quotes_is_gray_tmp = $task_quotes_obj->get_quotes_is_gray($quotes_info_tmp['status'], $request_info_tmp['status_code']);
        
        $remind_num_tmp = $task_quotes_obj->get_quotes_remind_num($user_id, $quotes_id_tmp);
        $remind_num_tmp = trim($remind_num_tmp);
        
        if( $quotes_info_tmp['status']==1 )
        {
            $hired_quotes_info = $quotes_info_tmp;
        }
        
        $quotes_list[] = array(
            'quotes_id' => $quotes_info_tmp['quotes_id'],
            'user_id' => $quotes_info_tmp['user_id'],
            'user_icon' => $quotes_info_tmp['user_icon'],
            'is_vip' => $quotes_info_tmp['is_vip'],
            'status' => $quotes_info_tmp['status'],
            'is_gray' => $quotes_is_gray_tmp,
            'remind_num' => $remind_num_tmp,
        );
    }
    
    $tip_title_tmp = ''; //提示标题
    $tip_content_tmp = ''; //提示内容
    if( $status_code_tmp=='started' ) //待雇佣，待过期，无报价
    {
        $tip_title_tmp = '等待反馈中';
        $tip_content_tmp = '我们会将你的需求发送给所有符合资格的供应者，你会在24小时内收到回复';
    }
    elseif( $status_code_tmp=='introduced' ) //待雇佣，待过期，有报价
    {
        $tip_title_tmp = '';
        $tip_content_tmp = "{$quotes_count_tmp}个{$service_info_tmp['profession_name']}想为你提供服务";
    }
    elseif( $status_code_tmp=='closed' ) //待雇佣，已过期，无报价
    {
        $tip_title_tmp = '暂时没有符合要求的人选';
        $tip_content_tmp = '很遗憾，暂时没有能为你服务的供应者符合您的需求';
    }
    elseif( $status_code_tmp=='quoted' ) //待雇佣，已过期，有报价
    {
        $tip_title_tmp = '';
        $tip_content_tmp = "{$quotes_count_tmp}个{$service_info_tmp['profession_name']}想为你提供服务";
    }
    elseif( $status_code_tmp=='canceled' )
    {
        $canceled_time_str = date('Y.m.d', $request_info_tmp['canceled_time']);
        $tip_title_tmp = '';
        $tip_content_tmp = "你在{$canceled_time_str}取消了该订单";
    }
    elseif( in_array($status_code_tmp, array('hired', 'paid', 'reviewed')) )
    {
        $tip_title_tmp = '';
        $tip_content_tmp = "已选中“{$hired_quotes_info['user_nickname']}”";
    }
    
    $request_list[] = array(
        'request_id' => $request_info_tmp['request_id'],
        'service_id' => $request_info_tmp['service_id'],
        'title' => $request_info_tmp['title'],
        'add_time_str' => $request_info_tmp['add_time_str'],
        'status_color' => $request_info_tmp['status_color'],
        'status_code' => $request_info_tmp['status_code'],
        'status_name' => $request_info_tmp['status_name'],
        'tip_title' => $tip_title_tmp,
        'tip_content' => $tip_content_tmp,
        'request_icon' => 'http://img16.poco.cn/yueyue/20150416/2015041614590034524576.png?64x64_130',
        'quotes_list' => $quotes_list,
    );
}

$data = array();
$data['request_count'] = trim($request_count);
$data['request_list'] = $request_list;
$data['mid'] = '122OD04002'; //模板ID




//  16进制颜色转换
foreach ($data['request_list'] as $k => $v) 
{
    $data['request_list'][$k]['status_color'] = str_replace( substr($v['status_color'],0,4),"#",$v['status_color'] );
}



$tpl->assign('request_list', $data['request_list']);



if (isset($_INPUT['print'])) 
{
    print_r($data['request_list']);
}




$tpl->output();
 ?>
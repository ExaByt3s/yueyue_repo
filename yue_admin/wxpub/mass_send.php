<?php
include_once('./common.inc.php');
$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
$bind_id = $_COOKIE['cur_bind_id'];
$act     = $_INPUT['act'];
if( $act=='new' )//新增任务or任务详情
{
    $sex = intval($_INPUT['sex']);
    $country = trim($_INPUT['country']);
    $province = trim($_INPUT['province']);
    $city = trim($_INPUT['city']);

    $no_sex = intval($_INPUT['no_sex']);
    $no_country = intval($_INPUT['no_country']);
    $no_province = intval($_INPUT['no_province']);
    $no_city = intval($_INPUT['no_city']);

    //国家列表
    $country_list = $weixin_helper_obj->get_user_list($bind_id, false, "country<>'' AND is_subscribe=1", '', '0,99999', 'country');
    $country_list = array_map('array_shift', $country_list);
    $country_list = array_unique($country_list);
    $country_list = array_values($country_list);
    $country_list = array_combine($country_list, $country_list);

    /*
    //省份列表
    $province_list = $weixin_helper_obj->get_user_list($bind_id, false, "province<>'' AND is_subscribe=1", '', '0,99999', 'province');
    $province_list = array_map('array_shift', $province_list);
    $province_list = array_unique($province_list);
    $province_list = array_values($province_list);
    $province_list = array_combine($province_list, $province_list);

    //城市列表
    $city_list = $weixin_helper_obj->get_user_list($bind_id, false, "city<>'' AND is_subscribe=1", '', '0,99999', 'city');
    $city_list = array_map('array_shift', $city_list);
    $city_list = array_unique($city_list);
    $city_list = array_values($city_list);
    $city_list = array_combine($city_list, $city_list);
     */

    $tpl = $my_app_pai->getView('mass_send_info.tpl.htm');
    $header_html = $my_app_pai->webControl('WxpubAdminPageHeader',$header_arr,true);

    $tpl->assign('bind_id', $bind_id);
    $tpl->assign('act', $act);

    $tpl->assign('country', $country_list);
    //$tpl->assign('province', $province_list);
    //$tpl->assign('city', $city_list);

    //  $tpl->assign('country_selected', $country);
    //  $tpl->assign('province_selected', $province);
    //  $tpl->assign('city_selected', $city);

    //  $tpl->assign($no_sex);
    //  $tpl->assign($no_country);
    //  $tpl->assign($no_province);
    //  $tpl->assign($no_city);

    $count_total = $weixin_helper_obj->get_user_list($bind_id, true, 'is_subscribe=1');
    $tpl->assign('total', $count_total);
    //$tpl->assign('select', $count_select);
    $tpl->output();
    exit;
}
elseif( $act=='save' )//保存任务
{
    $media_id = trim($_INPUT['media_id']);
    if( strlen($media_id)<1||$bind_id<1 )
    {
        pop_msg('参数错误');
        exit;
    }

    //整理条件
    $sex = intval($_INPUT['sex']);
    $no_sex = intval($_INPUT['no_sex']);

    $country = trim($_INPUT['country']);
    $no_country = intval($_INPUT['no_country']);

    $province = trim($_INPUT['province']);
    $no_province = intval($_INPUT['no_province']);

    $city = trim($_INPUT['city']);
    $no_city = intval($_INPUT['no_city']);

    $min_time = trim($_INPUT['begin_time']);
    $max_time = trim($_INPUT['end_time']);

    $data = compact(
        'sex', 
        'no_sex', 
        'country', 
        'no_country', 
        'province', 
        'no_province', 
        'city', 
        'no_city', 
        'min_time', 
        'max_time'
    );
    $create_mission_condition_rst = $weixin_helper_obj->create_mission_condition_str($data);

    $where = $create_mission_condition_rst['condition_str'];
    $count = $weixin_helper_obj->get_user_list($bind_id, true, $where);
    if( $count==0 )
    {
        pop_msg('条件没有匹配任何用户');
    }

    $data = array(
        'bind_id'=>$bind_id,
        'media_id'=>$media_id,
        'condition_str'=>$where,
        'mission_desc'=>$create_mission_condition_rst['mission_desc'],
        'user_count'=>$count,
        'status'=>0,
        'remark'=>mysql_escape_string($remark),
    );

    $mission_id = $weixin_helper_obj->add_mass_send_mission($data);
    if( $mission_id>1 )
    {
        pop_msg('任务已成功新建，将在若干分钟后开始发送。', 'mass_send.php');
    }
}
elseif( $act=='force_send')//更新并发送
{
    $mission_id = intval($_INPUT['mission_id']);

    $mission_info = $weixin_helper_obj->get_mass_send_mission_info($mission_id);
    if( !is_array($mission_info)||count($mission_info)<1 )
    {
        pop_msg('任务不存在', 'mass_send.php');
    }

    $where = $mission_info['condition_str'];
    $count = $weixin_helper_obj->get_user_list($bind_id, true, $where);
    $data = array(
        'user_count'=>$count,
        'status'=>0, 
        'err_msg' => '',
    );

    $update_rst = $weixin_helper_obj->update_mass_send_mission($mission_id, $data);
    if( $update_rst )
    {
        pop_msg('任务已成功更新，将自动提交到微信并开始发送。', 'mass_send.php');
    }
}
elseif( $act=='cancel_mission')
{
    $mission_id = intval($_INPUT['mission_id']);

    $mission_info = $weixin_helper_obj->get_mass_send_mission_info($mission_id);
    if( !is_array($mission_info)||count($mission_info)<1 )
    {
        pop_msg('任务不存在');
    }
  
    $cancel_rst = $weixin_helper_obj->cancel_mass_send_mission($mission_id);
    if( !$cancel_rst )
    {
        pop_msg('任务无法取消');
    }
    pop_msg('任务成功取消', 'mass_send.php');
}
elseif( $act=='ajax' )
{
    //数量
    $count_select = intval($_INPUT['count_select']);

    $sex = intval($_INPUT['sex']);
    $no_sex = intval($_INPUT['no_sex']);
    
    $country = trim($_INPUT['country']);
    $country = iconv('UTF-8','gbk',$country);
    $no_country = intval($_INPUT['no_country']);

    $province = trim($_INPUT['province']);
    $province = iconv('UTF-8','gbk',$province);
    $no_province = intval($_INPUT['no_province']);

    $city = trim($_INPUT['city']);
    $city = iconv('UTF-8','gbk',$city);
    $no_city = intval($_INPUT['no_city']);

    $min_time = trim($_INPUT['begin_time']);
    $max_time = trim($_INPUT['end_time']);

    $data = compact(
        'sex', 
        'no_sex', 
        'country', 
        'no_country', 
        'province', 
        'no_province', 
        'city', 
        'no_city', 
        'min_time', 
        'max_time'
    );
    $create_mission_condition_rst = $weixin_helper_obj->create_mission_condition_str($data);
    $where = $create_mission_condition_rst['condition_str'];

    if( $count_select==1 )
    {
        $count = $weixin_helper_obj->get_user_list($bind_id, true, $where);
        $count_total = $weixin_helper_obj->get_user_list($bind_id, true, 'is_subscribe=1');
        echo "{$count}/{$count_total}";
        die();
    }

    //地区, 联动下拉选框
    if( strlen($country)>0&&strlen($province)>0 )
    {
        //city_list
        $data_list = $weixin_helper_obj->get_user_list($bind_id, false, "is_subscribe=1 AND country='{$country}' AND province='{$province}' AND city<>''", '', '0,99999', 'city' );
    }
    elseif( strlen($country)>0 )
    {
        //province_list
        $data_list = $weixin_helper_obj->get_user_list($bind_id, false, "is_subscribe=1 AND country='{$country}' AND province<>''", '', '0,99999', 'province' );

    }
    /*
    else
    {
        //country_list
        $data_list = $weixin_helper_obj->get_user_list($bind_id, false, "is_subscribe=1 AND country<>''", '', '0,99999', 'country');
    }
    */

    $data_arr = array_map('array_shift', $data_list);
    $data_arr = array_unique($data_arr);
    $data_arr = array_values($data_arr);

    foreach( $data_arr as &$data)
    {
        $data = iconv('gbk','UTF-8',$data);
    }

    $data_arr = array_map('urlencode', $data_arr);
    
    echo json_encode($data_arr);
    exit;
}
//任务列表
$handle_status_enum = array(
    '0' => '待处理',
    '1' => '处理中',
    '2' => '已取消',
    '7' => '处理失败',
    '8' => '处理完成', 
);
$sub_mission_status_enum = array(
    '0' =>'待处理',
    '1' =>'处理中',
    '2' =>'已取消',
    '7' =>'提交失败',
    '8' =>'提交成功',
);
$tpl = $my_app_pai->getView('mass_send_mission_list.tpl.htm');
$header_html = $my_app_pai->webControl('WxpubAdminPageHeader',$header_arr,true);

$where_str = '';
$total_count = $weixin_helper_obj->get_mass_send_mission_list($bind_id, true, $where_str);

$page_obj    = POCO::singleton('show_page');
$page_obj->set(20, $total_count);

$mission_list = $weixin_helper_obj->get_mass_send_mission_list($bind_id, false, $where_str, 'mission_id DESC', $page_obj->limit());

//整理数据
foreach( $mission_list as &$mission_info )
{
    $mission_info['status_show'] = $handle_status_enum[$mission_info['status']];
    $msg_list = $weixin_helper_obj->get_mass_send_msg_list($bind_id,false, "mission_id={$mission_info['mission_id']}",'msg_id ASC', '0,99999999');

    $mission_info['sub_mission_status_list'] = array();
    $mission_info['success_count'] = 0;
    foreach( $msg_list as $msg_info )
    {
        $mission_info['sub_mission_status_list'][] = array( 'sub_mission_status_show'=>$sub_mission_status_enum[$msg_info['status']] );
        $mission_info['success_count'] += $msg_info['wx_sent_count'];
    }

    //处理时间
    $handle_time = intval($mission_info['handle_time']);
    $mission_info['handle_time_str'] = '--';
    if( $handle_time>0 )
    {
        $mission_info['handle_time_str'] = date('Y-m-d H:i:s', $handle_time);
    }

    //提交时间，fail_time 或 success_time
    $mulit_time = 0 + $mission_info['fail_time'] + $mission_info['success_time'];
    $mission_info['mulit_time_str'] = '--';
    if( $mulit_time>0 )
    {
        $mission_info['mulit_time_str'] = date('Y-m-d H:i:s', $mulit_time);
    }

    $mission_info['add_time_str'] = date('Y-m-d H:i:s', $mission_info['add_time']);
}

$tpl->assign('mission_list', $mission_list);

$page_select   = $page_obj->output(true);
$tpl->assign('page_select', $page_select);
$tpl->output();
exit;
?>

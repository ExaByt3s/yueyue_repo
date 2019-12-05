<?php

/**
 * 首页模特数据
 * zy 2014.9.25
 */

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$ori_img_size = '_260.';
$img_size = '_320.';

$location_id = intval ( $_INPUT ['location_id'] ) ? intval ( $_INPUT ['location_id'] ) : 101029001;

$type = $_INPUT ['type'];

$cms_obj = new cms_system_class ();
$score_obj = POCO::singleton ( 'pai_score_rank_class' );
$date_obj = POCO::singleton ( 'pai_date_rank_class' );
$model_style_obj = POCO::singleton ( 'pai_model_style_v2_class' );
$pic_obj = POCO::singleton ( 'pai_pic_class' );
$pic_score_obj = POCO::singleton ( 'pai_score_rank_class' );
$model_card_obj = POCO::singleton ( 'pai_model_card_class' );
$cameraman_card_obj = POCO::singleton ( 'pai_cameraman_card_class' );
$pai_user_obj = POCO::singleton ( 'pai_user_class' );
$rank_event_obj = POCO::singleton('pai_rank_event_class');
$pai_cms_parse_obj      = POCO::singleton( 'pai_cms_parse_class' );

$page_mode = $_INPUT ['_page_mode'];

$check_role = $pai_user_obj->check_role ( $yue_login_id );

if(empty($check_role))
{
	$check_role = "cameraman";
}

if ($check_role == 'model')
{
	$role = 1;
}

$ranking_array = $rank_event_obj->get_rank_event_by_location_id($location_id, $check_role);

$pai_cms_parse_obj->set_come_from('weixin');
$cms_data = $pai_cms_parse_obj->cms_parse_by_array($ranking_array);

//txt1第一栏第一   ,txt2第一栏第二, txt3第二栏第一, txt4第二栏第二


foreach ( $cms_data as $key => $val )
{
	$data = '';

	$info = $cms_obj->get_last_issue_record_list ( false, '0,4', 'place_number ASC', $val ['rank_id'] );
	$count_info = $cms_obj->get_last_issue_record_list ( true, '0,4', 'place_number DESC', $val ['rank_id'] );
	
	
	$data ['title'] = $val ['name'];
	$data ['key'] = $key;
	$data ['rank_id'] = $val ['rank_id'];
	$data ['text'] = '更多';
	$data ['dmid'] = $val ['dmid'];
	
	if($val['rank_id']!=15 && $count_info>4)
	{
		$data ['more'] = $count_info;
	}
	
	$unit = $val [2];
	$record = '';
	foreach ( $val['user_list'] as $k => $v )
	{
		$record ['user_id'] = $v ['user_id'];
		
		$record ['txt1'] = $v ['num'];
		$record ['txt2'] = $v ['unit'];
		$record ['txt3'] = $v ['nickname'];
		$record ['txt4'] = $v ['style'];
		
		$record ['url'] = 'model_card/' . $v ['user_id'];
		$record ['user_icon'] = yueyue_resize_act_img_url($v ['user_icon'],'320');
		
		$record ['link_type'] = $v ['link_type'];

		//modify by hudw
		$record ['link_type'] = 'inner_web';
		
		$data ['list'] [] = $record;
	
	}
	
	$ret [] = $data;
	$data = '';
}

$output_arr ['code'] = $ret ? 1 : 0;
$output_arr ['msg'] = $ret ? '获取成功' : '获取失败';
$output_arr ['list'] = $ret;
$output_arr ['type'] = 'home_page';

mobile_output ( $output_arr, false );

?>
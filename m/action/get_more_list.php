<?php

/**
 * 首页更多列表
 */

include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$page = intval ( $_INPUT ['page'] );

$location_id = intval ( $_INPUT ['location_id'] ) ? intval ( $_INPUT ['location_id'] ) :101029001;

$rank_id = ( int ) $_INPUT ['rank_id'];

$type = $_INPUT ['type'];

yue_convert_weixin_topic_url($url);exit;

$poco_cache_obj = new poco_cache_class ();

$score_obj = POCO::singleton ( 'pai_score_rank_class' );
$date_obj = POCO::singleton ( 'pai_date_rank_class' );
$user_obj = POCO::singleton ( 'pai_model_style_v2_class' );
$pic_obj = POCO::singleton ( 'pai_pic_class' );
$pic_score_obj = POCO::singleton ( 'pai_score_rank_class' );
$model_card_obj = POCO::singleton ( 'pai_model_card_class' );
$cameraman_card_obj = POCO::singleton ( 'pai_cameraman_card_class' );
$pai_user_obj = POCO::singleton ( 'pai_user_class' );

// 分页使用的page_count
$page_count = 11;
if ($page > 1)
{
	$limit_start = ($page - 1) * ($page_count - 1);
}
else
{
	$limit_start = 0;
}

$limit = "{$limit_start},{$page_count}";

$cms_obj = new cms_system_class ();


$path = "/disk/data/htdocs232/poco/pai/mobile_app/hot_config/{$location_id}.php";

if(file_exists($path))
{
	$include_path = $path;
	$ranking_array = include_once ($include_path);
}

$title = $ranking_array[$rank_id][0];
$unit = $ranking_array[$rank_id][2];


if ($order)
{
	$info = $cms_obj->get_last_issue_record_list ( false, $limit, 'place_number DESC', $rank_id );
}
else
{
	$info = $cms_obj->get_last_issue_record_list ( false, $limit, 'place_number ASC', $rank_id );
}

$data ['title'] = $title;


if($pai_user_obj->check_role($yue_login_id) == 'model')
{
    $role = 1;
}

foreach ( $info as $key => $v )
{
	$record ['user_id'] = $v ['user_id'];
	
	if ($role)
	{
		$record ['url'] = 'http://yp.yueus.com/mobile/app?from_app=1#zone/' . $v ['user_id'] . '/cameraman';
		$record ['num'] = '约拍 ' . $date_obj->count_cameraman_take_photo_times ( $v ['user_id'] );
		$record ['unit'] = '次';
		$record ['style'] = '';
		$record ['nickname'] = $v ['user_name'];
		
		if ($rank_id == 15)
		{
			$record ['url'] = $v ['link_url'];
			$record ['num'] = '';
			$record ['unit'] = '';
			$record ['style'] = '';
			
			$record ['user_icon'] = $v ['img_url'];
			$record ['nickname'] = $v ['remark'];
			$record ['url'] = $v ['link_url'];
		}
		//头像修改
		$pic_array = $pic_obj->get_user_pic ( $v ['user_id'], '0,5' );
		foreach ( $pic_array as $key => $val )
		{
			$num = explode ( '?', $val ['img'] );
			$num = explode ( 'x', $num [1] );
			$num_v2 = explode ( '_', $num [1] );
			
			$width = $num [0];
			$height = $num_v2 [0];
			
			if ($width < $height)
			{
				$record ['user_icon'] = str_replace ( "_260.", "_440.", $val ['img'] );
				break;
			}
			$record ['user_icon'] = str_replace ( "_260.", "_440.", $val ['img'] );
		}
	
	}
	else
	{
		
		if ($unit == '魅力')
		{
			$result = $pic_score_obj->get_score_rank ( $v ['user_id'] );
			$record ['num'] = $result ['score'];
			$record ['unit'] = $unit;
		}
		elseif ($unit == '分')
		{
			$record ['num'] = $v ['place_number'] * 2;
			$record ['unit'] = $unit;
		}
		elseif ($unit == '小时')
		{
			$sql_str = "SELECT hour, price FROM pai_db.pai_model_style_v2_tbl WHERE user_id=$record[user_id] AND group_id=1";
			$result = db_simple_getdata ( $sql_str, TRUE, 101 );
			if ($result ['hour'])
			{
				$record ['num'] = $result ['price'] . "/" . $result ['hour'];
				$record ['unit'] = $unit;
			}
		}
		else
		{
			$record ['num'] = '';
			$record ['unit'] = $unit;
		}
		
		$record ['nickname'] = $v ['user_name'];
		$record ['user_icon'] = get_user_icon ( $v ['user_id'], $size = 468 );
		
		//头像修改
		$pic_array = $pic_obj->get_user_pic ( $v ['user_id'], $limit = '0,5' );
		foreach ( $pic_array as $key => $val )
		{
			$num = explode ( '?', $val ['img'] );
			$num = explode ( 'x', $num [1] );
			$num_v2 = explode ( '_', $num [1] );
			
			$width = $num [0];
			$height = $num_v2 [0];
			
			if ($width < $height)
			{
				$record ['user_icon'] = str_replace ( "_260.", "_440.", $val ['img'] );
				break;
			}
			$record ['user_icon'] = str_replace ( "_260.", "_440.", $val ['img'] );
		}
		
		$user_info = $user_obj->get_model_style_combo ( $v ['user_id'] );
		$style_array = explode ( ' ', $user_info ['main'] [0] ['style'] );
		$record ['style'] = $style_array [0] ? $style_array [0] : '清新';
		$record ['style'] = '擅长 ' . $record ['style'];
		
		$record ['url'] = 'http://yp.yueus.com/mobile/app?from_app=1#model_card/' . $v ['user_id'];
	}
	
	$data ['user_list'] [] = $record;
}

if ($data)
	$options [] = $data;
	
// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 10;

$has_next_page = (count ( $ret ) > $rel_page_count);

if ($has_next_page)
{
	array_pop ( $ret );
}

$output_arr ['list'] = $options;

$output_arr ['has_next_page'] = $has_next_page;

mobile_output ( $output_arr, false );

?>
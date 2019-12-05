<?php

/**
 * 发布活动
 * hdw 2014.8.29
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(empty($yue_login_id))
{
	die('no login');
}

$admin_user_obj = POCO::singleton('event_admin_user_class');

/**
 * 添加活动
 * 
 * @param Array $data 活动数据
 * @param int   $audit 0=>需要审核 1=>审核通过
 * $data['club_id'] = intval($_INPUT['club_id']); //俱乐部ID，不用获取，接口自动接收
 * */
$data['title'] = mb_convert_encoding(trim($_INPUT['title']),'gbk','utf-8'); //活动标题
$data['user_id'] = $yue_login_id;//发布人ID
$data['cover_image'] = trim(str_replace("_260.jpg",".jpg",$_INPUT['cover_image'])); //封面图
$data['type_icon'] = 'yuepai';//测试写死

$data['location_id'] = intval($_INPUT['location_id']);//地区ID
$data['address'] = mb_convert_encoding(trim($_INPUT['address']),'gbk','utf-8');//地址
$data['budget'] = intval($_INPUT['budget']);//活动费用
$data['category'] = 2; //写死
$data['join_mode'] = 0; //写死
$data['add_time'] = time(); //发布时间 时间戳
$data['status'] = 0; //写死
$data['content'] = mb_convert_encoding(trim($_INPUT['content']),'gbk','utf-8'); //主题
$data['remark'] = mb_convert_encoding(trim($_INPUT['remark']),'gbk','utf-8'); //备注
$data['last_update_time'] = time();// 发布时间戳
$data['pay_type']=1;//支持过期退随时退
$data['club_id'] = intval($_INPUT['club_id']);
$data['other_info'] = $_REQUEST['other_info_detail']; //模特图文介绍

$data['is_authority'] = $admin_user_obj->get_is_authority_by_user_id($yue_login_id,$data['type_icon']);

$data['leader_info'] = $_INPUT['leader_info_detail']; //领队信息

$data['table_data'] = $_INPUT['table_info']; // 场次信息

foreach($data['table_data'] as $key => $value){
	$data['table_data'][$key]['begin_time'] = strtotime($value['begin_time']);
	$data['table_data'][$key]['end_time'] = strtotime($value['end_time']);

	//获取最后一场时间作为活动结束时间
    $end_time = $data['table_data'][$key]['end_time'];
}

//活动开始时间
$start_time = $data['table_data'][0]['begin_time'];


if($start_time>$end_time){
	$output_arr['code'] = 0;
	$output_arr['msg'] = '活动时间错误';
	$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');

	mobile_output($output_arr,false); 
	exit;
}

if($data['budget']<10 || $data['budget']>10000){
	$output_arr['code'] = 0;
	$output_arr['msg'] = '活动价格要在10-10000之内';
	$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');

	mobile_output($output_arr,false); 
	exit;
}


foreach ($data['other_info'] as $key => $value) 
{
	$data['other_info'][$key]['text'] = $keyword = mb_convert_encoding($value['text'],'gbk','utf-8');	

	foreach ($data['other_info'][$key]['img'] as $i_key => $i_value) 
	{
		$data['other_info'][$key]['img'][$i_key] = $i_value['img_s'];		
	}
}

foreach ($data['leader_info'] as $key => $value) 
{
	$data['leader_info'][$key]['name'] = $keyword = mb_convert_encoding($value['name'],'gbk','utf-8');
}


$data['start_time'] = $start_time;
$data['end_time'] = $end_time;


// 序列化
$data['other_info'] = serialize($data['other_info']);
$data['leader_info'] = serialize($data['leader_info']);
$data['table_data'] = serialize($data['table_data']);

//print_r($data);exit;

$ret = add_event($data,$audit=0);

$output_arr['code'] = $ret;
$output_arr['msg'] = $ret ? '正在审核' : '需要审核';
$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');

mobile_output($output_arr,false); 

?>
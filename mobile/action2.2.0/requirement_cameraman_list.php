<?php

/**
 * 摄影师需求列表
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');



$model_oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );
$model_enroll_obj = POCO::singleton ( 'pai_model_oa_enroll_class' );


/**
 * 页面接收参数
 */
$page = intval($_INPUT['page']) ;
$style = iconv("utf-8","gbk",$_INPUT['style']);
$question_budget = iconv("utf-8","gbk",$_INPUT['question_budget']);
$date_time = $_INPUT['date_time'];
$location_id = $_INPUT['location_id']?$_INPUT['location_id']:101029001;

$question_budget = urldecode($question_budget);

if(empty($page))
{
	$page = 1;
}


// 分页使用的page_count
$page_count = 11;
if($page > 1)
{
	$limit_start = ($page - 1)*($page_count - 1);	
}
else
{
	$limit_start = ($page - 1)*$page_count;	
}

$limit = "{$limit_start},{$page_count}";

$where = "";

if($style)
{
	$where .= " and style like '%{$style}%'";
}
if($question_budget)
{
	$where .= " and question_budget='{$question_budget}'";
}

if($location_id)
{
	$where .= " and location_id={$location_id}";
}


switch ($date_time) {
	case 'today':
		$now = date("Y-m-d");
		$where .= " and FROM_UNIXTIME(UNIX_TIMESTAMP(date_time),'%Y-%m-%d')='{$now}'";
	break;
	
	case 'tomorrow':
		$tomorrow = date("Y-m-d",strtotime("+1 day"));
		$where .= " and FROM_UNIXTIME(UNIX_TIMESTAMP(date_time),'%Y-%m-%d')='{$tomorrow}'";
	break;
	
	case 'next_3_day':
		$now = date("Y-m-d");
		$day_3 = date("Y-m-d",strtotime("+2 day"));
		$where .= " and FROM_UNIXTIME(UNIX_TIMESTAMP(date_time),'%Y-%m-%d') between '{$now}' and '{$day_3}'";
	break;
	
	case 'next_7_day':
		$now = date("Y-m-d");
		$day_7 = date("Y-m-d",strtotime("+6 day"));
		$where .= " and FROM_UNIXTIME(UNIX_TIMESTAMP(date_time),'%Y-%m-%d') between '{$now}' and '{$day_7}'";
	break;
	
	case 'weekend':
	
		$arr = event_system_class::get_weekend_by_date ();
		$sun = date("Y-m-d",$arr ["Sun"]-1);
		$sat =  date("Y-m-d",$arr ["Sat"]);

		$where .= " and FROM_UNIXTIME(UNIX_TIMESTAMP(date_time),'%Y-%m-%d') between '{$sat}' and '{$sun}'";
	break;
	
	default:
	break;
	
}


$ret = $model_oa_order_obj->get_requirement_list(false,$where,$limit);

foreach($ret as $k=>$val)
{
	$time = date("H:i",strtotime($val['date_time']));
	
	if($time=='10:00')
	{
		$time_text = "上午";
	}
	elseif($time=='15:00')
	{
		$time_text = "下午";
	}
	elseif($time=='18:00')
	{
		$time_text = "晚上";
	}
	else
	{
		$time_text = "下午";
	}

	$new_ret[$k]['time'] = date("n月d日",strtotime($val['date_time']))." ".$time_text;
    if((int)$val['question_budget'])
    {
        $new_ret[$k]['budget'] = '￥' . $val['question_budget'];
    }else{
        $new_ret[$k]['budget'] = $val['question_budget'];
    }

	$new_ret[$k]['style'] = $val['question_style'];
	$new_ret[$k]['order_id'] = $val['order_id'];
	
	$check_sign = $model_enroll_obj->check_repeat($yue_login_id,$val['order_id']);
	
	if($check_sign)
	{
		$new_ret[$k]['sign_text'] = '已报名';
	}
	else
	{
		$new_ret[$k]['sign_text'] = '';
	}
}

// 输出前进行过滤最后一个数据，用于真实输出
$rel_page_count = 10;

$has_next_page = (count($new_ret)>$rel_page_count);

if($has_next_page)
{
	array_pop($new_ret);
}

$output_arr['list'] = $new_ret;


$output_arr['has_next_page'] = $has_next_page;

mobile_output($output_arr,false);

?>
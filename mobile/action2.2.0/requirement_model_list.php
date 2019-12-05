<?php

/**
 * 模特问卷列表
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$model_list_obj = POCO::singleton ( 'pai_model_oa_model_list_class' );
$model_style_obj = POCO::singleton ( 'pai_model_style_v2_class' );
$date_rank_obj = POCO::singleton ( 'pai_date_rank_class' );
$model_card_obj   = POCO::singleton ( 'pai_model_card_class' );
$pic_obj          = POCO::singleton ('pai_pic_class');

/**
 * 页面接收参数
 */
$order_id = intval($_INPUT['order_id']) ;


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


$ret = $model_list_obj->get_question_model_list($order_id,$limit);

foreach($ret as $k=>$val)
{
	$style_arr = $model_style_obj->get_model_style_by_user_id($val['user_id']);
	$new_ret[$k]['style'] = $style_arr[0]['style'];
	$new_ret[$k]['price'] = $style_arr[0]['price'].'/'.$style_arr[0]['hour'].'小时';
	$new_ret[$k]['take_photo_times'] = $date_rank_obj->count_model_take_photo_times ( $val['user_id'] );
	$new_ret[$k]['nickname'] = get_user_nickname_by_user_id($val['user_id']);
	$new_ret[$k]['user_id'] =$val['user_id'];
	
	$model_info = $model_card_obj->get_model_card_info($val['user_id']);
	if (empty ( $model_info ['cover_img'] ))
	{
		$pic_array = $pic_obj->get_user_pic ( $val ['user_id'], $limit = '0,10' );
		foreach ( $pic_array as $a => $b )
		{
			
			$num = explode ( '?', $b ['img'] );
			$num = explode ( 'x', $num [1] );
			$num_v2 = explode ( '_', $num [1] );
			
			$width = $num [0];
			$height = $num_v2 [0];
			
			if ($width < $height)
			{
				$model_info ['cover_img'] = $b ['img'];
				
				break;
			}
		}
	}
	$new_ret [$k] ['img_url'] = yueyue_resize_act_img_url ( $model_info ['cover_img'], '320' );
	
	unset($style_arr);
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
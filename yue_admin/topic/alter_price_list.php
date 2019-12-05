<?php

include_once 'common.inc.php';
include_once 'top.php';

$tpl = new SmartTemplate("alter_price_list.tpl.htm");

$alter_topic_id = (int)$_INPUT['alter_topic_id'];
$style = $_INPUT['style'];

$page_obj = new show_page ();

$alert_price_obj = POCO::singleton('pai_alter_price_class');
$alert_price_user_obj = POCO::singleton('pai_alter_price_user_class');
$model_style_v2_obj = POCO::singleton('pai_model_style_v2_class');
$alert_price_log_obj = POCO::singleton('pai_alter_price_log_class');

$topic_list = $alert_price_obj->get_topic_list(false,"del_status=0");

$topic_info = $alert_price_obj->get_topic_info($alter_topic_id);


$list = $alert_price_user_obj->get_user_list(false, "alter_topic_id={$alter_topic_id}", 'user_id ASC');

$i=0;

foreach($list as $k=>$val){
	$style_arr = $model_style_v2_obj->get_model_style_by_user_id($val['user_id']);
	$j=0;
	foreach($style_arr as $style_val)
	{
		$new_list[$i]['id'] = $val['id'];
		$new_list[$i]['tag'] = $val['tag'];
		$new_list[$i]['user_id'] = $style_val['user_id'];
		$new_list[$i]['alter_topic_id'] = $val['alter_topic_id'];
		$new_list[$i]['nickname'] = get_user_nickname_by_user_id($style_val['user_id']);
		$new_list[$i]['style'] = $style_val['style'];
		$new_list[$i]['old_price'] = $style_val['price'].'Ԫ/'.$style_val['hour'].'Сʱ';
		
		if($j==0)
		{
			$new_list[$i]['first'] = 1;
		}
		
		$log_info = $alert_price_log_obj->get_log_id_info($val['alter_topic_id'],$val['user_id'],$style_val['style']);
		
		if($log_info['log_id'])
		{
			
			$change_ret = $alert_price_log_obj->change_alter_price($log_info['alter_type'],$log_info['type_value'],$style_val['price'],$style_val['hour']);
			
			$alter_price = $change_ret['price'].'Ԫ/'.$change_ret['hour'].'Сʱ';
			
			$new_list[$i]['alter_price'] = $alter_price;
			$new_list[$i]['log_id'] = $log_info['log_id'];
		}
		else
		{
			$new_list[$i]['alter_price'] = '';
			$new_list[$i]['log_id'] = 0;
		}
		
		
		unset($alter_price);
		$i++;
		$j++;
	}
}

include_once('/disk/data/htdocs232/poco/pai/config/model_card_config.php');

foreach ($model_style as $key => $val) {
    $tmp_arr = array(
        'style' => (string)$val,
    );
    $model_style_arr[]=$tmp_arr;
}


$tpl->assign('list', $new_list);
$tpl->assign('model_style_arr', $model_style_arr);
$tpl->assign('topic_list', $topic_list);
$tpl->assign('alter_topic_id', $alter_topic_id);
$tpl->assign('topic_title',$topic_info['title']);

$tpl->assign('style', $style);


$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
$tpl->output();
?>
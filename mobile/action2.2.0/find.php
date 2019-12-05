<?php
/**
 * hudw 2014.8.11
 * 首页发现图片列表
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

/**
 * 页面接收参数
 */
$page = intval($_INPUT['page']);
$type = $_INPUT['type'] ? trim($_INPUT['type']) : 'style';
$location_id = $_INPUT['location_id'] ;

if(empty($location_id))
{
	$location_id = 0;
}


$obj = POCO::singleton('pai_find_style_class');

switch($type)
{
	case 'style':

		include_once '/disk/data/htdocs232/poco/pai/config/model_card_config.php';

		foreach ($model_style as $key => $value) 
		{
			//$type_name = mb_convert_encoding($value,'utf-8','gbk');

		  	$ret[$key]['title'] = $value;

		  	$list = $obj->discover_model_by_style($location_id,$value);

		  	foreach ($list as $l_key => $l_value) 
		  	{
		  		$list[$l_key]['user_icon'] = $l_value['user_icon_165'];		  		

		  		// 中间的图
				if(($l_key+2)%3==0)
				{
					$list[$l_key]['type'] = 'middle';			
				}
				else
				{
					$list[$l_key]['type'] = 'one';	
				}
		  	}

		  	$ret[$key]['list'] = $list;

		  	if(!count($ret[$key]['list']))	
		  	{
		  		unset($ret[$key]);
		  	}


		}	

		$ret = array_values($ret);

		$output_arr['data'] = $ret;
		
		break;
	case 'chest':
		/*
		 * 按胸围发现模特
		 * @param int $location_id
		 * @param string $cup 参数为  A B C D E+
		 * 
		 * return array
		 */		
		
		$config = array('A','B','C','D','E+');		

		foreach ($config as $key => $value) 
		{
			$type_name = $value;

		  	$ret[$key]['title'] = $value;

		  	$list = $pai_obj->discover_model_by_chest($location_id,$type_name);

		  	foreach ($list as $l_key => $l_value) 
		  	{
		  		$list[$l_key]['user_icon'] = $l_value['user_icon_165'];

		  		// 中间的图
				if(($l_key+2)%3==0)
				{
					$list[$l_key]['type'] = 'middle';			
				}
				else
				{
					$list[$l_key]['type'] = 'one';	
				}
		  	}

		  	$ret[$key]['list'] = $list;

		  	if(!count($ret[$key]['list']))	
		  	{
		  		unset($ret[$key]);
		  	}


		}	

		$ret = array_values($ret);

		$output_arr['data'] = $ret;

		break;
	case 'height':
		/*
		 * 按身高发现模特
		 * @param int $location_id
		 * @param int $height_type 
		 * 160以下参数传1
		 * 160-165参数传2
		 * 166-170参数传3
		 * 171-175参数传4
		 * 176-180参数传5
		 * 180以上参数传6
		 * 
		 * return array
		 */		

		$config = array('160以下'=>1,'160-165'=>2,'166-170'=>3,'171-175'=>4,'176-180'=>5,'180以上'=>6);		

		foreach ($config as $key => $value) 
		{
			$type_name = $value;

		  	$ret[$key]['title'] = mb_convert_encoding($key,'gbk','utf-8');;

		  	$list = $pai_obj->discover_model_by_height($location_id,$type_name);

		  	foreach ($list as $l_key => $l_value) 
		  	{
		  		$list[$l_key]['user_icon'] = $l_value['user_icon_165'];
 
		  		// 中间的图
				if(($l_key+2)%3==0)
				{
					$list[$l_key]['type'] = 'middle';			
				}
				else
				{
					$list[$l_key]['type'] = 'one';	
				}
		  	}

		  	$ret[$key]['list'] = $list;

		  	if(!count($ret[$key]['list']))	
		  	{
		  		unset($ret[$key]);
		  	}


		}	

		$ret = array_values($ret);

		$output_arr['data'] = $ret;
		break;	 	 
}

mobile_output($output_arr,false); 
?>
<?php

	include_once 'common.inc.php';
	check_authority(array('model'));
	$model_add_obj  = POCO::singleton('pai_model_add_class');
	$ids  = $_INPUT['ids'] ? $_INPUT['ids'] : '';
	$uid  = $_INPUT['uid'] ? $_INPUT['uid'] : 0;
	//die($uid);
	$liststyles = $_POST['liststyles'] ? $_POST['liststyles'] : '';
	//var_dump($liststyles);exit;
	$model_add_obj->del_style($uid);
	if (!empty($ids) && is_array($ids)) 
	{
		# code...
		foreach ($ids as $key => $id) 
		{
			$data['style']       = $id;
			$data['twoh_price']  = $liststyles[$id][0];
			$data['fourh_price'] = $liststyles[$id][1];
			$data['addh_price']  = $liststyles[$id][2];
			$model_add_obj->add_style($uid, $data);
		}
	}
	$list = $model_add_obj->list_style($uid);
	$str  = ''; 
	if (!empty($list) && is_array($list)) 
	{
	foreach ($list as $key => $style) 
	{
		$style_name = '';
		switch ($style['style']) 
		{
			case 0:
				$style_name = '欧美';
				break;
			case 1:
				$style_name = '情绪';
				break;
			case 2:
				$style_name = '清新';
				break;
			case 3:
				$style_name = '复古';
				break;
			case 4:
				$style_name = '韩系';
				break;
			case 5:
				$style_name = '日系';
				break;
			case 6:
				$style_name = '性感';
				break;
			case 7:
				$style_name = '街拍';
				break;
			case 8:
				$style_name = '胶片';
				break;
			case 9:
				$style_name = '商业';
				break;
			
			default:
				$style_name = '';
				break;
		}
		if ($key != 0) 
		{
			$str .=',';
		}
		$str .= $style_name;
	}
  }
 // print_r($str.'ok-no');
	$arr = array
	(
		'msg' =>  'success!',
		'str' =>  iconv('gb2312', 'UTF-8', $str)
	);
	echo json_encode($arr);

?>
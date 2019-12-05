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
				$style_name = 'ŷ��';
				break;
			case 1:
				$style_name = '����';
				break;
			case 2:
				$style_name = '����';
				break;
			case 3:
				$style_name = '����';
				break;
			case 4:
				$style_name = '��ϵ';
				break;
			case 5:
				$style_name = '��ϵ';
				break;
			case 6:
				$style_name = '�Ը�';
				break;
			case 7:
				$style_name = '����';
				break;
			case 8:
				$style_name = '��Ƭ';
				break;
			case 9:
				$style_name = '��ҵ';
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
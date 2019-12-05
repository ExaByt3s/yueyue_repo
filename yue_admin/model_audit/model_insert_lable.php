<?php 
//临时插入数据使用

	include_once 'common.inc.php';
	/*$data['label'] = '漂亮';
	$str = "100935,100175,101562,101554,100203,100071,102041,101543,100080,100178,100229,100062,100200,100177,100221,100197,100992,102114,102888,102443,100739,101763,101508,101555,101512,100223,100616,100305,100060,100649,100167";
	//$str = "101831,102803";
	$list = explode(',', $str);
	if ($list) 
	{
		foreach ($list as $key => $vo) 
		{
			$uid = (int)$vo;
			$model_add_obj->insert_label($uid, $data);
		}
	}*/
	$model_add_obj  = POCO::singleton('pai_model_add_class');
	//用户获取用户标签 return array $data
	$list = $model_add_obj->get_label_info($user_id);
 	print_r($list);


 ?>
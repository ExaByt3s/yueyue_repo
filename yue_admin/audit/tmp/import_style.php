<?php 

/*
 * 导入所有风格和价格
*/
	//include_once 'common.inc.php';
	include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
	$model_add_obj  = POCO::singleton('pai_model_add_class');
	$model_style_obj = POCO::singleton('pai_model_style_v2_class');
	//$app_style = $model_style_obj->get_model_style_by_user_id($uid);
	$where_str = "1";
	$total_count =  $model_add_obj->get_model_list(true, $where_str);
	$list = $model_add_obj->get_model_list(false, $where_str, 'uid DESC', '0,{$total_count}', $fields = 'uid');
	//print_r($list);exit;
	//$list = array(0=>array('uid'=>100580), 1=>array('uid'=> 103688));
	foreach ($list as $key => $vo) 
	{
		$uid = (int)$vo['uid'];
		if ($uid == 0 || $model_add_obj->list_style($uid)){continue;}
		//$model_add_obj->list_style($uid);
		$app_style = $model_style_obj->get_model_style_by_user_id($uid);
		if (!empty($app_style) && is_array($app_style)) 
	    {
	    	foreach ($app_style as $app_key => $vf) 
	    	{
	    		$data['style'] = change_style_int($vf['style']);
	    		$price      = $vf['price'];
	    		if ($vf['hour'] == 2) 
	    		{
	    			$data['twoh_price']  = $price;
	    			$data['fourh_price'] = 0;
	    		}
	    		else
	    		{
	    			$data['twoh_price']  = 0;
	    			$data['fourh_price'] = $price;
	    		}
	    		$data['addh_price'] = $vf['continue_price'];
	    		//print_r($data);exit;
	    		$model_add_obj->add_style($uid, $data);
	    	}
	    	
	    }
	}
	echo true;
	//print_r($list);exit;



 ?>
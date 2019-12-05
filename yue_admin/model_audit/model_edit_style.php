<?php 

	include_once 'common.inc.php';
	include_once 'include/common_function.php';
	$model_add_obj  = POCO::singleton('pai_model_add_class');
	$model_style_obj = POCO::singleton('pai_model_style_v2_class');
	$tpl = new SmartTemplate("model_edit_style.tpl.htm");
	$uid = $_INPUT['uid'] ? $_INPUT['uid'] : 0;
	$app_style = $model_style_obj->get_model_style_by_user_id($uid);
	//var_dump($app_style);
	//Ñ­»·app·ç¸ñ
	if (!empty($app_style) && is_array($app_style)) 
	{
		foreach ($app_style as $app_key => $vo) 
		{
			$app_style[$app_key]['style'] = change_style_int($vo['style']);
			//var_dump($app_style[$app_key]['style']);
			switch ($app_style[$app_key]['style']) 
			{
				case 0:
					$app_price_a      = $vo['price'];
					if ($vo['hour'] == 2) 
					{
						$tpl->assign('app_two_a', $app_price_a);
					}
					else
					{
						$tpl->assign('app_fourh_a', $app_price_a);
					}
					$continue_price_a = $vo['continue_price'];
					$tpl->assign('continue_price_a', $continue_price_a);
				break;
				case 1:
					$app_price_1      = $vo['price'];
					//die($app_price_1);
					if ($vo['hour'] == 2) 
					{
						$tpl->assign('app_two_1', $app_price_1);
					}
					else
					{
						$tpl->assign('app_fourh_1', $app_price_1);
					}
					$continue_price_1 = $vo['continue_price'];
					$tpl->assign('continue_price_1', $continue_price_1);
				break;
			    case 2:
					$app_price_2      = $vo['price'];
					if ($vo['hour'] == 2) 
					{
						$tpl->assign('app_two_2', $app_price_2);
					}
					else
					{
						$tpl->assign('app_fourh_2', $app_price_2);
					}
					$continue_price_2 = $vo['continue_price'];
					$tpl->assign('continue_price_2', $continue_price_2);
				break;
				case 3:
					$app_price_3      = $vo['price'];
					if ($vo['hour'] == 2) 
					{
						$tpl->assign('app_two_3', $app_price_3);
					}
					else
					{
						$tpl->assign('app_fourh_3', $app_price_3);
					}
					$continue_price_3 = $vo['continue_price'];
					$tpl->assign('continue_price_3', $continue_price_3);
				break;
				case 4:
					$app_price_4      = $vo['price'];
					if ($vo['hour'] == 2) 
					{
						$tpl->assign('app_two_4', $app_price_4);
					}
					else
					{
						$tpl->assign('app_fourh_4', $app_price_4);
					}
					$continue_price_4 = $vo['continue_price'];
					$tpl->assign('continue_price_4', $continue_price_4);
				break;
				case 5:
					$app_price_5      = $vo['price'];
					if ($vo['hour'] == 2) 
					{
						$tpl->assign('app_two_5', $app_price_5);
					}
					else
					{
						$tpl->assign('app_fourh_5', $app_price_5);
					}
					$continue_price_5 = $vo['continue_price'];
					$tpl->assign('continue_price_5', $continue_price_5);
				break;
				case 6:
					$app_price_6      = $vo['price'];
					if ($vo['hour'] == 2) 
					{
						$tpl->assign('app_two_6', $app_price_6);
					}
					else
					{
						$tpl->assign('app_fourh_6', $app_price_6);
					}
					$continue_price_6 = $vo['continue_price'];
					$tpl->assign('continue_price_6', $continue_price_6);
				break;
				case 7:
					$app_price_7      = $vo['price'];
					if ($vo['hour'] == 2) 
					{
						$tpl->assign('app_two_7', $app_price_7);
					}
					else
					{
						$tpl->assign('app_fourh_7', $app_price_7);
					}
					$continue_price_7 = $vo['continue_price'];
					$tpl->assign('continue_price_7', $continue_price_7);
				break;
				case 8:
					$app_price_8      = $vo['price'];
					if ($vo['hour'] == 2) 
					{
						$tpl->assign('app_two_8', $app_price_8);
					}
					else
					{
						$tpl->assign('app_fourh_8', $app_price_8);
					}
					$continue_price_8 = $vo['continue_price'];
					$tpl->assign('continue_price_8', $continue_price_8);
				break;
				case 9:
					$app_price_9      = $vo['price'];
					if ($vo['hour'] == 2) 
					{
						$tpl->assign('app_two_9', $app_price_9);
					}
					else
					{
						$tpl->assign('app_fourh_9', $app_price_9);
					}
					$continue_price_9 = $vo['continue_price'];
					$tpl->assign('continue_price_9', $continue_price_9);
				break;
			}
		}
	}
	$list = $model_add_obj->list_style($uid);
	if (!empty($list) && is_array($list)) 
	{
		foreach ($list as $key => $listsyles) 
		{
			switch ($listsyles['style']) 
			{
				case '0':
					$style_s = "checked='true'";
					$style_two   = $listsyles['twoh_price'];
					$style_fourh = $listsyles['fourh_price'];
					$style_addh  = $listsyles['addh_price'];
					$tpl->assign('style_s', $style_s);
					$tpl->assign('style_two', $style_two);
					$tpl->assign('style_fourh', $style_fourh);
					$tpl->assign('style_addh', $style_addh);
				break;
				case '1':
					$style_1 = "checked='true'";
					$style_1_two   = $listsyles['twoh_price'];
					$style_1_fourh = $listsyles['fourh_price'];
					$style_1_addh  = $listsyles['addh_price'];
					$tpl->assign('style_1', $style_1);
					$tpl->assign('style_1_two', $style_1_two);
					$tpl->assign('style_1_fourh', $style_1_fourh);
					$tpl->assign('style_1_addh', $style_1_addh);
				break;
				case '2':
					$style_2 = "checked='true'";
					$style_2_two   = $listsyles['twoh_price'];
					$style_2_fourh = $listsyles['fourh_price'];
					$style_2_addh  = $listsyles['addh_price'];
					$tpl->assign('style_2', $style_2);
					$tpl->assign('style_2_two', $style_2_two);
					$tpl->assign('style_2_fourh', $style_2_fourh);
					$tpl->assign('style_2_addh', $style_2_addh);
				break;
				case '3':
					$style_3 = "checked='true'";
					$style_3_two   = $listsyles['twoh_price'];
					$style_3_fourh = $listsyles['fourh_price'];
					$style_3_addh  = $listsyles['addh_price'];
					$tpl->assign('style_3', $style_3);
					$tpl->assign('style_3_two', $style_3_two);
					$tpl->assign('style_3_fourh', $style_3_fourh);
					$tpl->assign('style_3_addh', $style_3_addh);
				break;
				case '4':
					$style_4 = "checked='true'";
					$style_4_two   = $listsyles['twoh_price'];
					$style_4_fourh = $listsyles['fourh_price'];
					$style_4_addh  = $listsyles['addh_price'];
					$tpl->assign('style_4', $style_4);
					$tpl->assign('style_4_two', $style_4_two);
					$tpl->assign('style_4_fourh', $style_4_fourh);
					$tpl->assign('style_4_addh', $style_4_addh);
				break;
				case '5':
					$style_5 = "checked='true'";
					$style_5_two   = $listsyles['twoh_price'];
					$style_5_fourh = $listsyles['fourh_price'];
					$style_5_addh  = $listsyles['addh_price'];
					$tpl->assign('style_5', $style_5);
					$tpl->assign('style_5_two', $style_5_two);
					$tpl->assign('style_5_fourh', $style_5_fourh);
					$tpl->assign('style_5_addh', $style_5_addh);
				break;
				case '6':
					$style_6 = "checked='true'";
					$style_6_two   = $listsyles['twoh_price'];
					$style_6_fourh = $listsyles['fourh_price'];
					$style_6_addh  = $listsyles['addh_price'];
					$tpl->assign('style_6', $style_6);
					$tpl->assign('style_6_two', $style_6_two);
					$tpl->assign('style_6_fourh', $style_6_fourh);
					$tpl->assign('style_6_addh', $style_6_addh);
				break;
				case '7':
					$style_7 = "checked='true'";
					$style_7_two   = $listsyles['twoh_price'];
					$style_7_fourh = $listsyles['fourh_price'];
					$style_7_addh  = $listsyles['addh_price'];
					$tpl->assign('style_7', $style_7);
					$tpl->assign('style_7_two', $style_7_two);
					$tpl->assign('style_7_fourh', $style_7_fourh);
					$tpl->assign('style_7_addh', $style_7_addh);
				break;
				case '8':
					$style_8 = "checked='true'";
					$style_8_two   = $listsyles['twoh_price'];
					$style_8_fourh = $listsyles['fourh_price'];
					$style_8_addh  = $listsyles['addh_price'];
					$tpl->assign('style_8', $style_8);
					$tpl->assign('style_8_two', $style_8_two);
					$tpl->assign('style_8_fourh', $style_8_fourh);
					$tpl->assign('style_8_addh', $style_8_addh);
				break;
				case '9':
					$style_9 = "checked='true'";
					$style_9_two   = $listsyles['twoh_price'];
					$style_9_fourh = $listsyles['fourh_price'];
					$style_9_addh  = $listsyles['addh_price'];
					$tpl->assign('style_9', $style_9);
					$tpl->assign('style_9_two', $style_9_two);
					$tpl->assign('style_9_fourh', $style_9_fourh);
					$tpl->assign('style_9_addh', $style_9_addh);
					break;
			}
			# code...
		}
	}

	$tpl->assign('user_id', $uid);
    $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
    $tpl->output();


 ?>
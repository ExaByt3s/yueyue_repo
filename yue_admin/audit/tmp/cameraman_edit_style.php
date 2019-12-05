<?php 

	include_once 'common.inc.php';
	check_authority(array('cameraman'));
	$cameraman_add_obj  = POCO::singleton('pai_cameraman_add_class');
	$tpl = new SmartTemplate("cameraman_edit_style.tpl.htm");
	$act = $_INPUT['act'] ? $_INPUT['act'] : 'edit';
	$uid = $_INPUT['uid'] ? $_INPUT['uid'] : 0;
	switch ($act) {
		//修改时
		case 'edit':
			         $list = $cameraman_add_obj->cameraman_list_style($uid);
	                 if (!empty($list) && is_array($list)) 
	                 {
		               foreach ($list as $key => $listsyles) 
		                {
			              switch ($listsyles['style']) 
			             {
				              case '0':
					          $style_s = "checked='true'";
					          $tpl->assign('style_s', $style_s);
				              break;
				              case '1':
					          $style_1 = "checked='true'";
					          $tpl->assign('style_1', $style_1);
				              break;
				              case '2':
					          $style_2 = "checked='true'";
					          $tpl->assign('style_2', $style_2);
				              break;
				              case '3':
					          $style_3 = "checked='true'";
					          $tpl->assign('style_3', $style_3);
				              break;
				              case '4':
					          $style_4 = "checked='true'";
					          $tpl->assign('style_4', $style_4);
				              break;
				              case '5':
					          $style_5 = "checked='true'";
					          $tpl->assign('style_5', $style_5);
				              break;
				              case '6':
					          $style_6 = "checked='true'";
					          $tpl->assign('style_6', $style_6);
				              break;
				              case '7':
					          $style_7 = "checked='true'";
					          $tpl->assign('style_7', $style_7);
				              break;
				              case '8':
					          $style_8 = "checked='true'";
					          $tpl->assign('style_8', $style_8);
				              break;
				              case '9':
					          $style_9 = "checked='true'";
					          $tpl->assign('style_9', $style_9);
					          break;
			                 }
		                }
	                  }
	              $tpl->assign('user_id', $uid);
                  $tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
                  $tpl->output();
			break;
		case 'update':
			$ids  = $_INPUT['ids'] ? $_INPUT['ids'] : '';
			$cameraman_add_obj->cameraman_del_style($uid);
			if (!empty($ids) && is_array($ids)) 
			{
				foreach ($ids as $key => $style) 
				{
					$data['style'] = $style;
					$cameraman_add_obj->cameraman_add_style($uid, $data);
				}
			}
			$list = $cameraman_add_obj->cameraman_list_style($uid);
			$str = '';
			if (!empty($list) && is_array($list)) 
			{
				foreach ($list as $key => $vo) 
				{
					$style_name = '';
					switch ($vo['style']) 
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
					}
					if ($key != 0) 
					{
						$str .= ',';
					}
					$str .= $style_name;
				}
			}
			$arr  = array
			(

				'msg' =>  'success!',
		        'str' =>  iconv('gb2312', 'UTF-8', $str)
			);
			echo json_encode($arr);
			break;
	}
	


 ?>
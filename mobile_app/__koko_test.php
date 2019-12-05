<?php
/**
 * 测试 
 */
include_once('protocol_common.inc.php');
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
header("Content-type: text/html; charset=utf-8");

function mobile_app_curl($url, $post_data)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER,  false);
	curl_setopt($ch, CURLOPT_COOKIE, "visitor_flag=1386571300; visitor_r=; cmt_hash=2746320925; _topbar_introduction=1; lastphoto_show_mode=list; session_id=67cd1e92439b03d60254f6afd6ada9c7; session_ip=112.94.240.51; session_ip_location=101029001; session_auth_hash=05d30ac6bf7bb8d1902df17a936ce6a4; g_session_id=3808f8022c9c8c16b8f5b6b7ddeb57c7; member_id=65849144; fav_userid=65849144; remember_userid=65849144; nickname=Mr.Ceclian; fav_username=Mr.Ceclian; activity_level=fans; pass_hash=f5544bdf101337398cbb8b07a3b05fe6");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, array('req' => $post_data));
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}
function sub_request($user_id,$service_id,$question_list,$seller_id,$prices)//$user_id用户ID $service_id问卷类型 $question_list答案数据格式参照http://113.107.204.251/wiki/index.php/首页#.E6.8F.90.E4.BA.A4.E9.97.AE.E5.8D.B7.E4.BF.A1.E6.81.AF.EF.BC.8C.E7.94.9F.E6.88.90.E9.9C.80.E6.B1.82
{
    $user_id = (int)$user_id;
	$service_id = (int)$service_id;
	//获取服务信息
	$task_service_obj = POCO::singleton('pai_task_service_class');
	$service_info = $task_service_obj->get_service_info($service_id);
	$title = trim($service_info['service_name']);
	//获取商家信息
	$task_profile_obj = POCO::singleton('pai_task_profile_class');
	$profile_info = $task_profile_obj->check_seller_by_user_id($seller_id);
	if(!$profile_info)
	{
		$submit_ret['result'] = -1;//result返回1为成功
		$submit_ret['message'] = '商家ID错误';
		return $submit_ret;
	}
	//获取用户信息
	$pai_user_obj = POCO::singleton('pai_user_class');
	$user_info = $pai_user_obj->get_user_info($user_id);
	$cellphone = trim($user_info['cellphone']);
	//整理资料
	$more_info = array(
		'title' => $title,
		'cellphone' => $cellphone,
		'email' => '',
	);
	$task_request_obj = POCO::singleton('pai_task_request_class');
	$auto_data=array(
	                 'user_id'=>$seller_id,
					 'price'=>$prices,
					 'content'=>'test',
					 );
	$submit_ret = $task_request_obj->submit_request($user_id, $service_id, $more_info, $question_list,true,$auto_data);
	//$submit_ret返回值为array,元素有以下
	//$submit_ret['result'];//result返回1为成功
	//$submit_ret['message'];
	//$submit_ret['request_id'];
	return $submit_ret;
}


$op = trim($_INPUT['op']);
$id = trim($_INPUT['id']);
if($op == 'get_person_info')
{
	$url = 'http://yp.yueus.com/mobile_app/task/get_quote_info.php';
	$param = array(
		'user_id'  => 100031,
		'quote_id'  => 219,
	);
	$post_data = array(
		'version'   => '1.0.6',
		'os_type'   => 'android',
		'ctime'     => time(),
		'app_name'  => 'poco_yuepai_android',
		'sign_code' => substr(md5('poco_'.json_encode($param).'_app'), 5, -8),
		'is_enc'    => 0,
		'param'     => $param,
	);
	$post_data = json_encode($post_data);
    $post_data = iconv('GBK', 'UTF-8', $post_data);
	print_r(mobile_app_curl($url, $post_data));	
}
elseif( $op=='get_question_list' )
{
	$url = 'http://yp.yueus.com/mobile_app/task/get_question_list.php';
	$param = array(
		'service_id' => $id?$id:2,
	);
	$post_data = array(
		'version'   => '1.0.6',
		'os_type'   => 'android',
		'ctime'     => time(),
		'app_name'  => 'poco_yuepai_android',
		'sign_code' => substr(md5('poco_'.json_encode($param).'_app'), 5, -8),
		'is_enc'    => 0,
		'param'     => $param,
	);
	$post_data = json_encode($post_data);
    $post_data = iconv('GBK', 'UTF-8', $post_data);
	print_r(mobile_app_curl($url, $post_data));
}
elseif( $op=='submit_request' )
{
	$post_arr=array(
					  'question_detail_list' => array(
					                                  array(
															  'question_titleid'=>17,
															  'data'=>array(
																		   array(
																				 'anwserid'=>72, 
																				 'type'=>6, 
																				 'message'=>'101029049'
																				 ),//
																			 )
															  ),
					                                  array(
															  'question_titleid'=>12,
															  'data'=>array(
																		   array(
																				 'anwserid'=>28, 
																				 'type'=>0, 
																				 'message'=>'abcde 0x27#ab'
																				 ),//
																			 )
															  ),
					                                  array(
															  'question_titleid'=>20,
															  'data'=>array(
																		   array(
																				 'anwserid'=>63, 
																				 'type'=>7, 
																				 'message'=>'2015-04-15',
																				 ),//
																		   array(
																				 'anwserid'=>64, 
																				 'type'=>8, 
																				 'message'=>'12:15'
																				 ),//
																			 )
															  ),
					                                  array(
															  'question_titleid'=>21,
															  'data'=>array(
																		   array(
																				 'anwserid'=>65, 
																				 'type'=>7, 
																				 'message'=>'2015-04-15',
																				 ),//
																		   array(
																				 'anwserid'=>66, 
																				 'type'=>9, 
																				 'message'=>'5 days'
																				 ),//
																			 )
															  ),					                                  
													  )
					  );
					  
					  

	$url = 'http://yp.yueus.com/mobile_app/task/submit_request.php';
	$param = array(
	  'user_id' => 109650,
	  'service_id' => 4, //问卷类型
	  'version'=>'2015041510243512365', //问卷版本号
	  'question_id' => 2, //问卷ID
	  'data'=>$post_arr,
	 );
	 
	$param = poco_iconv_arr($param,'GBK', 'UTF-8');
	$post_data = array(
		'version'   => '1.0.6',
		'os_type'   => 'android',
		'ctime'     => time(),
		'app_name'  => 'poco_yuepai_android',
		'sign_code' => substr(md5('poco_'.json_encode($param).'_app'), 5, -8),
		'is_enc'    => 0,
		'param'     => $param,
	);
	$post_data = json_encode($post_data);
    $post_data = iconv('GBK', 'UTF-8', $post_data);
	print_r(mobile_app_curl($url, $post_data));
}
elseif( $op=='del_request' )
{
	$url = 'http://yp.yueus.com/mobile_app/task/del_request.php';
	$param = array(
	  'user_id' => 100031,
	  'request_id' => 1203,
	 );
	 
	$param = poco_iconv_arr($param,'GBK', 'UTF-8');
	$post_data = array(
		'version'   => '1.0.6',
		'os_type'   => 'android',
		'ctime'     => time(),
		'app_name'  => 'poco_yuepai_android',
		'sign_code' => substr(md5('poco_'.json_encode($param).'_app'), 5, -8),
		'is_enc'    => 0,
		'param'     => $param,
	);
	$post_data = json_encode($post_data);
    $post_data = iconv('GBK', 'UTF-8', $post_data);
	print_r(mobile_app_curl($url, $post_data));
}
elseif($op == 'get_q_list')
{
	$task_questionnaire_obj = POCO::singleton('pai_task_questionnaire_class');
	$id=$id?$id:2;
	$re = $task_questionnaire_obj -> get_questionnaire_list($id,2);
	//$re = $task_questionnaire_obj -> get_questionnaire_version_list($id);
	print_r($re);
	exit;
	$cp = new poco_communication_protocol_class ();
	//$re = poco_iconv_arr($re, 'GBK','UTF-8');
	$options ['data']['list'] = $re;
    $cp->output ( $options );
	//$post_data = json_encode($re);
	//print_r($post_data);
}
elseif($op == 'show_q_data')
{
	$task_questionnaire_obj = POCO::singleton('pai_task_questionnaire_class');
	$id = $id?$id:39;
	echo $id;
	$re = $task_questionnaire_obj -> show_questionnaire_data($id);
	print_r($re);
	exit;
}
elseif($op == 'show_u_history')
{
	$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
	$user_id = $id?$id:39;
	echo $id;
	$re = $task_quotes_obj -> get_quotes_history_by_userid($id);
	print_r($re);
	exit;
}
elseif($op == 'del_request')
{
	if($id)
	{
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$re = $task_request_obj->del_request($id);
	}
	echo $id;
	print_r($re);
	exit;
}
elseif($op == 'send_lead')
{
	if($id)
	{
		$task_obj = POCO::singleton('pai_task_request_class');
		$re = $task_obj->submit_lead_by_request_id_all($id);
		//$user_id="109650,102610,101870,12352";
		//$re = $task_obj->send_request_lead_by_artificial($id,$user_id);
	}
	echo $id;
	print_r($re);
	exit;
}
elseif($op == 'get_lead_mode')
{
	$name = 'G_PAI_TASK_REQUEST_LEAD_MODE';
	$task_setting_obj = POCO::singleton('pai_task_setting_class');
	$lead_mode = $task_setting_obj->get($name);
	var_dump($lead_mode);
}
elseif($op == 'add_log')
{
	$obj = POCO::singleton('pai_task_admin_log_class');
	$request_id=1;
	//$re = $obj->add_log('109650',1003,$_INPUT,'abcdefg',$request_id);
	var_dump($re);
	$list = $obj->get_log_by_type(1003,$request_id);
	print_r($list);
}
elseif($op == 'profile')
{
	$obj = POCO::singleton('pai_task_profile_class');
	$request_id=1;
	//$re = $obj->add_log('109650',1003,$_INPUT,'abcdefg',$request_id);
	$list = $obj->get_user_profile_list(109650);
	print_r($list);
}
elseif($op == 're_list')
{
	$obj = POCO::singleton('pai_task_request_class');
	$list = $obj->get_request_detail_list_by_user_id(110482);
	print_r($list);
}
elseif($op == 'goodslist')
{
	$obj = POCO::singleton('pai_task_goods_class');
	$data = array(
	              'profile_id'=>10,
				  );
	$list = $obj->get_user_goods_list($data);
	print_r($list);
}
elseif($op == 'goodsdetail')
{
	header("Content-type: text/html; charset=gb2312");
	$obj = POCO::singleton('pai_task_goods_class');
	if($_POST)
	{
		$img = explode("\r\n",$_POST['img']);
		$data = $_POST;
		//$data['img'] = $img;
		$img_data = array();
		foreach($img as $val)
		{
			$img_data[] = array('img_url'=>$val);
			//$data['img'][] = array('img_url'=>$val);
		}
		$data['img'] = $img_data;		
		//print_r($data);
		$obj->update_goods($data['goods_id'],$data);
		exit;
	}
	$id=$id?$id:17;
	$list = $obj->get_goods_info($id);
	//print_r($list);
	///////////////////////
		echo '
			<form id="form1" name="form1" method="post" action="" target="_blank">
			  <table width="90%" border="1" align="center" cellpadding="1" cellspacing="1">
			  ';
			echo '
				  <tr>
				      <td colspan="2" align="center" height="50">'.$list['service_data']['service_name'].'</td>
				  </tr>
					';
		foreach($list['default_data'] as $val)
		{	  
			echo '
				  <tr>
					  <td width="26%" height="25">'.$val['name'].'</td>
					  <td width="74%" height="25">';
			if($val['input']==1)
			{
				echo '<input name="'.$val['key'].'" type="text" id="'.$val['key'].'" value="'.$val['value'].'" size="50" maxlength="50" />';
			}
			else
			{
				echo '<textarea name="'.$val['key'].'" cols="50" rows="5" id="'.$val['key'].'">'.$val['value'].'</textarea>';
			}
			echo '
					  </td>
					</tr>
					';
		}
		foreach($list['time_data'] as $val)
		{	  
			echo '
				  <tr>
					  <td width="26%" height="25">'.$val['name'].'</td>
					  <td width="74%" height="25">';
			if($val['input']==1)
			{
				echo '<input name="'.$val['key'].'" type="text" id="'.$val['key'].'" value="'.$val['value'].'" size="50" maxlength="50" />';
			}
			else
			{
				echo '<textarea name="'.$val['key'].'" cols="50" rows="5" id="'.$val['key'].'">'.$val['value'].'</textarea>';
			}
			echo '
					  </td>
					</tr>
					';
		}
		foreach($list['attribute_data'] as $val)
		{	  
			echo '
				  <tr>
					  <td width="26%" height="25">'.$val['name'].'</td>
					  <td width="74%" height="25">';
			if($val['input']==1)
			{
				echo '<input name="'.$val['key'].'" type="text" id="'.$val['key'].'" value="'.$val['value'].'" size="50" maxlength="50" />';
			}
			else
			{
				echo '<textarea name="'.$val['key'].'" cols="50" rows="5" id="'.$val['key'].'">'.$val['value'].'</textarea>';
			}
			echo '
					  </td>
					</tr>
					';
		}
			echo '
				  <tr>
					  <td width="26%" height="25">'.$list['image_data']['name'].'</td>
					  <td width="74%" height="25">
					  <textarea name="'.$list['image_data']['key'].'" cols="150" rows="5" id="'.$list['image_data']['key'].'">'.$list['image_data']['value'].'</textarea>			
					  </td>
					</tr>
					';
		echo '
				<tr>
				  <td colspan="2" align="center"><input type="submit" name="button" id="button" value="提交" /><input name="goods_id" type="hidden" id="goods_id" value="'.$list['goods_data']['id'].'" /></td>
				</tr>
			  </table>
			</form>
		';	
	///////////////////////	
}
elseif($op == 'goods')
{
	header("Content-type: text/html; charset=gb2312");
	$obj = POCO::singleton('pai_task_goods_class');
	if($_POST)
	{
		$img = explode("\r\n",$_POST['img']);
		$data = $_POST;
		//$data['img'] = $img;
		$img_data = array();
		foreach($img as $val)
		{
			$img_data[] = array('img_url'=>$val);
			//$data['img'][] = array('img_url'=>$val);
		}
		$data['img'] = $img_data;
		//print_r($data);
		$in = $obj->add_goods($data);
		print_r($in);
		exit;
	}
	$obj_user = POCO::singleton('pai_task_seller_class');
	$user_list = $obj_user->get_seller_all_list();
	$user_id = $_GET['user_id']?$_GET['user_id']:$user_list[0]['user_id'];
	$profile_id = $_GET['profile_id']?$_GET['profile_id']:$profile_list[0]['profile_id'];
	$obj_profile = POCO::singleton('pai_task_profile_class');
	$profile_list = $obj_profile->get_user_profile_list($user_id);	
	$profile_id = $_GET['profile_id']?$_GET['profile_id']:$profile_list[0]['profile_id'];
	foreach($profile_list as $val)
	{
		if($profile_id == $val['profile_id'])
		{
			$id = $val['service_id'];
		}
		break;
	}
	//print_r($profile_list);
	//$id = $id?$id:1;
	$list = $obj->show_goods_data($id);
		echo '
			<form id="form1" name="form1" method="post" action="" target="_blank">
			  <table width="90%" border="1" align="center" cellpadding="1" cellspacing="1">
			  ';
			echo '
				  <tr>
				      <td colspan="2" align="center" height="50">'.$list['service_data']['service_name'].'</td>
				  </tr>
				  <tr>
					  <td width="26%" height="25">用户ID</td>
					  <td width="74%" height="25">';
			echo '
						<select name="user_id" id="user_id" onchange="javascript:window.location.href=\'__koko_test.php?op=goods&user_id=\'+this.value">
						';
			foreach($user_list as $val)
			{
				echo '<option value="'.$val['user_id'].'"'.($user_id==$val['user_id']?' selected="selected"':'').'>['.$val['user_id'].']'.$val['nickname'].'</option>';
			}
			echo '
						</select>
			';
			echo '
					  </td>
					</tr>
				  <tr>
					  <td width="26%" height="25">ProfileID</td>
					  <td width="74%" height="25">';
			echo '
						<select name="profile_id" id="profile_id" onchange="javascript:window.location.href=\'__koko_test.php?op=goods&user_id='.$_GET['user_id'].'&profile_id=\'+this.value">
						';
			foreach($profile_list as $val)
			{
				echo '<option value="'.$val['profile_id'].'"'.($profile_id==$val['profile_id']?' selected="selected"':'').'>['.$val['profile_id'].']'.$val['title'].'</option>';
			}
			echo '
						</select>
			';
			echo '
					  </td>
					</tr>
					';
		foreach($list['default_data'] as $val)
		{	  
			echo '
				  <tr>
					  <td width="26%" height="25">'.$val['name'].'</td>
					  <td width="74%" height="25">';
			if($val['input']==1)
			{
				echo '<input name="'.$val['key'].'" type="text" id="'.$val['key'].'" value="" size="50" maxlength="50" />';
			}
			else
			{
				echo '<textarea name="'.$val['key'].'" cols="50" rows="5" id="'.$val['key'].'"></textarea>';
			}
			echo '
					  </td>
					</tr>
					';
		}
		foreach($list['time_data'] as $val)
		{	  
			echo '
				  <tr>
					  <td width="26%" height="25">'.$val['name'].'</td>
					  <td width="74%" height="25">';
			if($val['input']==1)
			{
				echo '<input name="'.$val['key'].'" type="text" id="'.$val['key'].'" value="" size="50" maxlength="50" />';
			}
			else
			{
				echo '<textarea name="'.$val['key'].'" cols="50" rows="5" id="'.$val['key'].'"></textarea>';
			}
			echo '
					  </td>
					</tr>
					';
		}
		foreach($list['attribute_data'] as $val)
		{	  
			echo '
				  <tr>
					  <td width="26%" height="25">'.$val['name'].'</td>
					  <td width="74%" height="25">';
			if($val['input']==1)
			{
				echo '<input name="'.$val['key'].'" type="text" id="'.$val['key'].'" value="" size="50" maxlength="50" />';
			}
			else
			{
				echo '<textarea name="'.$val['key'].'" cols="50" rows="5" id="'.$val['key'].'"></textarea>';
			}
			echo '
					  </td>
					</tr>
					';
		}
			echo '
				  <tr>
					  <td width="26%" height="25">'.$list['image_data']['name'].'</td>
					  <td width="74%" height="25">
					  <textarea name="'.$list['image_data']['key'].'" cols="150" rows="5" id="'.$list['image_data']['key'].'">'.$list['image_data']['value'].'</textarea>			
					  </td>
					</tr>
					';
		echo '
				<tr>
				  <td colspan="2" align="center"><input type="submit" name="button" id="button" value="提交" /></td>
				</tr>
			  </table>
			</form>
		';	
}
elseif($op == 'auto_re')
{
	/*
	$user_id = 110482;
	$service_id = 4;
	$post_arr=array(
					  'question_detail_list' => array(
					                                  array(
															  'question_titleid'=>17,
															  'data'=>array(
																		   array(
																				 'anwserid'=>72, 
																				 'type'=>6, 
																				 'message'=>'101029049'
																				 ),//
																			 )
															  ),
					                                  array(
															  'question_titleid'=>12,
															  'data'=>array(
																		   array(
																				 'anwserid'=>28, 
																				 'type'=>0, 
																				 'message'=>'abcde 0x27#ab'
																				 ),//
																			 )
															  ),
					                                  array(
															  'question_titleid'=>20,
															  'data'=>array(
																		   array(
																				 'anwserid'=>63, 
																				 'type'=>7, 
																				 'message'=>'2015-04-15',
																				 ),//
																		   array(
																				 'anwserid'=>64, 
																				 'type'=>8, 
																				 'message'=>'12:15'
																				 ),//
																			 )
															  ),
					                                  array(
															  'question_titleid'=>21,
															  'data'=>array(
																		   array(
																				 'anwserid'=>65, 
																				 'type'=>7, 
																				 'message'=>'2015-04-15',
																				 ),//
																		   array(
																				 'anwserid'=>66, 
																				 'type'=>9, 
																				 'message'=>'5 days'
																				 ),//
																			 )
															  ),					                                  
													  )
					  );  
	$question_list = array(
						  'user_id' =>$user_id,
						  'service_id' => $service_id, //问卷类型
						  'version'=>'2015041510243512365', //问卷版本号
						  'question_id' => 2, //问卷ID
						  'data'=>$post_arr,
						 );						 
	*/
	///////////////////////
	if($_POST)
	{
		$array = array(
					  1=>array(
							   'question_id'=>4,
							   'version'=>'2015042917123565841',
							   'data'=>'a:6:{s:4:"data";a:1:{s:20:"question_detail_list";a:9:{i:0;a:2:{s:16:"question_titleid";s:2:"67";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"273";s:4:"type";s:1:"6";s:7:"message";s:9:"101029001";}}}i:1;a:2:{s:16:"question_titleid";s:2:"68";s:4:"data";a:3:{i:0;a:3:{s:8:"anwserid";s:3:"275";s:4:"type";s:1:"7";s:7:"message";s:8:"2015-6-1";}i:1;a:3:{s:8:"anwserid";s:3:"283";s:4:"type";s:1:"8";s:7:"message";s:4:"9:30";}i:2;a:3:{s:8:"anwserid";s:3:"274";s:4:"type";s:1:"9";s:7:"message";s:5:"3小时";}}}i:2;a:2:{s:16:"question_titleid";s:2:"43";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"165";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:3;a:2:{s:16:"question_titleid";s:2:"44";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"173";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:4;a:2:{s:16:"question_titleid";s:2:"45";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"176";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:5;a:2:{s:16:"question_titleid";s:2:"46";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"178";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:6;a:2:{s:16:"question_titleid";s:2:"48";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"182";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:7;a:2:{s:16:"question_titleid";s:2:"49";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"185";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:8;a:2:{s:16:"question_titleid";s:2:"69";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"278";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}}}s:10:"service_id";s:1:"1";s:7:"user_id";s:6:"111194";s:11:"question_id";s:1:"4";s:11:"location_id";s:9:"101029001";s:7:"version";s:19:"2015042917123565841";}',
							   ),
					  2=>array(
							   'question_id'=>6,
							   'version'=>'2015042218082186593',
							   'data'=>'a:6:{s:4:"data";a:1:{s:20:"question_detail_list";a:7:{i:0;a:2:{s:16:"question_titleid";s:2:"56";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"213";s:4:"type";s:1:"6";s:7:"message";s:9:"101019001";}}}i:1;a:2:{s:16:"question_titleid";s:2:"57";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"214";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:2;a:2:{s:16:"question_titleid";s:2:"58";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"218";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:3;a:2:{s:16:"question_titleid";s:2:"59";s:4:"data";a:3:{i:0;a:3:{s:8:"anwserid";s:3:"221";s:4:"type";s:1:"0";s:7:"message";s:0:"";}i:1;a:3:{s:8:"anwserid";s:3:"222";s:4:"type";s:1:"0";s:7:"message";s:0:"";}i:2;a:3:{s:8:"anwserid";s:3:"226";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:4;a:2:{s:16:"question_titleid";s:2:"60";s:4:"data";a:3:{i:0;a:3:{s:8:"anwserid";s:3:"230";s:4:"type";s:1:"0";s:7:"message";s:0:"";}i:1;a:3:{s:8:"anwserid";s:3:"231";s:4:"type";s:1:"0";s:7:"message";s:0:"";}i:2;a:3:{s:8:"anwserid";s:3:"232";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:5;a:2:{s:16:"question_titleid";s:2:"61";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"233";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:6;a:2:{s:16:"question_titleid";s:2:"62";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"235";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}}}s:10:"service_id";s:1:"2";s:7:"user_id";s:6:"111425";s:11:"question_id";s:1:"6";s:11:"location_id";s:9:"101029001";s:7:"version";s:19:"2015042218082186593";}',
							   ),
					  3=>array(
							   'question_id'=>5,
							   'version'=>'2015042917132445218',
							   'data'=>'a:6:{s:4:"data";a:1:{s:20:"question_detail_list";a:8:{i:0;a:2:{s:16:"question_titleid";s:2:"70";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"280";s:4:"type";s:1:"6";s:7:"message";s:9:"101004001";}}}i:1;a:2:{s:16:"question_titleid";s:2:"71";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"281";s:4:"type";s:1:"7";s:7:"message";s:9:"2015-5-10";}}}i:2;a:2:{s:16:"question_titleid";s:2:"50";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"189";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:3;a:2:{s:16:"question_titleid";s:2:"51";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"195";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:4;a:2:{s:16:"question_titleid";s:2:"52";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"196";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:5;a:2:{s:16:"question_titleid";s:2:"53";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"205";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:6;a:2:{s:16:"question_titleid";s:2:"54";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"207";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:7;a:2:{s:16:"question_titleid";s:2:"55";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"212";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}}}s:10:"service_id";s:1:"3";s:7:"user_id";s:6:"109530";s:11:"question_id";s:1:"5";s:11:"location_id";s:9:"101004001";s:7:"version";s:19:"2015042917132445218";}',
							   ),
					  );
		$user_id = $_POST['user_id']?$_POST['user_id']:110482;
		$service_id = $_POST['services_id']?$_POST['services_id']:1;
		$prices = $_POST['prices']?$_POST['prices']:10000;
		$seller_id = $_POST['seller_id']?$_POST['seller_id']:109650;
		$data = unserialize($array[$service_id]['data']);
		if(!$data)
		{
			$data = unserialize(iconv('gbk','utf-8',$array[$service_id]['data']));
		}
		$question_list = array(
							  'user_id' =>$user_id,
							  'service_id' => $service_id,
							  'version'=>$array[$service_id]['version'],
							  'question_id' => $array[$service_id]['question_id'],
							  'data'=>$data['data'],
							 );
		///////////////////////
		$re=sub_request($user_id,$service_id,$question_list,$seller_id,$prices);
		print_r($re);
	}
	else
	{
		echo '
<form id="form1" name="form1" method="post" action="">
  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td width="26%" height="25">需求用户ID</td>
      <td width="74%" height="25"><label for="user_id"></label>
      <input name="user_id" type="text" id="user_id" value="110482" size="10" maxlength="6" /></td>
    </tr>
    <tr>
      <td height="25">商户</td>
      <td height="25"><input name="seller_id" type="text" id="seller_id" value="109650" size="10" maxlength="6" /></td>
    </tr>
    <tr>
      <td height="25">服务类型</td>
      <td height="25"><label for="services_id"></label>
        <select name="services_id" id="services_id">
          <option value="1">影棚租赁</option>
          <option value="2">摄影培训</option>
          <option value="3">化妆服务</option>
      </select></td>
    </tr>
    <tr>
      <td height="25">金额</td>
      <td height="25"><input name="prices" type="text" id="prices" value="0.01" size="10" maxlength="6" /></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><input type="submit" name="button" id="button" value="提交" /></td>
    </tr>
  </table>
</form>
		';
	}
}
else
{
	die('op error');
}

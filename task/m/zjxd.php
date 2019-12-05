<?php

 
/**
 * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */
$file_dir = dirname(__FILE__);
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once($file_dir.'/../task_common.inc.php');

$redirect_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

if (!$yue_login_id) 
{
	header('Location:login.php?r_url='.urlencode($redirect_url));
	exit();
}

// ����̼ң���ֱ���˳���¼
$get_all_profile_obj = POCO::singleton('pai_task_profile_class');
$is_supplier = $get_all_profile_obj->check_seller_by_user_id($yue_login_id);

if ($is_supplier) 
{
	$user_obj = POCO::singleton ( 'pai_user_class' );
	$user_obj->logout();
	js_pop_msg("�����̼��˺�Ŷ������ֱ���µ�", false,"./login.php?r_url=".urlencode($redirect_url));

}

// Ȩ���ļ�
include_once($file_dir.'/../task_for_normal_auth_common.inc.php');


function sub_request($user_id,$service_id,$question_list,$seller_id,$prices)
{
	$user_id = (int)$user_id;
	$service_id = (int)$service_id; 
	//��ȡ������Ϣ
	$task_service_obj = POCO::singleton('pai_task_service_class');
	$service_info = $task_service_obj->get_service_info($service_id);
	$title = trim($service_info['service_name']);
	//��ȡ�̼���Ϣ
	$task_profile_obj = POCO::singleton('pai_task_profile_class');
	$profile_info = $task_profile_obj->check_seller_by_user_id($seller_id);
	if(!$profile_info)
	{
		$submit_ret['result'] = -1;//result����1Ϊ�ɹ�
		$submit_ret['message'] = '�̼�ID����';
		return $submit_ret;
	}
	//��ȡ�û���Ϣ
	$pai_user_obj = POCO::singleton('pai_user_class');
	$user_info = $pai_user_obj->get_user_info($user_id);
	$cellphone = trim($user_info['cellphone']);
	//��������
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
	//$submit_ret����ֵΪarray,Ԫ��������
	//$submit_ret['result'];//result����1Ϊ�ɹ�
	//$submit_ret['message'];
	//$submit_ret['request_id'];
	return $submit_ret;
}

if(!$_INPUT['add'])
{
	// ��ȡ�б�
	$task_service_obj = POCO::singleton('pai_task_service_class');
	$service_list = $task_service_obj->get_service_list(false,'status=1');


	$tpl = $my_app_pai->getView('zjxd.tpl.html');
	$tpl->assign('service_list', $service_list);  //�����б�
	$tpl->assign('time', time());  //�����

	$tpl->output();
}
else
{
	//�ύ����
	
	$array = array(
				  1=>array(
						   'question_id'=>4,
						   'version'=>'2015042917123565841',
						   'data'=>'a:6:{s:4:"data";a:1:{s:20:"question_detail_list";a:9:{i:0;a:2:{s:16:"question_titleid";s:2:"67";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"273";s:4:"type";s:1:"6";s:7:"message";s:9:"101029001";}}}i:1;a:2:{s:16:"question_titleid";s:2:"68";s:4:"data";a:3:{i:0;a:3:{s:8:"anwserid";s:3:"275";s:4:"type";s:1:"7";s:7:"message";s:10:"'.date('Y-m-d',strtotime('+5 day')).'";}i:1;a:3:{s:8:"anwserid";s:3:"283";s:4:"type";s:1:"8";s:7:"message";s:4:"9:30";}i:2;a:3:{s:8:"anwserid";s:3:"274";s:4:"type";s:1:"9";s:7:"message";s:5:"3Сʱ";}}}i:2;a:2:{s:16:"question_titleid";s:2:"43";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"165";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:3;a:2:{s:16:"question_titleid";s:2:"44";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"173";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:4;a:2:{s:16:"question_titleid";s:2:"45";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"176";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:5;a:2:{s:16:"question_titleid";s:2:"46";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"178";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:6;a:2:{s:16:"question_titleid";s:2:"48";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"182";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:7;a:2:{s:16:"question_titleid";s:2:"49";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"185";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:8;a:2:{s:16:"question_titleid";s:2:"69";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"278";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}}}s:10:"service_id";s:1:"1";s:7:"user_id";s:6:"111194";s:11:"question_id";s:1:"4";s:11:"location_id";s:9:"101029001";s:7:"version";s:19:"2015042917123565841";}',
						   ),
				  2=>array(
						   'question_id'=>6,
						   'version'=>'2015042218082186593',
						   'data'=>'a:6:{s:4:"data";a:1:{s:20:"question_detail_list";a:7:{i:0;a:2:{s:16:"question_titleid";s:2:"56";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"213";s:4:"type";s:1:"6";s:7:"message";s:9:"101019001";}}}i:1;a:2:{s:16:"question_titleid";s:2:"57";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"214";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:2;a:2:{s:16:"question_titleid";s:2:"58";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"218";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:3;a:2:{s:16:"question_titleid";s:2:"59";s:4:"data";a:3:{i:0;a:3:{s:8:"anwserid";s:3:"221";s:4:"type";s:1:"0";s:7:"message";s:0:"";}i:1;a:3:{s:8:"anwserid";s:3:"222";s:4:"type";s:1:"0";s:7:"message";s:0:"";}i:2;a:3:{s:8:"anwserid";s:3:"226";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:4;a:2:{s:16:"question_titleid";s:2:"60";s:4:"data";a:3:{i:0;a:3:{s:8:"anwserid";s:3:"230";s:4:"type";s:1:"0";s:7:"message";s:0:"";}i:1;a:3:{s:8:"anwserid";s:3:"231";s:4:"type";s:1:"0";s:7:"message";s:0:"";}i:2;a:3:{s:8:"anwserid";s:3:"232";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:5;a:2:{s:16:"question_titleid";s:2:"61";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"233";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:6;a:2:{s:16:"question_titleid";s:2:"62";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"235";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}}}s:10:"service_id";s:1:"2";s:7:"user_id";s:6:"111425";s:11:"question_id";s:1:"6";s:11:"location_id";s:9:"101029001";s:7:"version";s:19:"2015042218082186593";}',
						   ),
				  3=>array(
						   'question_id'=>5,
						   'version'=>'2015042917132445218',
						   'data'=>'a:6:{s:4:"data";a:1:{s:20:"question_detail_list";a:8:{i:0;a:2:{s:16:"question_titleid";s:2:"70";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"280";s:4:"type";s:1:"6";s:7:"message";s:9:"101029001";}}}i:1;a:2:{s:16:"question_titleid";s:2:"71";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"281";s:4:"type";s:1:"7";s:7:"message";s:10:"'.date('Y-m-d',strtotime('+5 day')).'";}}}i:2;a:2:{s:16:"question_titleid";s:2:"50";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"189";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:3;a:2:{s:16:"question_titleid";s:2:"51";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"195";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:4;a:2:{s:16:"question_titleid";s:2:"52";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"196";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:5;a:2:{s:16:"question_titleid";s:2:"53";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"205";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:6;a:2:{s:16:"question_titleid";s:2:"54";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"207";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}i:7;a:2:{s:16:"question_titleid";s:2:"55";s:4:"data";a:1:{i:0;a:3:{s:8:"anwserid";s:3:"212";s:4:"type";s:1:"0";s:7:"message";s:0:"";}}}}}s:10:"service_id";s:1:"3";s:7:"user_id";s:6:"109530";s:11:"question_id";s:1:"5";s:11:"location_id";s:9:"101029001";s:7:"version";s:19:"2015042917132445218";}',
						   ),
				  );
	$user_id = $yue_login_id;
	$service_id = (int)$_POST['services_id'];
	$prices = (int)$_POST['prices'];
	$seller_id = (int)$_POST['seller_id'];

	if(!$service_id)
	{
		$output_arr['result'] = 0;
		$output_arr['message'] = '��ѡ���������';
		mobile_output($output_arr,false); 
		exit();
	}
	if(!$prices)
	{
		$output_arr['result'] = 0;
		$output_arr['message'] = '��������';
		mobile_output($output_arr,false); 
		exit();
	}
	if(!$seller_id)
	{
		$output_arr['result'] = 0;
		$output_arr['message'] = '�������̼�ID';
		mobile_output($output_arr,false); 
		exit();
	}

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

	$output_arr['result'] = $re['result'];
	$output_arr['message'] = $re['message'];
	mobile_output($output_arr,false); 
}


?>
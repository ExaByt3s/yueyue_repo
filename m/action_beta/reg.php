<?php
/*
    ע��ҳ��
*/
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$type = trim($_INPUT['type']);


 /**
  * ҳ�����
  * 
  * @param int $info_type ��Ϣ��key
  * @param int $code      
  * @return null
  */
function out_put( $info_type,$code=0,$return_user_info=false,$user_id='',$more_info=array() ){

	$info_arr = array(

		'phone_format_error'		=>'�ֻ������ʽ����',
		'phone_is_bind'				=>'�����Ѱ󶨹�',
		'verify_code_sent_error'	=>'��֤�뷢�ʹ���',
		'verify_code_sent_success'	=>'��֤�뷢�ͳɹ�',
		'weixin_is_bind'=>'��΢�ź��Ѱ󶨹�ԼԼ��',
		'open_id_is_null'=>'���ȵ�΢����Ȩ',

		/*��֤��*/		
		'verify_code_error'		   =>'��֤���ʽ����',
		'verify_code_check_error'  =>'��֤�벻��ȷ',
		'verify_code_check_success'=>'��֤�ɹ�',
	
		'user_not_reg'=>'�û�ע���쳣',
		'role_choose_error' => '��ɫѡ�����',
		'choose_role_success'=>'��ɫѡ��ɹ�',
	    'role_has_choose'=>'��ɫ��ѡ�����',
	    'phone_is_null'=>'�ֻ�����Ϊ��',
		'pwd_is_null'=>'����Ϊ��',

		'reg_error' 		=>'ע��ʧ��',
		'reg_success' 		=>'ע��ɹ�',
	    'reset_pwd_success'=>'�������óɹ�',
		'reset_pwd_fail'=>'��������ʧ��',
		'phone_has_not_reg'=>'���ֻ��Ż�δע��',


	);
	if( !array_key_exists($info_type,$info_arr) ){

		$output_arr['code'] = 0;
		$output_arr['msg']  = 'δ֪�Ĵ�����ʾ';

	}
	else{

		$output_arr['code'] = $code;
		$output_arr['msg']  = $info_arr[$info_type];
	
	}
	if( $return_user_info&&!empty($user_id) ){

		$pai_user_obj   	= POCO::singleton('pai_user_class');
		$ret 				= $pai_user_obj->get_user_info_by_user_id($user_id);
        if(!$ret['model_style_v2'])
        {
            $ret['model_style_v2'] = array();
        }
		$output_arr['data'] = $ret;
	}

	if( !empty($more_info) && is_array($more_info) )
	{
		$output_arr = array_merge($more_info, $output_arr);
	}
	
	mobile_output($output_arr, false);
	exit();

}


switch ($type) {
    //������֤��
	case 'bind_sent_verify_code':
		//�󶨷�����֤��  �û�����û�󶨹��ֻ� ��ǰ��Ҫ�󶨵��ֻ�δ���˰󶨹�
		$phone 			 	= trim($_INPUT['phone']);
		$reset 			 	= trim($_INPUT['reset']);
		$pai_user_obj	 	= POCO::singleton('pai_user_class');	
		$check_phone_ret	= $pai_user_obj->check_phone_format($phone); 
		$yue_open_id = $_COOKIE['yueus_openid'];
		
				
		if(!$yue_open_id)
		{
			out_put( 'open_id_is_null' );
		}
		
		if( !$check_phone_ret )
			out_put( 'phone_format_error' );
		
		if($reset!=1)
		{
			$check_phone_ret = $pai_user_obj->check_cellphone_exist($phone);
			//���˾Ͳ����ٰ� 
			if( $check_phone_ret )
			{
				out_put( 'phone_is_bind',2 );
			}
		}
		
		$bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');
		$check_open_id_bind = $bind_weixin_obj->get_bind_info_by_open_id($yue_open_id);
		//���΢�ź��Ƿ��Ѱ�ԼԼ
		if($check_open_id_bind)
		{
			out_put( 'weixin_is_bind' );
		}
			
		//����У����
		$pai_sms_obj = POCO::singleton('pai_sms_class');
		$ret = $pai_sms_obj->send_phone_reg_verify_code($phone);
		if( !$ret )
		{
			out_put( 'verify_code_sent_error' );
		}else
		{	
			out_put( 'verify_code_sent_success',1 );
		}
	break;
	
	//��֤��֤��
	case 'verify_phone':
		
		$phone 		 		= trim($_INPUT['phone']);
		$verify_code 		= trim($_INPUT['verify_code']);
		$pai_user_obj 		= POCO::singleton('pai_user_class');
		$check_phone_ret	= $pai_user_obj->check_phone_format($phone); 
		$yue_open_id = $_COOKIE['yueus_openid'];
		
		if( !$check_phone_ret )
			out_put( 'phone_format_error' );
		
		if( strlen($verify_code)<1 )
		{
			out_put( 'verify_code_error' );
		}
		
		if(!$yue_open_id)
		{
			out_put( 'open_id_is_null' );
		}
		
		$bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');
		$check_open_id_bind = $bind_weixin_obj->get_bind_info_by_open_id($yue_open_id);
		//���΢�ź��Ƿ��Ѱ�ԼԼ
		if($check_open_id_bind)
		{
			out_put( 'weixin_is_bind' );
		}

		//���˾Ͳ����ٰ� 
		$check_phone_ret	= $pai_user_obj->check_cellphone_exist($phone);
		if( $check_phone_ret )
			out_put( 'phone_is_bind' );		
			
		//�����֤��
		$pai_sms_obj = POCO::singleton('pai_sms_class');
		$ret = $pai_sms_obj->check_phone_reg_verify_code($phone, $verify_code,0,false);
		if( !$ret )
		{
			out_put( 'verify_code_check_error' );
		}else
		{
			out_put( 'verify_code_check_success',1 );
		}
	break;
	
	//ע��
	case 'reg_act':
		//ִ�а��ֻ����� �û�����û�󶨹��ֻ� ��ǰ��Ҫ�󶨵��ֻ�δ���˰󶨹�
		$phone 		 		= trim((int)$_INPUT['phone']);
		$pwd 		 		= trim($_INPUT['pwd']);
		$verify_code 		= trim($_INPUT['verify_code']);
		$role = trim($_INPUT['role']);
		$yue_open_id = $_COOKIE['yueus_openid'];
		
		$pai_user_obj 	= POCO::singleton('pai_user_class');
		$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
		$bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');
		
		if(!$yue_open_id)
		{
			out_put( 'open_id_is_null' );
		}
			
		if(!$phone)
		{
			out_put( 'phone_is_null' );
		}
		
		if(!$pwd)
		{
			out_put( 'pwd_is_null' );
		}

		if(!in_array($role,array('model','cameraman')) || empty($role))
		{
			out_put( 'role_choose_error' );
		}

		
		$check_phone_ret	= $pai_user_obj->check_phone_format($phone); 

		if( !$check_phone_ret )
			out_put( 'phone_format_error' );
		
		if( strlen($verify_code)<1 )
		{
			out_put( 'verify_code_error' );
		}

		//���˾Ͳ����ٰ� 
		$check_phone_ret	= $pai_user_obj->check_cellphone_exist($phone);
		if( $check_phone_ret )
		{
			out_put( 'phone_is_bind' );
		}
		
		$check_open_id_bind = $bind_weixin_obj->get_bind_info_by_open_id($yue_open_id);
		//���΢�ź��Ƿ��Ѱ�ԼԼ
		if($check_open_id_bind)
		{
			out_put( 'weixin_is_bind' );
		}
			
		//�����֤��
		$pai_sms_obj = POCO::singleton('pai_sms_class');
		$ret = $pai_sms_obj->check_phone_reg_verify_code($phone, $verify_code);
		if( !$ret )
		{
			out_put( 'verify_code_check_error' );
		}
		
		$weixin_user_info = $weixin_pub_obj->get_weixin_user($yue_open_id);
		
		if(empty($weixin_user_info['nickname']))
		{
			$weixin_user_info['nickname'] = "΢���û�";
		}
		
		//ע���ʺ�
		$user_info_arr['nickname'] = $weixin_user_info['nickname'];
		$user_info_arr['cellphone'] = $phone;
		$user_info_arr['role'] = $role;
		$user_info_arr['pwd'] = $pwd;
		$user_info_arr['reg_from'] = "weixin";
		$user_id = $pai_user_obj->create_account($user_info_arr,$err_msg);
		
		if( $user_id<1 ){
			out_put( 'reg_error' );
		}
		else
		{
			
			if($role=='model')
			{
				$model_card_obj = POCO::singleton ( 'pai_model_card_class' );
				$insert_model ["user_id"] = $user_id;
				$model_card_obj->add_model_card ( $insert_model );
			}else
			{
				$cameraman_card_obj = POCO::singleton ( 'pai_cameraman_card_class' );
				$insert_cameraman ["user_id"] = $user_id;
				$cameraman_card_obj->add_cameraman_card ( $insert_cameraman );
			}
			
			
			//���û���¼
			$pai_user_obj->load_member($user_id);
			
			//��΢��
			$bind_data['user_id'] = $user_id;
			$bind_data['open_id'] = $yue_open_id;
			$ret = $bind_weixin_obj->add_bind($bind_data);
			if($ret)
			{			
				//ͬ��΢��ͷ��
				if($weixin_user_info['headimgurl'])
				{
					$bind_weixin_obj->upload_icon($user_id,$weixin_user_info['headimgurl']);
				}
				
				$template_code = 'G_PAI_WEIXIN_USER_BIND';
				$data = array('nickname'=>$weixin_user_info['nickname'], 'cellphone'=>$phone);
				$weixin_pub_obj->message_template_send_by_user_id($user_id, $template_code, $data);
			}
			
			$pai_user_obj->add_bind_phone_log( $user_id,$phone,'BIND_PHONE' );
			out_put( 'reg_success',1,true,$user_id );

		}
	break;
	
	case "reset_pwd":
		$phone 		 		= trim((int)$_INPUT['phone']);
		$pwd 		 		= trim($_INPUT['pwd']);
		$verify_code 		= trim($_INPUT['verify_code']);
		
		if(!$phone)
		{
			out_put( 'phone_is_null' );
		}
		
		if(!$pwd)
		{
			out_put( 'pwd_is_null' );
		}
		
		$pai_user_obj 		= POCO::singleton('pai_user_class');
		$check_phone_ret	= $pai_user_obj->check_phone_format($phone); 

		if( !$check_phone_ret )
		{
			out_put( 'phone_format_error' );
		}
		
		$check_phone_ret = $pai_user_obj->check_cellphone_exist($phone);
		
		if(!$check_phone_ret)
		{
			out_put( 'phone_has_not_reg' );
		}
		
		if( strlen($verify_code)<1 )
		{
			out_put( 'verify_code_error' );
		}
		
		//�����֤��
		$pai_sms_obj = POCO::singleton('pai_sms_class');
		$ret = $pai_sms_obj->check_phone_reg_verify_code($phone, $verify_code);
		if( !$ret )
		{
			out_put( 'verify_code_check_error' );
		}
		
		$ret = $pai_user_obj->update_pwd_by_phone($phone, $pwd);
		if($ret)
		{
			out_put( 'reset_pwd_success',1 );
		}else {
			out_put( 'reset_pwd_fail' );
		}
		
	break;
	
}


mobile_output($return_arr,false);


<?php
/**
 * 手机绑定、解绑
 * @author Henry
 * @copyright 2014-09-13
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
 /**
  * 检验手机号格式
  * 
  * @param int $phone 手机号
  * @return bool
  */
function check_phone_format($phone){

	if( !preg_match('/^1\d{10}$/isU',$phone) )
	{

		return false;
		
	}
	return true;

}
 /**
  * 检验用户是否已经绑定手机号
  * 
  * @param int $user_id 手机号
  * @return bool
  */
function check_is_bind($user_id){

	$pai_user_obj	= POCO::singleton('pai_user_class');
	$phone_tmp 		= $pai_user_obj->get_phone_by_user_id($user_id);
	if( strlen($phone_tmp)>0 )
	{
		return $phone_tmp;
		
	}
	return false;

}
 /**
  * 检验手机号是否已经被绑定了
  * 
  * @param int $phone 手机号
  * @return bool
  */
function check_phone_is_bind( $phone ){

	$pai_user_obj	= POCO::singleton('pai_user_class');
	//检查手机号是否已被绑定
	$user_id_tmp = $pai_user_obj->get_user_id_by_phone($phone);
	if( $user_id_tmp>0 )
	{
		return $user_id_tmp;
	}
	return false;

}
 /**
  * 页面输出
  * 
  * @param int $info_type 信息的key
  * @param int $code      
  * @return null
  */
function out_put( $info_type,$code=0,$return_user_info=false,$user_id='',$more_info=array() ){

	$info_arr = POCO_APP_PAI::ini('pai_tip');
	if( !array_key_exists($info_type,$info_arr) ){

		$output_arr['code'] = 0;
		$output_arr['msg']  = '未知的错误提示';

	}
	else{

		$output_arr['code'] = $code;
		$output_arr['msg']  = $info_arr[$info_type];
	
	}
	if( $return_user_info&&!empty($user_id) ){

		$pai_user_obj   	= POCO::singleton('pai_user_class');
		$ret 				= $pai_user_obj->get_user_info_by_user_id($user_id);
		$output_arr['data'] = $ret;
	}
	if( !empty($more_info) && is_array($more_info) )
	{
		$output_arr = array_merge($more_info, $output_arr);
	}
	
	mobile_output($output_arr, false);
	exit();

}
$type = trim($_INPUT['type']);
if( in_array($type,array('change_bind_sent_verify_code','change_bind_phone','change_password')) ){

	if( empty($yue_login_id) ){
		//改变绑定手机、更改用户密码  用户必须是已登陆
		exit('非法操作');

	}

}
switch ($type) {

	case 'change_bind_sent_verify_code':
		//改变绑定的手机号发送验证码  保证用户已绑定过手机  当前需要绑定的手机未被人绑定过
		$phone 			 	= trim($_INPUT['phone']);
		$pai_user_obj	 	= POCO::singleton('pai_user_class');	
		$check_phone_ret	= check_phone_format($phone); 
		if( !$check_phone_ret )
			out_put( 'phone_format_error' );

		$check_bind_ret		= check_is_bind($yue_login_id);
		if( !$check_bind_ret ){
			//用户没绑定过 则不能更改绑定
			out_put( 'user_no_bind' ); 
		
		}
		$rel_user_id 		= check_phone_is_bind( $phone );
		if( !empty($rel_user_id) ){

			if(  $rel_user_id == $yue_login_id  ){
				//当修改的号码没变的时候
				out_put( 'phone_cant_bind_again' );
			
			}
			else{

				out_put( 'phone_is_bind' );

			}
		}
		//发送校验码
		$pai_sms_obj = POCO::singleton('pai_sms_class');
		$ret = $pai_sms_obj->send_phone_bind_verify_code($phone,$yue_login_id);
		if( !$ret )
			out_put( 'verify_code_sent_error' );
		else
			out_put( 'verify_code_sent_success',1 );
	break;
	case 'change_bind_phone':
		//改变绑定的手机号  保证用户已绑定过手机  当前需要绑定的手机未被人绑定过
		$phone 		  = trim($_INPUT['phone']);
		$verify_code  = trim($_INPUT['verify_code']);
		$password  	  = trim($_INPUT['password']);
		$pai_user_obj = POCO::singleton('pai_user_class');	
		$ret = $pai_user_obj->check_pwd($yue_login_id,$password);
		if( !$ret ){

			out_put( 'password_check_faild' );

		}
		$pai_sms_obj  = POCO::singleton('pai_sms_class');
		$pai_user_obj = POCO::singleton('pai_user_class');
		$check_phone_ret	= check_phone_format($phone); 
		if( !$check_phone_ret )
			out_put( 'phone_format_error' );
		
		if( strlen($verify_code)<1 )
		{
			out_put( 'verify_code_error' );
		}
		//检查验证码
		$ret = $pai_sms_obj->check_phone_bind_verify_code($phone, $verify_code, $yue_login_id);
		if( !$ret )
		{
			out_put( 'verify_code_check_error' );
		}
		
		$cur_phone = check_is_bind($yue_login_id);
		if( empty($cur_phone) )
			out_put( 'user_no_bind' );
		$rel_user_id = check_phone_is_bind( $phone );
		if( !empty($rel_user_id) ){

			if(  $rel_user_id == $yue_login_id  ){

				out_put( 'phone_cant_bind_again' );
			
			}
			else{

				out_put( 'phone_is_bind' );

			}
		}

		$ret = $pai_user_obj->bind_phone($yue_login_id, $phone);
		if( !$ret ){

			out_put( 'change_bind_error' );

		}	
		else{

			$pai_sms_obj = POCO::singleton('pai_sms_class');
			$pai_sms_obj->send_notice_by_change_bind($cur_phone, array('phone'=>$phone), $yue_login_id);
			$pai_user_obj->add_bind_phone_log( $yue_login_id,$phone,'BIND_PHONE' );
			out_put( 'change_bind_success',1,true,$yue_login_id );

		}	
	break;	
	default:
		out_put( 'type_error' );
	break;
}
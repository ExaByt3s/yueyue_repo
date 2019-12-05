<?php

	/**
	* 支付宝绑定
	* @author hudw <[email]>
	*/
	include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');



	$pai_bind_account_obj = POCO::singleton('pai_bind_account_class');
	$type 				  = trim($_INPUT['type']);


	//检查模特机构
	$pai_model_relate_org_obj = POCO::singleton('pai_model_relate_org_class');
	$pai_model_relate_org_ret = $pai_model_relate_org_obj -> get_org_info_by_user_id($yue_login_id);

	if ($pai_model_relate_org_ret) 
	{
		$output_arr['limit'] = 1;
		$output_arr['data'] = $pai_model_relate_org_ret;
		mobile_output($output_arr,false);

		die();
	} 
	else 
	{
		switch ($type) {

			case 'bind_act':
				$bind_data['real_name']		= trim(iconv('UTF-8','GBK',$_INPUT['real_name']) );
				$bind_data['third_account'] = trim(iconv('UTF-8','GBK',$_INPUT['third_account']) );
				$bind_data['user_id']		= $yue_login_id;
				$bind_data['type']			= 'alipay_account';
				$bind_data['pic']           = trim($_INPUT['pic']);
				$ret = $pai_bind_account_obj->add_bind($bind_data);
				if( $ret > 0 ){

					$output_arr['code'] = 1;
					$output_arr['msg']  = '提交绑定成功,待工作人员审核';
					$output_arr['data'] = array();	

				}
				else{

					$output_arr['code'] = 0;
					$output_arr['msg']  = '绑定失败';
					$output_arr['data'] = array();	

				}
				mobile_output($output_arr,false);
			break;

		}
	}
	





	
	

?>
 
<?php

	/**
	* ֧������
	* @author hudw <[email]>
	*/
	include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
	$pai_bind_account_obj = POCO::singleton('pai_bind_account_class');
	$type 				  = trim($_INPUT['type']);
	switch ($type) {

		case 'check_bind':
			
			$user_id    = $yue_login_id;
			$type       = 'alipay_account';
			$ret 		= $pai_bind_account_obj->get_bind_status($user_id,$type);
			//-1 δ�� 0 ����� 1����� 2��˲�ͨ��
			switch ($ret['status']) {
				case '-1':
					$output_arr['code'] = 0;
					$output_arr['msg']  = 'δ��֧�����˺�';
					$output_arr['data'] = array();	
					break;
				case '0':
					$output_arr['code'] = 1;
					$output_arr['msg']  = '�����';
					$output_arr['data'] = array('third_account'=>$ret['third_account']);	
					break;
				case '1':
					$output_arr['code'] = 1;
					$output_arr['msg']  = '�����';
					$output_arr['data'] = array('third_account'=>$ret['third_account']);	
					break;
				case '2':
					$output_arr['code'] = 0;
					$output_arr['msg']  = '��˲�ͨ��';
					$output_arr['data'] = array();					
					break;														
				default:
					break;
			}
			mobile_output($output_arr,false);
		break;
		case 'bind_act':
			$bind_data['real_name']		= trim(iconv('UTF-8','GBK',$_INPUT['real_name']) );
			$bind_data['third_account'] = trim($_INPUT['third_account']);
			$bind_data['user_id']		= $yue_login_id;
			$bind_data['type']			= 'alipay_account';
			$ret = $pai_bind_account_obj->add_bind($bind_data);
			if( $ret > 0 ){

				$output_arr['code'] = 1;
				$output_arr['msg']  = '�ύ�󶨳ɹ�,��������Ա���';
				$output_arr['data'] = array();	

			}
			else{

				$output_arr['code'] = 0;
				$output_arr['msg']  = '��ʧ��';
				$output_arr['data'] = array();	

			}
			mobile_output($output_arr,false);
		break;

	}



	
	

?>
 
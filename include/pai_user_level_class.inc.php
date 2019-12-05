<?php
/*
 * �û��ȼ�������
 */

class pai_user_level_class extends POCO_TDG
{
	var $price = 300;
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		//$this->setServerId ( 101 );
		//$this->setDBName ( 'pai_db' );
		//$this->setTableName ( 'pai_city_tbl' );
	}
	
	/*
	 * ��ȡ�û��ȼ�
	 */
	function get_user_level($user_id)
	{
		$user_id = (int)$user_id;
		
		$pai_payment_obj = POCO::singleton ( 'pai_payment_class' );
		$id_obj = POCO::singleton ( 'pai_id_class' );
		$id_audit_obj = POCO::singleton ( 'pai_id_audit_class' );
		
		//���֤��Ϣ
		$id_info = $id_obj->get_id_info($user_id);
		
		//�����Ϣ
		$id_audit_info = $id_audit_obj->get_audit_info($user_id);
		
		
		//���ý�
		$available_balance = $pai_payment_obj->get_bail_available_balance($user_id);
		
		if($available_balance>=$this->price && $id_info)
		{
			$level = 3;
		}elseif($id_info)
		{
			$level = 2;
		}else 
		{
			$level = 1;
		}
		
		return $level;
	}
	
	
	/*
	 * �ȼ���֤�б�
	 * 
	 */
	function level_list($user_id)
	{
		$id_audit_obj = POCO::singleton ( 'pai_id_audit_class' );
		$pai_payment_obj = POCO::singleton ( 'pai_payment_class' );
		
		$level = $this->get_user_level($user_id);
		
		if($level==1)
		{
			$level_arr[0]['level'] = "V1";
			$level_arr[0]['name'] = "�ֻ���֤";
			$level_arr[0]['status'] = "����֤";
			
			$level_arr[1]['level'] = "V2";
			$level_arr[1]['name'] = "ʵ����֤";
			$level_arr[1]['status'] = "";
			
			$level_arr[2]['level'] = "V3";
			$level_arr[2]['name'] = "������֤";
			$level_arr[2]['status'] = "";
		}elseif($level==2)
		{
			$level_arr[0]['level'] = "V1";
			$level_arr[0]['name'] = "�ֻ���֤";
			$level_arr[0]['status'] = "����֤";
			
			$level_arr[1]['level'] = "V2";
			$level_arr[1]['name'] = "ʵ����֤";
			$level_arr[1]['status'] = "����֤";
			
			$level_arr[2]['level'] = "V3";
			$level_arr[2]['name'] = "������֤";
			$level_arr[2]['status'] = "";
		}elseif($level==3)
		{
			$level_arr[0]['level'] = "V1";
			$level_arr[0]['name'] = "�ֻ���֤";
			$level_arr[0]['status'] = "����֤";
			
			$level_arr[1]['level'] = "V2";
			$level_arr[1]['name'] = "ʵ����֤";
			$level_arr[1]['status'] = "����֤";
			
			$level_arr[2]['level'] = "V3";
			$level_arr[2]['name'] = "������֤";
			$level_arr[2]['status'] = "����֤";
		}
		
		//���֤��֤״̬
		$id_audit_info = $id_audit_obj->get_audit_info($user_id);
		if($id_audit_info['status']==1)
		{
			$level_arr[1]['audit_status'] = "1";
		}
		else
		{
			$level_arr[1]['audit_status'] = "0";
		}
		
		//���ý�
		$available_balance = $pai_payment_obj->get_bail_available_balance($user_id);
		
		if($available_balance>=$this->price)
		{
			$level_arr[2]['balance_status'] = "1";
		}
		else
		{
			$level_arr[2]['balance_status'] = "0";
		}
		
		return $level_arr;
	}
	
	
	public function level_detail($user_id)
	{
		$pai_payment_obj = POCO::singleton ( 'pai_payment_class' );
		$id_obj = POCO::singleton ( 'pai_id_class' );
		$id_audit_obj = POCO::singleton ( 'pai_id_audit_class' );

        $user_id = (int)$user_id;
		
		$id_info = $id_obj->get_id_info($user_id);
		$id_audit_info = $id_audit_obj->get_audit_list(false, "user_id={$user_id} and status=0");
		
		if($id_info)
		{
			$upload = 0;
			$text = "�����";
			$img = $id_info['img'];
			$is_check = 1;
			$id_code = $id_info['id_code'];
			$name = $id_info['name'];
		}elseif($id_audit_info)
		{
			$upload = 0;
			$text = "�ϴ��ɹ��������";
			$img = $id_audit_info[0]['img'];
			$is_check = 0;
		}else {
			$upload = 1;
			$text = "";
			$img = "";
			$is_check = 0;
		}
		
		//���ý�
		$available_balance = $pai_payment_obj->get_bail_available_balance($user_id);
		
		if($available_balance>=$this->price)
		{
			$balance_status = 1;
		}else
		{
			$balance_status = 0;
		}
		
		$ret['upload'] = $upload;
		$ret['text'] = $text;
		$ret['img'] = $img;
		$ret['is_check'] = $is_check;
		$ret['balance_status'] = $balance_status;
		$ret['id_code'] = $id_code;
		$ret['name'] = $name;
		
		return $ret;
	}
	
	
	public function send_level_approval_msg($user_id)
	{
		$pai_payment_obj = POCO::singleton ( 'pai_payment_class' );
		$id_obj = POCO::singleton ( 'pai_id_class' );
		$chat_user_obj = POCO::singleton('pai_chat_user_info');
		
		//���ý�
		$available_balance = $pai_payment_obj->get_bail_available_balance($user_id);
		
		//���֤��Ϣ
		$id_info = $id_obj->get_id_info($user_id);
		
		if($available_balance>=$this->price && $id_info )
		{
			$content = "����Ϣ����ġ�V3������֤�������ѱ�ͨ��������Է����V3ģ�ص���Լ�ˣ��������԰ɡ�";
		}elseif($id_info)
		{
			$content = "����Ϣ����ġ�V2ʵ����֤�������ѱ�ͨ��������Է����V2ģ�ص���Լ�ˣ��������԰ɡ�";
			
		}
		
		$chat_user_obj->redis_get_user_info($user_id);
		
		send_message_for_10005 ( $user_id, $content );
	}

	public function send_level_deny_msg($user_id)
	{
		
		$content = "�ף��ǳ���Ǹ����ġ�V2ʵ����֤������δ��ͨ�����뱾���ֳ����֤�������գ��������֤��Ƭ����ͨ������ͼƬ��׼����ͨ��ͼƬ�������������֤����ϸ���롢���֡�ͷ�����֤���벻ȫ�򿴲��������ͨ���������ɣ�����ʱ�������������֤������ͷһЩŶ������ͼƬ�������ύ������Ŷ�����ͼ��ͣ�";
		$url = "/mobile/app?from_app=1#mine/authentication/v2";
		send_message_for_10005 ( $user_id, $content,$url );
	}
}

?>
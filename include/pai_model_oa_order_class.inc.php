<?php
/*
 * OA����������
 */

class pai_model_oa_order_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_user_library_db' );
		$this->setTableName ( 'model_oa_order_tbl' );
	}
	
	
	/*
	 * ���LOG
	 * @param string  $type LOG��ʶ
	 * @param int  $order_id ������
	 * @param array  $log_array ��־��Ϣ
	 * 
	 */
	public function add_log($type,$order_id,$log_array)
	{
		global $yue_login_id;
		
		$log_data ['order_id'] = $order_id;
		$log_data ['user_id'] = $yue_login_id;
		$log_data ['type'] = $type;
		$log_data ['log'] = print_r($log_array,true);
		$log_data ['add_time'] = time ();
		
		$log_str = db_arr_to_update_str($log_data);
		$sql = "INSERT IGNORE pai_log_db.model_oa_log_tbl SET ".$log_str;
		$this->findBySql($sql);
		
	}
	
	/*
	 * ���
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_order($insert_data)
	{
		
		if (empty ( $insert_data ['cameraman_nickname'] ))
		{
			trace ( "��Ӱʦ�ǳƲ���Ϊ��", basename ( __FILE__ ) . " ��:" . __LINE__ . " ����:" . __METHOD__ );
			return false;
		}
		
		if (empty ( $insert_data ['cameraman_phone'] ))
		{
			trace ( "��ϵ�绰����Ϊ��", basename ( __FILE__ ) . " ��:" . __LINE__ . " ����:" . __METHOD__ );
			return false;
		}
		
		
		if (empty ( $insert_data ['source'] ))
		{
			trace ( "������Դ����Ϊ��", basename ( __FILE__ ) . " ��:" . __LINE__ . " ����:" . __METHOD__ );
			return false;
		}
		
		$insert_data ['add_time'] = time ();
		
		$num = str_replace(array("��","��","��"),"",$insert_data ['model_num']);
		$num = (int)$num;
		
		$insert_data ['receivable_amount'] = $insert_data ['hour']*$insert_data ['budget']*$num;
		$insert_data ['payable_amount'] = $insert_data ['hour']*$insert_data ['budget']*$num;
		
		$order_id = $this->insert ( $insert_data );
		
		$this->add_log("add_order",$order_id,$insert_data);
		
		return $order_id;
	
	}
	
	/**
	 * ����
	 *
	 * @param array $data
	 * @param int $id
	 * @return bool
	 */
	public function update_order($data, $order_id)
	{
		if (empty ( $data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
		}
		$order_id = ( int ) $order_id;
		if (empty ( $order_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
		}
		
		$where_str = "order_id = {$order_id}";
		
		if($data['cameraman_phone'])
		{
			//�༭����LOG
			$this->add_log("edit",$order_id,$data);
		}
		return $this->update ( $data, $where_str );
	}
	
	/*
	 * ��ȡ
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_order_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
	{
		
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		}
		else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}
	
	public function get_order_info($order_id)
	{
		$order_id = ( int ) $order_id;
		$ret = $this->find ( "order_id={$order_id}" );
		return $ret;
	}
	
	public function get_order_status($order_id)
	{
		$order_id = ( int ) $order_id;
		$ret = $this->find ( "order_id={$order_id}" );
		return $ret['order_status'];
	}
	
	public function get_wait_order_info_by_phone($phone)
	{
		$phone = ( int ) $phone;
		$ret = $this->find ( "order_status='wait' and cameraman_phone={$phone}" );
		
		//��������
		include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
		
		$ret ['city_name'] = get_poco_location_name_by_location_id ( $ret ['location_id'] );
		
		
		return $ret;
	}
	
	/*
	 * ����ڵȴ����״̬�Ƿ��ظ��ύ
	 */
	public function check_duplicate_submit($cellphone)
	{
		$cellphone = ( int ) $cellphone;
		
		if (empty ( $cellphone ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�ֻ�����Ϊ��' );
		}
		
		$where_str = "audit_status='wait' and cameraman_phone={$cellphone}";
		$ret = $this->findCount ( $where_str );
		if ($ret)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/*
	 * ȷ���µ�
	 */
	public function order_confirm_order($order_id)
	{
		$order_id = ( int ) $order_id;
		
		global $yue_login_id;
		
		if(!$yue_login_id)
		{
			return false;
		}
		
		$order_status = $this->get_order_status($order_id);
		//�ȴ��µ�״̬����ȷ���µ�
		if(!in_array($order_status,array("wait","complete_recommend")))
		{
			return -1;
		}
		
		$data ['order_status'] = 'confirm_order';
		$data ['audit_time'] = time();
		$ret = $this->update_order ( $data, $order_id );
		if ($ret == 1)
		{
			$this->audit_pass ( $order_id );
		}
		
		//��LOG
		$this->add_log("confirm_order",$order_id);
		
		return $ret;
	}
	
	/*
	 * ���ģ���Ƽ�
	 */
	public function order_complete_recommend($order_id)
	{
		$order_id = ( int ) $order_id;
		
		global $yue_login_id;
		
		if(!$yue_login_id)
		{
			return false;
		}
		
		$information_obj = POCO::singleton ('pai_information_push');
		
		$order_info = $this->get_order_info($order_id);
		//���µ�״̬�ſ���ִ���Ƽ�ģ�ز���
		if($order_info['order_status']!='confirm_order')
		{
			return -1;
			
		}
		
		$data ['order_status'] = 'complete_recommend';
		$ret = $this->update_order ( $data, $order_id );
		
/*		if($ret && $order_info['source']==4)
		{
			$pai_user_obj = POCO::singleton ( 'pai_user_class' );
			$user_id = $pai_user_obj->get_user_id_by_phone($order_info['cameraman_phone']);
			
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = '2';
			$send_data['card_text1'] = '����������󷢲���ԼԼΪ�㾫��ɸѡ�˼���ģ��';
			$send_data['card_title'] = '�鿴ģ������';
			$send_data['link_url']     = 'http://yp.yueus.com/mobile/app?from_app=1#camera_demand/model_push/' .$order_id ;
        	$send_data['wifi_url']     = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#camera_demand/model_push/' . $order_id;
			
			$information_obj->send_msg_for_system(10006,$user_id,$send_data);
		}*/
		
		//��LOG
		$this->add_log("complete_recommend",$order_id);
		
		
		return $ret;
	}
	
	/*
	 * ����ȷ��
	 */
	public function order_shoot_confirm($order_id)
	{
		$order_id = ( int ) $order_id;
		
		$order_status = $this->get_order_status($order_id);
		//������Ƽ����ܲ�������ȷ��
		if($order_status!='complete_recommend')
		{
			return -1;
		}
		
		$data ['order_status'] = 'shoot_confirm';
		$ret = $this->update_order ( $data, $order_id );
		
		//��LOG
		$this->add_log("shoot_confirm",$order_id);
		
		return $ret;
	}
	
	/*
	 * ����ȷ��
	 */
	public function order_pay_confirm($order_id)
	{
		$order_id = ( int ) $order_id;
		
		$order_status = $this->get_order_status($order_id);
		//����ȷ�ϲ����տ�
		if($order_status!='shoot_confirm')
		{
			return -1;
		}
		
		$data ['order_status'] = 'pay_confirm';
		$data ['payment_status'] = 'done';
		$ret = $this->update_order ( $data, $order_id );
		
		//��LOG
		$this->add_log("pay_confirm",$order_id);
		
		return $ret;
	}
	
	/*
	 * �ᵥ
	 */
	public function order_close($order_id)
	{
		$order_id = ( int ) $order_id;
		$data ['order_status'] = 'close';
		$ret = $this->update_order ( $data, $order_id );
		
		//��LOG
		$this->add_log("close",$order_id);
		
		return $ret;
	}
	
	/*
	 * �˿�
	 */
	public function order_refund($order_id)
	{
		$order_id = ( int ) $order_id;
		
		//�ᵥ�󲻿����ٸ��¶���״̬
		if($order_status=='close')
		{
			return -1;
		}
		
		$data ['order_status'] = 'refund';
		$ret = $this->update_order ( $data, $order_id );
		
		//��LOG
		$this->add_log("refund",$order_id);
		
		return $ret;
	}
	
	/*
	 * ����ȡ��
	 */
	public function order_cancel($order_id,$cancel_reason='')
	{
		$order_id = ( int ) $order_id;
		
		$order_info = $this->get_order_info($order_id);
		//�ᵥ�󲻿�����ȡ����
		if($order_info['order_status']=='close')
		{
			return -1;
		}
		
		$data ['order_status'] = 'cancel';
		$data ['cancel_reason'] = $cancel_reason;
		$ret = $this->update_order ( $data, $order_id );
		
/*		if($order_info['source']=='4')
		{
			$pai_user_obj = POCO::singleton ( 'pai_user_class' );
			$user_id = $pai_user_obj->get_user_id_by_phone($order_info['cameraman_phone']);
			$content = "�ܱ�Ǹ����������д����Ϣ�������⣬�������󷢲�δ��ͨ��ԼԼϵͳ����ˣ�ԭ��Ϊ��{$cancel_reason}�����������ʣ���ظ��������󡱺������ֻ����뵽�������֣�������Ա������ȡ����ϵ";
			send_message_for_10006($user_id, $content);
		}*/
		
		//��LOG
		$this->add_log("cancel",$order_id);
		
		return $ret;
	}
	
	/*
	 * �ȴ�����
	 */
	public function order_wait_shoot($order_id)
	{
		$order_id = ( int ) $order_id;
		
		$order_status = $this->get_order_status($order_id);
		//��ȷ���տ����ִ�еȴ�����
		if($order_status!='pay_confirm')
		{
			return -1;
		}
		
		
		$data ['order_status'] = 'wait_shoot';
		$ret = $this->update_order ( $data, $order_id );
		
		//��LOG
		$this->add_log("wait_shoot",$order_id);
		
		return $ret;
	}
	
	/*
	 * �ȴ��ᵥ
	 */
	public function order_wait_close($order_id)
	{
		$order_id = ( int ) $order_id;
		
		$order_status = $this->get_order_status($order_id);
		//״̬Ϊ�ȴ�������һ�����ܵȴ��ᵥ
		if($order_status!='wait_shoot')
		{
			return -1;
		}
		
		$data ['order_status'] = 'wait_close';
		$ret = $this->update_order ( $data, $order_id );
		
		//��LOG
		$this->add_log("wait_close",$order_id);
		
		return $ret;
	}
	
	/*
	 * ���ͨ��
	 */
	public function audit_pass($order_id)
	{
		$order_id = ( int ) $order_id;
		$data ['audit_status'] = 'pass';
		$ret = $this->update_order ( $data, $order_id );	
		return $ret;
	}
	
	/*
	 * ��˲�ͨ��
	 */
	public function audit_not_pass($order_id)
	{
		$order_id = ( int ) $order_id;
		$data ['audit_status'] = 'not_pass';
		$ret = $this->update_order ( $data, $order_id );
		return $ret;
	}
	
	/* 
	 * ��ȡ�ʾ�����
	 */
	public function get_requirement_list($b_select_count=false,$where='',$limit='')
	{
		$now = date("Y-m-d H:i:s");
		$where_str = "order_status in ('confirm_order','complete_recommend','close') and source=4 and date_time>'{$now}' ";
		
		if($where)
		{
			$where_str .= $where;
		}
		
		
		$order_by = "order_id desc";
		
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		}
		else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, '*' );
		}
		
		return $ret;
	}

}

?>
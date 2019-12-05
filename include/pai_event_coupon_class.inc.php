<?php
/*
 * ��Ż�ȯ������
 */

class pai_event_coupon_class extends POCO_TDG
{
	
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_event_share_coupon_tbl' );
	}
	

	/*
	 * ��ӷ����Ż�ȯ
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_share_coupon($insert_data)
	{
		
		if ( empty ( $insert_data ))
		{
			return false;
		}
		
		$insert_data['add_time'] = time();
		
		return $this->insert ( $insert_data ,'IGNORE');
	
	}
	
	/*
	 * �Ƿ��ѷ��Ż�ȯ
	 */
	public function check_share_coupon($cellphone)
	{
		if(!$cellphone)
		{
			$cellphone =0;
		}
		$ret = $this->find("cellphone={$cellphone}");
		if($ret)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	/*
	 * ��ӱ����Ż�ȯ
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_enroll_coupon($insert_data)
	{
		$this->setTableName ( 'pai_event_enroll_coupon_tbl' );
		
		if ( empty ( $insert_data ))
		{
			return false;
		}
		
		$insert_data['add_time'] = time();
		
		return $this->insert ( $insert_data);
	
	}
	
	
	/*
	 * ע���Ż�ȯ�Ƿ��ѷ�
	 */
	public function check_reg_coupon_send($user_id)
	{
		$this->setTableName ( 'pai_event_reg_coupon_tbl' );
		
		$ret = $this->find("user_id={$user_id}");
		
		if($ret)
		{
			return true;
		}
		else
		{	
			return false;
		}
	}
	
	
	/*
	 * ���ע���Ż�ȯ
	 */
	public function add_reg_coupon($insert_data)
	{
		$this->setTableName ( 'pai_event_reg_coupon_tbl' );
		
		if ( empty ( $insert_data ))
		{
			return false;
		}
		
		$insert_data['add_time'] = time();
		
		return $this->insert ( $insert_data,'IGNORE');
	}
 
	
	

}

?>
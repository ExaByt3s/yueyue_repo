<?php
/*
 * ������
 */

class pai_event_end_coupon_class extends POCO_TDG
{
	
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_event_end_coupon_tbl' );
	}
	
	/*
	 * ���
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_coupon_log($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			return false;
		}
		
		$insert_data['add_time'] = time();
		
		return $this->insert ( $insert_data );
	
	}
    
 

}

?>
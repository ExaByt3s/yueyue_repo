<?php
/*
 * ���ע��
 */

class pai_event_follow_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_event_follow_tbl' );
	}
	
	/*
     * ��ӻ��ע
     * 
     * @param int    $event_id    �ID
     * @param int    $user_id     �û�ID
     * 
     * return bool 
     */
	public function add_event_follow($event_id, $user_id)
	{
		$event_id = ( int ) $event_id;
		$user_id = ( int ) $user_id;
		
		if (empty ( $event_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�ID����Ϊ��' );
		}
		
		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�û�ID����Ϊ��' );
		}
		
		$insert_data ['event_id'] = $event_id;
		$insert_data ['follow_user_id'] = $user_id;
		$insert_data ['add_time'] = time ();
		
		return $this->insert ( $insert_data, "IGNORE" );
	}
	
	/*
     * ��ȡ��ע����
     * @param bool $b_select_count
     * @param string $where_str ��ѯ����
     * @param string $order_by ����
     * @param string $limit 
     * @param string $fields ��ѯ�ֶ�
     * 
     * return array
     */
	public function get_event_follow_list($b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $limit = '0,10', $fields = '*')
	{
		
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}
	
	/*
     * �Ƿ��ѹ�ע�û
     * 
     * @param int    $event_id    �ID
     * @param int    $user_id     �û�ID
     * 
     * return bool
     */
	public function check_event_follow($event_id, $user_id)
	{
		$event_id = ( int ) $event_id;
		$user_id = ( int ) $user_id;
		
		if (empty ( $event_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�ID����Ϊ��' );
		}
		
		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�û�ID����Ϊ��' );
		}
		
		$where_str = "event_id={$event_id} and follow_user_id={$user_id}";
		
		$ret = $this->get_event_follow_list ( true, $where_str );
		
		if ($ret)
		{
			return true;
		} else
		{
			return false;
		}
	
	}
	
	/*
     * ȡ����ע
     * 
     * @param int    $event_id    �ID
     * @param int    $user_id     �û�ID
     * 
     * return bool
     */
	public function cancel_follow($event_id, $user_id)
	{
		$event_id = ( int ) $event_id;
		$user_id = ( int ) $user_id;
		
		if (empty ( $event_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�ID����Ϊ��' );
		}
		
		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�û�ID����Ϊ��' );
		}
		
		$where_str = "event_id={$event_id} and follow_user_id={$user_id}";
		
		return $this->delete ( $where_str );
	
	}

}

?>
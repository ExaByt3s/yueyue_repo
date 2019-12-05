<?php
/*
 * ͼƬ��˷�����Ϣ��
 */

class pai_pic_send_message_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_log_db' );
		$this->setTableName ( 'pic_send_message_log' );
	}
	
	
	/*
	 * ͼƬ����log
	 * @param bool   $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_pic_send_message_info($user_id, $tpl_id, $add_time)
	{
		//echo $user_id."||".$tpl_id."||".$add_time;
		$user_id = (int)$user_id;
		if (empty ( $user_id ))
		{
			return false;
		}
		$tpl_id = (int)$tpl_id;
		if (empty($tpl_id)) 
		{
			return false;
		}
		if (empty($add_time)) 
		{
			return false;
		}
		$where_str = "add_time = '{$add_time}' AND user_id = {$user_id} AND tpl_id = {$tpl_id}";
		//var_dump($where_str);
		$id = $this->find($where_str, 'id');
		//var_dump($id);
		if ($id) 
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public function add_send_message($user_id, $tpl_id, $add_time)
	{
		$user_id = (int)$user_id;
		if (empty($user_id)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�û�ID����Ϊ��' );
		}
		$tpl_id  = (int)$tpl_id;
		if (empty($tpl_id)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ģ��ID����Ϊ��' );
		}
		if (empty($add_time)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':����ʱ�䲻��Ϊ��' );
		}
		$data['user_id'] = $user_id;
		$data['tpl_id']  = $tpl_id;
		$data['add_time'] = $add_time;
		return $this->insert($data);
	}
	
}

?>
<?php
/*
 * ר�ⱨ��������
 */

class pai_topic_enroll_class extends POCO_TDG
{
	
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_topic_enroll_tbl' );
	}
	
	/*
	 * ���
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_topic_enroll($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
		}
		
		return $this->insert ( $insert_data,'IGNORE' );
	
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
	public function get_topic_enroll_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
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
	
	public function get_topic_enroll_info($id)
	{
		$id = ( int ) $id;
		$ret = $this->find ( "id={$id}" );

		return $ret;
	}
	
	
	/*
	 * �Ƿ��ѱ���
	 */
	public function check_topic_enroll($topic_id,$user_id)
	{
		$topic_id = ( int ) $topic_id;
		$user_id = ( int ) $user_id;
		
		if(empty($topic_id) || empty($user_id))
		{
			return false;
		}
		
		$ret = $this->find ( "topic_id={$topic_id} and user_id={$user_id}" );
		
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
	 * ��ȡ��������
	 */
	public function get_topic_enroll_add_time($topic_id,$user_id)
	{
		$topic_id = ( int ) $topic_id;
		$user_id = ( int ) $user_id;
		
		if(empty($topic_id))
		{
			return false;
		}
		
		$ret = $this->find ( "topic_id={$topic_id} and user_id={$user_id}" );
		
		if($ret)
		{
			return $ret;
		}
		else
		{
			return false;
		}
	}

	/**
	 * ��������
	 * @param  [array] $data     [��������]
	 * @param  [string] $where_str [����]
	 * @return [int]            [true]
	 */
	public function update_info_where_str($data,$where_str)
	{
		if (empty($where_str)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':��������Ϊ��' );
		}
        $ret = $this->update($data, $where_str);
		return $ret;
	}

}

?>
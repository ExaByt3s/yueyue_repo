<?php
/*
 * �ļ��û�������
 */

class pai_alter_price_user_class extends POCO_TDG
{
	
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_alter_price_user_tbl' );
	}
	
	/*
	 * ����û�
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_user($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
		}
		
		return $this->insert ( $insert_data,'IGNORE' );
	
	}
	
	
	/**
	 * �����û�
	 *
	 * @param array $data
	 * @param int $id
	 * @return bool
	 */
	public function update_user($data, $id)
	{
		if (empty ( $data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
		}
		$id = ( int ) $id;
		if (empty ( $id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
		}
		
		$where_str = "id = {$id}";
		return $this->update ( $data, $where_str );
	}
	
	/*
	 * ɾ����¼
	 */
    public function del_user_by_user_id($alter_topic_id,$user_id)
    {
        $alter_topic_id = (int)$alter_topic_id;
 		$user_id = (int)$user_id;
 		
		if (empty($user_id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':�û�ID����Ϊ��');
		}
		
    	if (empty($alter_topic_id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ר��ID����Ϊ��');
		}
		
		$where_str = "user_id = {$user_id} and alter_topic_id={$alter_topic_id}";
		return $this->delete($where_str);
        
    }
    
	
	/*
	 * ��ȡ�û��б�
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_user_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,100000', $fields = '*')
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
	 * ���������û���ǩ
	 */
	public function update_user_topic_tag($topic_id,$tag='')
	{
		$topic_id = (int)$topic_id;
		$where_str = "alter_topic_id = {$topic_id}";
		
		$data['tag'] = $tag;
		return $this->update ( $data, $where_str );
	}
	
	

}

?>
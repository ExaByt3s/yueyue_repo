<?php
/*
 * ģ�������������
 */

class pai_model_style_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_model_style_tbl' );
	}
	
	/*
	 * ���ģ��������
	 * 
	 * @param int    $user_id �û�ID
	 * @param array  $type_arr ������
	 * @param array  $price_arr ����۸�
	 * 
	 * return bool 
	 */
	public function add_model_style($user_id, $type_arr, $price_arr)
	{
		$user_id = ( int ) $user_id;
		
		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�û�ID����Ϊ��' );
		}
		
		if (empty ( $type_arr ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���������鲻��Ϊ��' );
		}
		
		if (empty ( $price_arr ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':����۸����鲻��Ϊ��' );
		}
		
		//��ԭ�е�ɾ������������µķ��ͼ۸�
		$this->del_model_style ( $user_id );
		
		foreach ( $type_arr as $k => $style )
		{
			$insert_data ['user_id'] = $user_id;
			$insert_data ['style'] = $style;
			$insert_data ['price'] = $price_arr [$k];
			$this->insert ( $insert_data );
		}
	}
	
	/*
	 * ��ȡģ��������
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_model_style_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,15', $fields = '*')
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
	 * ɾ��ģ��������
	 * 
	 * @param int $user_id
	 */
	public function del_model_style($user_id)
	{
		$user_id = ( int ) $user_id;
		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�û�ID����Ϊ��' );
		}
		
		$where_str = "user_id = {$user_id}";
		return $this->delete ( $where_str );
	}
	
	/*
	 * �����û�ID��ȡģ��������
	 * 
	 * @param int $user_id
	 * 
	 * return array
	 */
	public function get_model_style_by_user_id($user_id, $fields = '*')
	{
		$user_id = ( int ) $user_id;
		$ret = $this->get_model_style_list ( false, "user_id={$user_id}", 'id DESC', '0,20', $fields );
		return $ret;
	}
}

?>
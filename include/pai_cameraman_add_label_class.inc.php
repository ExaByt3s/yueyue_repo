<?php

/**
 * ��Ӱʦ��ǩ��
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015��4��30��
 * @version 1
 */
class pai_cameraman_add_label_class extends POCO_TDG
{

	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_user_library_db' );
		$this->setTableName ( 'cameraman_label_list_tbl' );
	}
	
	
	/*
	 * ��ӻ�����Ϣ
	 *
	 * @param data   ����
	 *
	 * return id
	 */
	public function add_info($data)
	{
		if(!is_array($data) || empty($data))
		{
			throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ��');
		}
		return $this->insert($data,"IGNORE");
	}
	
	/*
	 * ��������
	 *
	 * $data [array]     ��Ҫ���µ�����
	 * $user_id  [int]       �û�ID
	 * return [boolean]  true|false
	 * **/
	public function update_info($data,$user_id)
	{
		$user_id = intval($user_id);
		if($user_id < 1)
		{
			throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��');
		}
		if(!is_array($data) || empty($data))
		{
			throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ��');
		}
		$where_str = "user_id = {$user_id}";
		return $this->update($data, $where_str);
	
	}
	
	
	/*
	 * ɾ������
	 * $id
	 * 
	 * **/
	public function del_info($id)
	{
		$id = intval($id);
		if($id <1)
		{
			throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��');
		}
		$where_str = "id = {$id}";
		return $this->delete($where_str);
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
	public function get_list($b_select_count = false,$cat_id = 0,$where_str = '', $order_by = 'id DESC', $limit = '0,20', $fields = '*')
	{
		$cat_id = intval($cat_id);
		if ($cat_id >0)
		{
			if(strlen($where_str) >0) $where_str .= ' AND ';
			$where_str .= "cat_id ={$cat_id}";
		}
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}
	
	
 
}
 
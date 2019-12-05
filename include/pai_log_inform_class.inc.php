<?php

/**
 *�ٱ���
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-04 11:09:14
 * @version 1
 */

class pai_log_inform_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_log_db' );
		$this->setTableName ( 'pai_examine_inform_tbl' );
	}

	/**
	 * ��ȡ�ٱ��б�
	 * @param  boolean $b_select_count [�Ƿ��ѯ����]
	 * @param  string  $where_str      [����]
	 * @param  string  $order_by       [����]
	 * @param  string  $limit          [ѭ������]
	 * @param  string  $fields         [��ѯ�ֶ�]
	 * @return [array] $ret            [����ֵ]
	 */
	public function get_inform_list($b_select_count = false, $where_str = '',$order_by = 'id DESC', $limit = '0,20', $fields = '*')
	{
		if ($b_select_count == true) 
		{
			$ret = $this->findCount($where_str);
		}
		else
		{
			$ret = $this->findAll($where_str, $limit, $order_by, $fields);
		}
		return $ret;
	}


	/**
	 * �������
	 * @param  [array] $data [��Ҫ���µ�����]
	 * @return [boolean]   [����ֵtrue|false]
	 */
	public function add_info($data)
	{
		if (empty($data)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':���鲻��Ϊ��');
		}
		$ret = $this->insert($data, 'IGNORE');
		return $ret;
	}


	/**
	 * ��������
	 * @param  [array] $data [��Ҫ���µ�����]
	 * @param  [int] $id   [����ID]
	 * @return [boolean]   [����ֵtrue|false]
	 */
	public function update_info($data, $id)
	{
		if (empty($data)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':���鲻��Ϊ��');
		}
		$id = (int)$id;
		if (empty($id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID����Ϊ��');
		}
		$where_str = "id = {$id}";
		$ret = $this->update($data, $where_str);
		return $ret;
	}
	/**
	 * ��ȡ�ٱ�����
	 * @param  [int] $id [����ID]
	 * @return [false|array]    [����ֵ]
	 */
	public function get_info($id)
	{
		$id = (int)$id;
		if(!$id)
		{
			return false;
		}
		return $this->find("id = {$id}");
	}

	/**
	 * ��ȡ�ٱ��˻��߾ٱ��ߴ���
	 * @param  [int]    $user_id [�û�ID]
	 * @param  [string] $type    [by��ʾ�ٱ���|to��ʾ���ٱ���]
	 * @return [int]             [����ֵ]
	 */
	public function get_inform_count($user_id, $type = 'by')
	{
		$user_id = (int)$user_id;
		if(!$user_id){return false;}
		//��Ϊ�ٱ���
		if($type == 'by')
		{
			$where_str = "by_informer = {$user_id}";
		}
		//���ٱ���
		else
		{
			$where_str = "to_informer = {$user_id}";
		}
		return $this->findCount($where_str);
	}
}

?>
<?php
/*
 * Ȩ�޲�����
 * xiao xiao
 * 2015-1-15 
 */

class pai_authority_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_user_library_db' );
		$this->setTableName ( 'authority_tbl' );
	}
	
	public function get_authority_list_user($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
	{
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str);
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}


	/**
	 * �ж��û��Ƿ��ǳ�������Ա
	 * return [boolean] true|false;
	 */
	public function user_id_is_root()
	{
		global $yue_login_id;
		$root_user_arr = array('100293','100008', '100002','100000','100003');
		if(in_array($yue_login_id, $root_user_arr))
		{
			$arr[0]['is_insert'] = 1;
			$arr[0]['is_delete'] = 1;
			$arr[0]['is_update'] = 1;
			$arr[0]['is_select'] = 1;
			return $arr;
		}
		else
		{
			return false;
		}
	}

	/*
     *�������ܲ�ѯһ������
     *return array $ret
	*/
	public function get_authority_info_by_where($where_str)
	{
		if (empty($where_str)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':��������Ϊ��' );
		}
		$ret = $this->find($where_str);
		return $ret;
	}

	/*
     * ����Ȩ����Ϣ
     *@param array $data
     *@param int $id
     *return boolean 
	*/
	public function update_authority_info_by_id($data, $id)
	{
		$id = (int)$id;
		if (empty($id)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
		}
		$where_str = "id = {$id}";
		$ret = $this->update($data, $where_str);
		return $ret;
	}

	/*
     * ��������
     *
	*/
	public function insert_authority_info_by_id($data)
	{
		if (empty($data)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':������ݲ���Ϊ��' );
		}
		$ret = $this->insert($data);
		return $ret;
	}

	/*
     * ɾ�����ݳɹ�
	*/
	public function delete_authority_info_by_id($id)
	{
		$id = (int)$id;
		if (empty($id)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
		}
		$where_str = "id = {$id}";
		$ret = $this->delete($where_str);
		return $ret;
	}
	/*
	 * ��ȡȨ���б�
	 * @param int $user_id �û�id
	 * return array
	 */
	public function get_authority_info_by_id($id)
	{
		$id = intval($id);
		if (empty ($id ))
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
		}
		$where_str = "id = {$id}";
		$ret = $this->find($where_str);
		return $ret;
	}
	
	
}

?>
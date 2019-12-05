<?php
/*
 * ģ�������
 * xiao xiao
 * 2015-1-16
 */

class pai_template_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_template_tbl' );
	}
	
	/*
     *��ȡģ���б�
     *@param boolean $b_select_count �Ƿ��ȡ����
     *@parma string $where_str ��ѯ����
     *@param sting $order_by ����
     *@param string $limit ��ѯ����
     *@param string $fields �ֶ���
	*/
	public function get_template_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
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


	/*
     * ����ģ����Ϣ
     *@param array $data
     *@param int $id
     *return boolean 
	*/
	public function update_template_info_by_id($data, $id)
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
	public function insert_template_by_data($data)
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
	public function delete_template_info_by_id($id)
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
	 * ��ȡģ�������Ϣ
	 * @param int $user_id �û�id
	 * return array
	 */
	public function get_template_info_by_id($id)
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
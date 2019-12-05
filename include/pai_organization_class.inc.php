<?php
/*
 * ������Ӳ�����
 */

class pai_organization_class extends POCO_TDG
{
	
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_user_library_db' );
		$this->setTableName ( 'organization_tbl' );
	}
	
	/*
	 * ���
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_org($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
		}
		
		return $this->insert ( $insert_data );
	
	}
    
    public function del_org($user_id)
    {
        $user_id = (int)$user_id;

		if (empty($user_id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':�û�ID����Ϊ��');
		}
		
		$where_str = "user_id = {$user_id}";
		return $this->delete($where_str);
        
    }
    
	
	/**
	 * ����
	 *
	 * @param array $data
	 * @param int $id
	 * @return bool
	 */
	public function update_org($data, $user_id)
	{
		if (empty($data)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':���鲻��Ϊ��');
		}
		$user_id = (int)$user_id;
		if (empty($user_id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':�û�ID����Ϊ��');
		}
		
		$where_str = "user_id = {$user_id}";
		return $this->update($data, $where_str);
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
	public function get_org_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
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
	 * ��ȡ
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_org_list_by_sql($b_select_count = false, $where_str = '', $order_by = 'o.id DESC', $limit = '0,10', $fields = '*')
	{
		$where_str = $where_str != '' ? "WHERE {$where_str}" : ''; 
		if ($b_select_count == true) 
		{
			$sql = "SELECT count(o.user_id) as c FROM `pai_user_library_db`.`organization_tbl` o, `pai_db`.`pai_user_tbl` u {$where_str}";
			$result = $this->query($sql);
			$ret = $result[0]['c'];
		}
		else
		{
			$sql = "SELECT o.*,u.location_id FROM `pai_user_library_db`.`organization_tbl` o, `pai_db`.`pai_user_tbl` u {$where_str} ORDER BY {$order_by} LIMIT {$limit}";
			$ret = $this->findBySql($sql);
		}
		return $ret;
	}

	/*
	* ͨ������user_id ��ȡ��������
    *@param string $user_id
	*/
	public function get_org_name_by_user_id($user_id)
	{
		$user_id = ( int ) $user_id;
		if (empty($user_id)) 
		{
			return false;
			//throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':USER_ID����Ϊ��');
		}
		$ret = $this->find ( "user_id={$user_id}", 'nick_name' );
		return $ret['nick_name'];
	}
	
	/*
     * ��ȡid
     *@param array $ret
	*/
	public function get_org_info($id)
	{
		$id = ( int ) $id;
		$ret = $this->find ( "id={$id}" );
		return $ret;
	}

	/*
     * ��ȡid
     *@param array $ret
	*/
	public function get_org_info_by_user_id($user_id)
	{
		$user_id = ( int ) $user_id;
		if (empty($user_id)) 
		{
			return false;
			//throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':USER_ID����Ϊ��');
		}
		$ret = $this->find ( "user_id={$user_id}" );
		return $ret;
	}

	/*
     *��ȡid
     *@param string $user_id
	*/
	public function get_org_id_by_user_id($user_id)
	{

		$user_id = ( int ) $user_id;
		if ($user_id <1)
		{
			return false;
		}
		$ret = $this->find ( "user_id={$user_id}", '','id' );
		return $ret['id'];
	}

    /**
     * ��ȡ��������
     * @param int $user_id
     * @param int $status
     * @param string $where_str
     * @return mixed
     */
    public function  get_org_id_by_user_id_v2($user_id,$status = -1,$where_str)
    {
        $user_id = intval($user_id);
        $status = intval($status);
        $where_str = trim($where_str);
        if($user_id <1) return false;
        if(strlen($where_str) >0) $where_str .= ' AND ';
        $where_str .= "user_id={$user_id}";
        if($status >=0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "status={$status}";
        }
        $ret = $this->find ( $where_str,'','id' );
        return $ret['id'];
    }


}

?>
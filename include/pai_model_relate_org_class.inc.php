<?php 

/*
 * ģ�ػ�������ģ��
 * xiaoxiao
 * 2015-1-23
*/
class pai_model_relate_org_class extends POCO_TDG
{
    /**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
        $this->setDBName ( 'pai_user_library_db' );
        $this->setTableName ( 'model_relation_org_tbl' );
	}

    /*
     *
     *ɾ������,ͨ��ģ��user_id
     *@param $user_id
    */
    public function delete_org_by_user_id($user_id)
    {
        $user_id = ( int ) $user_id;
        if (empty($user_id)) 
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':USER_ID����Ϊ��');
        }
        return $this->delete( "user_id={$user_id}");
    }

    /*
     * ���
     * 
     * @param array  $insert_data 
     * 
     * return bool 
     */
    public function add_model_org($insert_data)
    {
        
        if (empty ( $insert_data ))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
        }
        
        return $this->insert ( $insert_data );
    
    }
	/*
	 * ��ȡ�����б�
	 * @param bool $b_select_count
	 * @param string $user_id ģ��id
	 * @param string $where_str $where_str
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_org_list_by_user_id($b_select_count = false,$user_id ,$where_str='', $limit = '0,10', $order_by = 'priority DESC',  $fields = '*')
	{
		$user_id = ( int ) $user_id;
        if (empty($user_id)) 
        {
            return false;
        }
        if(strlen($where_str) >0) $where_str .= ' AND ';
        $where_str = "user_id = {$user_id}";
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
     * ��ȡ����ģ���Ƿ���ڸ�ģ��
     *@param int $user_id ģ��ID
     *@param $login_id ��¼id ���ǻ�����¼ID
     *return boolean true||false
    */
    public function get_org_model_audit_by_user_id($user_id = 0, $login_id = 0)
    {
        /*if ($login_id == 100293) 
        {
            var_dump($user_id);
            var_dump($login_id);
            //exit;
        }*/
        $user_id = ( int ) $user_id;
        if (empty($user_id)) 
        {
            return false;
        }
        $yue_login_id = ( int ) $login_id;
        //var_dump($yue_login_id);
        if (empty($yue_login_id)) 
        {
            return false;
        }
        if ($yue_login_id == $user_id) 
        {
             return true;
        }
        $where_str = "user_id = {$user_id} AND org_id = {$yue_login_id}";
        /*if ($login_id == 100293) 
        {
            var_dump($where_str);
            exit;
        } */
        $ret = $this->find($where_str);
        if (!empty($ret) && is_array($ret)) 
        {
           return true;
        }
        return false;
    }

    /*
     * �̼��б� �б�
     * @param bool $b_select_count
     * @param string $where_str where����
     * @param string $order_by ����
     * @param string $limit 
     * @param string $fields ��ѯ�ֶ�
     * 
     * return array
     */
    public function get_model_org_list_by_org_id($b_select_count = false,$where_str ='' , $limit = '0,10', $order_by = 'id DESC',  $fields = '*')
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
     * ģ�� �б�
     * @param bool $b_select_count
     * @param string $where_str ģ��id
     * @param string $order_by ����
     * @param string $limit 
     * @param string $fields ��ѯ�ֶ�
     * 
     * return array
     */
    public function get_model_org_list_v2_by_org_id($b_select_count = false,$where_str ='' , $limit = '0,10', $order_by = 're.id DESC',  $fields = '*')
    {
        if ($b_select_count == true)
        {
            $sql = "SELECT count(re.user_id) AS c FROM `pai_user_library_db`.`model_relation_org_tbl` re,`pai_db`.`pai_model_audit_tbl` au WHERE {$where_str}";
            //$ret = $this->findCount ( $where_str );
            $result = $this->query($sql);
            $ret = $result[0]['c'];
            unset($result);
        } 
        else
        {
            $sql = "SELECT re.user_id FROM `pai_user_library_db`.`model_relation_org_tbl` re,`pai_db`.`pai_model_audit_tbl` au WHERE {$where_str} ORDER BY {$order_by}";
            $ret = $this->findBySql ($sql);
        }
        return $ret;
    }

    /*
     *��ȡһ������������
     *@param int $user_id
    */
    public function get_org_info_by_user_id($user_id, $order_by = 'priority DESC')
    {
        $user_id = ( int ) $user_id;
        if(empty($user_id))
        {
        	return false;
        }
        $ret = $this->find("user_id={$user_id}", $order_by);
        return $ret;
    }

    /*
     * �ж��Ƿ�Ϊ����ģ��
     * @param int $user_id
     * return boolean true|false
    */
    public function get_is_org_by_user_id($user_id)
    {
        $user_id = ( int ) $user_id;
        if (empty($user_id)) 
        {
            return false;
        }
        $ret = $this->find("user_id={$user_id}", 'id');
        if ($ret) 
        {
            return true;
        }
        else
        {
            return false;
        }
        //return $ret;
    }

     /*
     * ��ȡģ�ظ���
     * @param bool $b_select_count
     * @param string $org_id ����id
     * @param string $status [�ϼ�|�¼�]
     * return int $total_count
     */
     public function get_model_total_count($b_select_count = false, $org_id, $is_approval = -1, $limit = '0,10', $order_by = 'ro.id DESC')
     {
       if (empty($org_id)) 
       {
            return false;
           //throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':����ID����Ϊ��' );
       }
       $org_id = (int)$org_id;
       $where_str = " AND ro.org_id = {$org_id}";
       if ($is_approval != -1) 
       {
          if ($is_approval == 1) 
          {
             $where_str .= " AND au.is_approval = 1";
          }
          else
          {
            $where_str .= " AND au.is_approval <> 1";
          }
       }
        if ($b_select_count == true) 
        {
           $sql = "SELECT count(ro.user_id) as c FROM  `pai_user_library_db`.`model_relation_org_tbl` ro, `pai_db`.`pai_model_audit_tbl` au WHERE ro.user_id = au.user_id {$where_str}";
           $result = $this->query($sql);
           $ret = $result[0]['c'];
           unset($result);
        }
        else
        {
            $sql = "SELECT ro.* FROM  `pai_user_library_db`.`model_relation_org_tbl` ro, `pai_db`.`pai_model_audit_tbl` au WHERE ro.user_id = au.user_id {$where_str} ORDER BY {$order_by} limit {$limit}";
            $ret = $this->findBySql($sql);
        }
        //var_dump($ret);
        return $ret;
     }


    
	
}



 ?>
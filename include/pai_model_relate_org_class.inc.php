<?php 

/*
 * 模特机构关联模型
 * xiaoxiao
 * 2015-1-23
*/
class pai_model_relate_org_class extends POCO_TDG
{
    /**
	 * 构造函数
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
     *删除机构,通过模特user_id
     *@param $user_id
    */
    public function delete_org_by_user_id($user_id)
    {
        $user_id = ( int ) $user_id;
        if (empty($user_id)) 
        {
            throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':USER_ID不能为空');
        }
        return $this->delete( "user_id={$user_id}");
    }

    /*
     * 添加
     * 
     * @param array  $insert_data 
     * 
     * return bool 
     */
    public function add_model_org($insert_data)
    {
        
        if (empty ( $insert_data ))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
        }
        
        return $this->insert ( $insert_data );
    
    }
	/*
	 * 获取机构列表
	 * @param bool $b_select_count
	 * @param string $user_id 模特id
	 * @param string $where_str $where_str
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
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
     * 获取机构模特是否存在该模特
     *@param int $user_id 模特ID
     *@param $login_id 登录id 即是机构登录ID
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
     * 商家列表 列表
     * @param bool $b_select_count
     * @param string $where_str where条件
     * @param string $order_by 排序
     * @param string $limit 
     * @param string $fields 查询字段
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
     * 模特 列表
     * @param bool $b_select_count
     * @param string $where_str 模特id
     * @param string $order_by 排序
     * @param string $limit 
     * @param string $fields 查询字段
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
     *获取一条机构的数据
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
     * 判断是否为机构模特
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
     * 获取模特个数
     * @param bool $b_select_count
     * @param string $org_id 机构id
     * @param string $status [上架|下架]
     * return int $total_count
     */
     public function get_model_total_count($b_select_count = false, $org_id, $is_approval = -1, $limit = '0,10', $order_by = 'ro.id DESC')
     {
       if (empty($org_id)) 
       {
            return false;
           //throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':机构ID不能为空' );
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
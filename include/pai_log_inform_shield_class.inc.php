<?php

/**
 *屏蔽用户类类
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-04 11:09:14
 * @version 1
 */

class pai_log_inform_shield_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_log_db' );
		$this->setTableName ( 'pai_examine_inform_shield_tbl' );
	}
    /**
     * 插入数据
     * @param array $insert_data 需要插入的数据
     * @return int     返回值true|false
     * @throws App_Exception
     */
    public function insert_data($insert_data)
	{
		if (empty($insert_data) || !is_array($insert_data))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':添加数据不能为空' );
		}
		return $this->insert($insert_data,'IGNORE');
	}

	/**
	 * 获取被屏蔽用户列表
	 * @param  boolean $b_select_count [是否查询个数]
	 * @param  string  $where_str      [条件]
	 * @param  string  $order_by       [排序]
	 * @param  string  $limit          [循环个数]
	 * @param  string  $fields         [查询字段]
	 * @return [array] $ret            [返回值]
	 */
	public function get_shield_list($b_select_count = false, $where_str = '',$order_by = 'id DESC', $limit = '0,20', $fields = '*')
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
	 * 获取举报数据
	 * @param  [int] $id [主机ID]
	 * @return [false|array]    [返回值]
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
     * 屏蔽用户
     * @param $user_id  $user_id [用户名ID]
     * @param string $type 类型
     * @param string $role yueseller yuebuyer both
     * @return bool
     */
    public function shield_user($user_id, $type = 'add',$role='both')
	{
		$user_id = (int)$user_id;
        $type = trim($type);
        $role = trim($role);
		if (!$user_id){return false;}
		/*$gmclient= new GearmanClient();
        #113.107.204.236
		$gmclient->addServers("172.18.5.211:9830");
		$gmclient->setTimeout(5000); // 设置超时
		do
		{
			$req_param['uid'] = $user_id;
            $req_param['role'] = $role;
			//echo json_encode($req_param);
			if($type == 'add')
			{
				$result = $gmclient->doBackground("add_blacklist",json_encode($req_param) );
			}
			else
			{
			  $result = $gmclient->doBackground("del_blacklist",json_encode($req_param) );
			}
            //print_r($result);
		}
		while(false);*/
		//while($gmclient->returnCode() != GEARMAN_SUCCESS);
		//echo $gmclient->returnCode();
		//exit;

		$gmclient = POCO::singleton('pai_gearman_base_class');
		$gmclient->connect('172.18.5.211', '9830');

		$req_param['uid'] = $user_id;
		$req_param['role'] = $role;

		if($type == 'add')
		{
			$result = $gmclient->_doBackground("add_blacklist",$req_param);
		}
		else
		{
			$result = $gmclient->_doBackground("del_blacklist",$req_param);
		}

		return true;
	}

	/**
	 * 删除屏蔽
	 * @param  [int] $id [主键ID]
	 * @return [boolean]     [true|false]
	 */
	public function delete_info($id)
	{
		$id = (int)$id;
		if (!$id) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
		}
		$where_str = "id = {$id}";
		return $this->delete($where_str);
	}

	/**
	 * 通过举报ID获取数据
	 * @param  [int] $inform_id [举报ID]
	 * @return [boolean]       [false|true]
	 */
	public function get_info_by_inform_id($inform_id)
	{
		$inform_id = (int)$inform_id;
		if(!$inform_id){return false;}
		$where_str = "inform_id = {$inform_id}";
		$ret = $this->find($where_str);
		if (is_array($ret) && !empty($ret)) 
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * 通过被举报人ID获取数据
	 * @param  [int] $inform_id [举报人ID]
	 * @return [boolean]       [false|true]
	 */
	public function get_info_by_user_id($user_id)
	{
		$user_id = (int)$user_id;
		if($user_id <1){return false;}
		$where_str = "user_id = {$user_id}";
		$ret = $this->find($where_str);
		if (is_array($ret) && !empty($ret)) 
		{
			return true;
		}
		else
		{
			return false;
		}
	}

}

?>
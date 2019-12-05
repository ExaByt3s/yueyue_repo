<?php
/**
 * 代运营操作类
 *
 */

class pai_mall_operate_agent_class extends POCO_TDG
{	
	/**
	 * 最后一次错误提示
	 * @var string
	 */
	protected $_last_err_msg = null;
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
        $this->setServerId('101');
        $this->setDBName('mall_db');
	}


    private function set_operate_agent_admin_tbl()
    {
        $this->setTableName('mall_operate_agent_admin_tbl');
    }

    private function set_operate_agent_seller_tbl()
    {
        $this->setTableName('mall_operate_agent_seller_tbl');
    }

    /**
	 * 添加管理员
	 *
	 * @param array $data
	 * @return bool
	 */
	public function add_admin($data)
	{
        $data['admin_user_id'] = (int)$data['admin_user_id'];

		if (empty($data) || empty($data['admin_user_id']))
		{
			return false;
		}

        $check_admin = $this->check_admin_exist($data['admin_user_id']);

        if($check_admin)
        {
            $result['result'] = -1;
            $result['message'] = "约约ID已存在";
            return $result;
        }

        $check_seller = $this->get_seller_list(true, "seller_user_id=".$data['admin_user_id']);
        if($check_seller)
        {
            $result['result'] = -1;
            $result['message'] = "该约约ID已在商家列表存在，请在商家列表删除该约约ID";
            return $result;
        }

        $this->set_operate_agent_admin_tbl();
		$this->insert($data);

        $result['result'] = 1;
        $result['message'] = "添加成功";
        return $result;
	}
	
	/**
	 * 更新管理员
	 *
	 * @param array $data
	 * @param int $id
	 * @return bool
	 */
	public function update_admin($data, $id)
	{
        $id = (int)$id;

		if (empty($data) || empty($id))
		{
            return false;
		}


        $this->set_operate_agent_admin_tbl();

		$where_str = "id = {$id}";
		return $this->update($data, $where_str);
	}


	
	/**
	 *  删除管理员
	 *
	 * @param int $id
	 * @return bool
	 */
	public function del_admin($id)
	{
        $id = (int)$id;
		if (empty($id))
		{
            return false;
		}

        $admin_info = $this->get_admin_info($id);


        $count = $this->get_seller_list(true, 'admin_user_id='.$admin_info['admin_user_id']);
        if($count)
        {
            $result['result'] = -1;
            $result['message'] = '该管理员还有关联的商家，请把商家删除或转移到其他管理员';
            return $result;
        }

        $this->set_operate_agent_admin_tbl();

		$where_str = "id = {$id}";
		$this->delete($where_str);

        $result['result'] = 1;
        $result['message'] = '删除成功';
        return $result;
	}

	
	/**
	 * 取管理员列表
	 *
	 * @param bool $b_select_count 是否返回总数：TRUE返回总数 FALSE返回列表
     * @param string $where_str    查询条件
	 * @param string $limit        查询条数
	 * @param string $order_by     排序条件
	 * @return array|int
	 */
	public function get_admin_list($b_select_count = false, $where_str = '', $limit = '0,10', $order_by = 'id DESC')
	{


        $this->set_operate_agent_admin_tbl();

		if ($b_select_count == true)
        {
            $rows = $this->findCount($where_str);
        }
		else
        {
            $rows = $this->findAll($where_str, $limit, $order_by);
        }
		return $rows;
	}


    public function get_admin_info($id)
    {
        $id = (int)$id;
        $this->set_operate_agent_admin_tbl();
        return $this->find("id={$id}");
    }

    public function check_admin_exist($user_id)
    {
        $user_id = (int)$user_id;
        $this->set_operate_agent_admin_tbl();

        $ret = $this->find("admin_user_id={$user_id}");
        if($ret)
        {
            return true;
        }
        else
        {
            return false;
        }
    }


    /**
     * 添加商家
     *
     * @param array $data
     * @return bool
     */
    public function add_seller($data)
    {
        $data['admin_user_id'] = (int)$data['admin_user_id'];
        $data['seller_user_id'] = (int)$data['seller_user_id'];

        if (empty($data['admin_user_id']) || empty($data['seller_user_id']))
        {
            $result['result'] = -1;
            $result['message'] = '参数错误';
            return $result;
        }

/*        $seller_obj = POCO::singleton ( 'pai_mall_seller_class' );

        $seller_info = $seller_obj->get_seller_info($data['seller_user_id'],2);

        if(!$seller_info)
        {
            $result['result'] = -1;
            $result['message'] = $data['seller_user_id'].'未开通商家';
            return $result;
        }*/

        if($data['admin_user_id']==$data['seller_user_id'])
        {
            $result['result'] = -1;
            $result['message'] = '商家ID不能和管理员ID一样哦';
            return $result;
        }

        $check = $this->get_seller_list(true, "seller_user_id=".$data['seller_user_id']);
        if($check)
        {
            $result['result'] = -1;
            $result['message'] = '该商家已有运营人管理了';
            return $result;
        }

        $this->set_operate_agent_seller_tbl();
        $this->insert($data);

        $this->send_gearman($data['admin_user_id'],$data['seller_user_id']);

        $result['result'] = 1;
        $result['message'] = '添加成功';
        return $result;
    }

    /**
     * 更新商家
     *
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function update_seller($data, $id)
    {
        $id = (int)$id;

        if (empty($data['admin_user_id']) || empty($id))
        {
            $result['result'] = -1;
            $result['message'] = '参数错误';
            return $result;
        }

        if($data['admin_user_id']==$data['seller_user_id'])
        {
            $result['result'] = -1;
            $result['message'] = '商家ID不能和管理员ID一样哦';
            return $result;
        }

        $this->set_operate_agent_seller_tbl();

        $where_str = "id = {$id}";
        $this->update($data, $where_str);

        $this->send_gearman($data['admin_user_id'],$data['seller_user_id']);

        $result['result'] = 1;
        $result['message'] = '更新成功';
        return $result;
    }



    /**
     *  删除商家
     *
     * @param int $id
     * @return bool
     */
    public function del_seller($id)
    {
        $id = (int)$id;
        if (empty($id))
        {
            return false;
        }

        $this->set_operate_agent_seller_tbl();

        $seller_info = $this->get_seller_info($id);

        $this->send_gearman("-1",$seller_info['seller_user_id']);

        $where_str = "id = {$id}";
        return $this->delete($where_str);
    }


    /**
     * 取商家列表
     *
     * @param bool $b_select_count 是否返回总数：TRUE返回总数 FALSE返回列表
     * @param string $where_str    查询条件
     * @param string $limit        查询条数
     * @param string $order_by     排序条件
     * @return array|int
     */
    public function get_seller_list($b_select_count = false, $where_str = '', $limit = '0,10', $order_by = 'id DESC')
    {

        $this->set_operate_agent_seller_tbl();

        if ($b_select_count == true)
        {
            $rows = $this->findCount($where_str);
        }
        else
        {
            $rows = $this->findAll($where_str, $limit, $order_by);
        }
        return $rows;
    }


    /*
     * 获取一个管理员下面有效的商家列表
     *
     * @param string $admin_user_id    管理员ID
     * @param string $b_select_count   是否返回总数：TRUE返回总数 FALSE返回列表
     * @param string $limit     分页
     * @return array|int
     */
    public function get_valid_seller_list($admin_user_id,$b_select_count = false,$limit = '0,10')
    {
        $admin_user_id = (int)$admin_user_id;

        $where_str = "admin_user_id={$admin_user_id}";

        $this->set_operate_agent_seller_tbl();

        if ($b_select_count == true)
        {
            $rows = $this->findCount($where_str);
        }
        else
        {
            $rows = $this->findAll($where_str, $limit, "id DESC");
        }

        return $rows;
    }


    public function get_seller_info($id)
    {
        $id = (int)$id;
        $this->set_operate_agent_seller_tbl();
        return $this->find("id={$id}");
    }



    public function send_gearman($agent_id,$seller_user_id)
    {
        $agent_id = (int)$agent_id;
        $seller_user_id = (int)$seller_user_id;

        /*$gmclient= new GearmanClient();
        $gmclient->addServers("172.18.5.211:9830");
        $gmclient->setTimeout(5000); // 设置超时
        do
        {
            $json_arr['pocoid']= "{$seller_user_id}";
            $json_arr['app_role']= 'yueseller';
            $json_arr['agent_id']= "{$agent_id}";

            $result= $gmclient->do("save_base_info",json_encode($json_arr) );
        }
        while(false);
        //while($gmclient->returnCode() != GEARMAN_SUCCESS);
        $ret = json_decode($result,true);*/


        $gmclient = POCO::singleton('pai_gearman_base_class');
        $gmclient->connect('172.18.5.211', '9830');

        $json_arr['pocoid']= "{$seller_user_id}";
        $json_arr['app_role']= 'yueseller';
        $json_arr['agent_id']= "{$agent_id}";

        $result= $gmclient->_do("save_base_info",$json_arr,'json');
        $ret = json_decode($result,true);

        return $ret;
    }


    /*
     * 代运营登录验证
     * @param int $user_id
     * @param string $pwd
     * @return array
     */
    public function user_login($user_id,$pwd)
    {
        $user_obj = POCO::singleton('pai_user_class');

        $user_id = (int)$user_id;
        $pwd_hash = md5($pwd);

        $where = "user_id={$user_id} and pwd_hash='$pwd_hash'";
        $check_user=$user_obj->get_user_list(true,$where);

        if($check_user)
        {

            $check_admin = $this->check_permit($user_id);

            if($check_admin)
            {
                $user_obj->load_member($user_id);

                $result['result']= 1;
                $result['message']= "登录成功";
                return $result;
            }
            else
            {
                $result['result']= -1;
                $result['message']= "你没有开通代运营权限";
                return $result;
            }
        }
        else
        {
            $result['result']= -1;
            $result['message']= "密码错误";
            return $result;
        }
    }


    /*
     * 代运营权限判断
     * @param int $user_id
     * @return bool
     */
    public function check_permit($user_id)
    {
        $user_id = (int)$user_id;

        if(empty($user_id))
        {
            return false;
        }

        $admin_where = "admin_user_id={$user_id}";
        $check_admin = $this->get_admin_list(true, $admin_where);

        if($check_admin)
        {
            return true;
        }
        else
        {
            return false;
        }
    }


    public function user_goods_list($user_id)
    {
        $goods_obj = POCO::singleton ( 'pai_mall_goods_class' );
        $ret = $goods_obj->user_goods_list($user_id,array("show"=>1), false,  'goods_id DESC', '0,200');

        foreach($ret as $k=>$val)
        {
            $goods_id = $val['goods_id'];
            $ret[$k]['link_url'] = "yueyue://goto?goods_id={$goods_id}&pid=1220102&type=inner_app";
            $ret[$k]['wifi_url'] = "yueyue://goto?goods_id={$goods_id}&pid=1220102&type=inner_app";
        }

        return $ret;
    }

	

}

?>
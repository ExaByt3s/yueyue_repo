<?php
/**
 * ����Ӫ������
 *
 */

class pai_mall_operate_agent_class extends POCO_TDG
{	
	/**
	 * ���һ�δ�����ʾ
	 * @var string
	 */
	protected $_last_err_msg = null;
	
	/**
	 * ���캯��
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
	 * ��ӹ���Ա
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
            $result['message'] = "ԼԼID�Ѵ���";
            return $result;
        }

        $check_seller = $this->get_seller_list(true, "seller_user_id=".$data['admin_user_id']);
        if($check_seller)
        {
            $result['result'] = -1;
            $result['message'] = "��ԼԼID�����̼��б���ڣ������̼��б�ɾ����ԼԼID";
            return $result;
        }

        $this->set_operate_agent_admin_tbl();
		$this->insert($data);

        $result['result'] = 1;
        $result['message'] = "��ӳɹ�";
        return $result;
	}
	
	/**
	 * ���¹���Ա
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
	 *  ɾ������Ա
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
            $result['message'] = '�ù���Ա���й������̼ң�����̼�ɾ����ת�Ƶ���������Ա';
            return $result;
        }

        $this->set_operate_agent_admin_tbl();

		$where_str = "id = {$id}";
		$this->delete($where_str);

        $result['result'] = 1;
        $result['message'] = 'ɾ���ɹ�';
        return $result;
	}

	
	/**
	 * ȡ����Ա�б�
	 *
	 * @param bool $b_select_count �Ƿ񷵻�������TRUE�������� FALSE�����б�
     * @param string $where_str    ��ѯ����
	 * @param string $limit        ��ѯ����
	 * @param string $order_by     ��������
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
     * ����̼�
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
            $result['message'] = '��������';
            return $result;
        }

/*        $seller_obj = POCO::singleton ( 'pai_mall_seller_class' );

        $seller_info = $seller_obj->get_seller_info($data['seller_user_id'],2);

        if(!$seller_info)
        {
            $result['result'] = -1;
            $result['message'] = $data['seller_user_id'].'δ��ͨ�̼�';
            return $result;
        }*/

        if($data['admin_user_id']==$data['seller_user_id'])
        {
            $result['result'] = -1;
            $result['message'] = '�̼�ID���ܺ͹���ԱIDһ��Ŷ';
            return $result;
        }

        $check = $this->get_seller_list(true, "seller_user_id=".$data['seller_user_id']);
        if($check)
        {
            $result['result'] = -1;
            $result['message'] = '���̼�������Ӫ�˹�����';
            return $result;
        }

        $this->set_operate_agent_seller_tbl();
        $this->insert($data);

        $this->send_gearman($data['admin_user_id'],$data['seller_user_id']);

        $result['result'] = 1;
        $result['message'] = '��ӳɹ�';
        return $result;
    }

    /**
     * �����̼�
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
            $result['message'] = '��������';
            return $result;
        }

        if($data['admin_user_id']==$data['seller_user_id'])
        {
            $result['result'] = -1;
            $result['message'] = '�̼�ID���ܺ͹���ԱIDһ��Ŷ';
            return $result;
        }

        $this->set_operate_agent_seller_tbl();

        $where_str = "id = {$id}";
        $this->update($data, $where_str);

        $this->send_gearman($data['admin_user_id'],$data['seller_user_id']);

        $result['result'] = 1;
        $result['message'] = '���³ɹ�';
        return $result;
    }



    /**
     *  ɾ���̼�
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
     * ȡ�̼��б�
     *
     * @param bool $b_select_count �Ƿ񷵻�������TRUE�������� FALSE�����б�
     * @param string $where_str    ��ѯ����
     * @param string $limit        ��ѯ����
     * @param string $order_by     ��������
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
     * ��ȡһ������Ա������Ч���̼��б�
     *
     * @param string $admin_user_id    ����ԱID
     * @param string $b_select_count   �Ƿ񷵻�������TRUE�������� FALSE�����б�
     * @param string $limit     ��ҳ
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
        $gmclient->setTimeout(5000); // ���ó�ʱ
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
     * ����Ӫ��¼��֤
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
                $result['message']= "��¼�ɹ�";
                return $result;
            }
            else
            {
                $result['result']= -1;
                $result['message']= "��û�п�ͨ����ӪȨ��";
                return $result;
            }
        }
        else
        {
            $result['result']= -1;
            $result['message']= "�������";
            return $result;
        }
    }


    /*
     * ����ӪȨ���ж�
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
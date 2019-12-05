<?php

/**
 *�����û�����
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-04 11:09:14
 * @version 1
 */

class pai_log_inform_shield_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_log_db' );
		$this->setTableName ( 'pai_examine_inform_shield_tbl' );
	}
    /**
     * ��������
     * @param array $insert_data ��Ҫ���������
     * @return int     ����ֵtrue|false
     * @throws App_Exception
     */
    public function insert_data($insert_data)
	{
		if (empty($insert_data) || !is_array($insert_data))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':������ݲ���Ϊ��' );
		}
		return $this->insert($insert_data,'IGNORE');
	}

	/**
	 * ��ȡ�������û��б�
	 * @param  boolean $b_select_count [�Ƿ��ѯ����]
	 * @param  string  $where_str      [����]
	 * @param  string  $order_by       [����]
	 * @param  string  $limit          [ѭ������]
	 * @param  string  $fields         [��ѯ�ֶ�]
	 * @return [array] $ret            [����ֵ]
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
     * �����û�
     * @param $user_id  $user_id [�û���ID]
     * @param string $type ����
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
		$gmclient->setTimeout(5000); // ���ó�ʱ
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
	 * ɾ������
	 * @param  [int] $id [����ID]
	 * @return [boolean]     [true|false]
	 */
	public function delete_info($id)
	{
		$id = (int)$id;
		if (!$id) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
		}
		$where_str = "id = {$id}";
		return $this->delete($where_str);
	}

	/**
	 * ͨ���ٱ�ID��ȡ����
	 * @param  [int] $inform_id [�ٱ�ID]
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
	 * ͨ�����ٱ���ID��ȡ����
	 * @param  [int] $inform_id [�ٱ���ID]
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
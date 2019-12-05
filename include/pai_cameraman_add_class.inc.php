<?php
/*
 * ��Ӱʦ¼��ģ����
 */

class pai_cameraman_add_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_user_library_db' );
		$this->setTableName ( 'cameraman_info_tbl' );
	}

	/*
	 * �����Ӱʦ������Ϣ
	 * 
	 * @param data   ����
	 * 
	 * return id 
	 */
	public function insert_cameraman_info($b_select_count = false ,$uid, $data)
	{
		$this->setTableName ( 'cameraman_info_tbl' );
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		if (empty($data) || !is_array($data)) 
		{
				throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ��' );
		}
		//$data['uid'] = $uid;
		$where_str = "uid = {$uid}";
		if ($this->find($where_str)) 
		{
			unset($data['inputer_time']);
			$this->update($data, $where_str);
		}
		else
		{
			$data['uid'] = $uid;
			$this->insert($data);
			$tmp_data['inputer_time'] = $data['inputer_time'];
		}
		if ($b_select_count == true) 
		{
		  $tmp_data['location_id'] = $data['location_id'];
		  //$tmp_data['inputer_name'] = $data['inputer_name']; 
		  $this->update_search_data($uid, $tmp_data);
		}
	}
	/*
     * ��������
     * $uid [int] [�û�ID]
     * $data [array] [��������]
	*/
	public function update_search_data($uid, $data)
	{
		$uid = (int)$uid;
		//die($uid);
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		if (empty($data) || !is_array($data)) 
		{
				throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ��' );
		}
		$this->setTableName ( 'cameraman_search_tbl' );
		$where_str = "uid = {$uid}";
		//die($where_str);
		if ($this->find($where_str)) 
		{
			$this->update($data, $where_str);
		}
		else
		{
			$data['uid'] = $uid;
			$this->insert($data);
		}

	}

	/*
     * ��Ӹ�����Ϣ
     * @param int uid
     * @param array $data
     * return void;
	*/
	public function insert_cameraman_personal($uid, $data)
	{
		//print_r($data);exit;
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		if(empty($data) || !is_array($data))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ��' );
		}
		$this->setTableName ( 'cameraman_personal_tbl' );
		$where_str = "uid = {$uid}";
		if ($this->find($where_str)) 
		{
			# code...
			$this->update($data, $where_str);
		}
		else
		{
			$data['uid'] = $uid;
		    $this->insert($data);
		}
		$tmp_data['sex']          = $data['sex'];
		$tmp_data['is_studio']    = $data['is_studio'];
		$tmp_data['birthday']     = $data['birthday'];
		$tmp_data['p_state']      = $data['p_state'];
		$tmp_data['join_age']     = $data['join_age'];
		//$tmp_data['month_take']   = $data['month_take'];
		$tmp_data['is_fview']     = $data['is_fview'];
		//$tmp_data['attend_total'] = $data['attend_total'];
		$this->update_search_data($uid, $tmp_data);
	}
	/*
     * �����������
     * @param int uid
     * @param $data array
	 * return void
     *
	*/
	public function insert_cameraman_other($uid, $data)
	{
		$this->setTableName ( 'cameraman_other_tbl' );
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		if(empty($data) || !is_array($data))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ��' );
		}
		$where_str = "uid = {$uid}";
		if ($this->find($where_str)) 
		{
			# code...
			$this->update($data, $where_str);
		}
		else
		{
			$data['uid'] = $uid;
		    $this->insert($data);
		}
		/*$tmp_data['label'] = $data['label'];
		$this->update_search_data($uid, $tmp_data);*/
	}

	/*
     * �������
     *@pram int uid
     *@param array $data
     * return void
	*/
	public function insert_cameraman_honor($uid, $data)
	{
		if(empty($data) || !is_array($data))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ��' );
		}
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$this->setTableName ( 'cameraman_honor_pic_tbl' );
		$data['add_time'] = date('Y-m-d H:i:s', time());
		$data['uid']      = $uid;
		$this->insert($data);
		$id = $this->get_last_insert_id();
		$data['id'] = $id;
		return $data;
	}
	/*
	 *�����Ʒ
	 *@param $data array
	 *@param int uid
     * return void
	*/

	public function insert_cameraman_pic($uid ,$data)
	{
		//print_r($data);
		if(empty($data) || !is_array($data))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ��' );
		}
		$uid = (int)$uid;
		if(empty($uid))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$this->setTableName ( 'cameraman_pic_tbl' );
		$data['add_time'] = date('Y-m-d H:i:s', time());
		$data['uid']      = $uid;
		$this->insert($data);
		$id = $this->get_last_insert_id();
		$data['id'] = $id;
		return $data;
	}

	/*
	 *��Ӹ�����Ϣ 
	 *@param $data array
     * return void
	*/
	public function insert_cameraman_follow($data)
	{
		//print_r($data);
		if(empty($data) || !is_array($data))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ��' );
		}
		$this->setTableName ( 'cameraman_follow_infornation_tbl' );
		$data['add_time'] = date('Y-m-d H:i:s', time());
		//print_r($data);exit;
		$id = $this->insert($data);
		if (empty($id) || isset($id)) 
		{
			$id = $this->get_last_insert_id();
		}
		$where_str = "id = {$id}";
		//die($where_str);
		$ret = $this->find($where_str);
		return $ret;
	}

	/*
	 * �����õ�����
     * @param string $where_str
     * return array arr
	*/
	public function search_model_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
	{
		$this->setTableName( 'cameraman_search_tbl' );
		if ($b_select_count == true) 
		{
			$ret = $this->findCount ( $where_str );
		}
		else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}

	/*
     * ��Ӱʦ�б�
     * @param string $where_str
     * return array $ret
	*/
	public function get_cameraman_list($b_select_count = false, $where_str = '', $order_by = 'uid DESC', $limit = '0,10', $fields = '*')
	{
		$this->setTableName( 'cameraman_info_tbl' );
		if ($b_select_count == true) 
		{
			$ret = $this->findCount ( $where_str );
		}
		else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}

	/*
	* @param int $uid
	* return array $ret 
	*
	*/
	public function get_cameraman_info($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//hrow new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$this->setTableName ( 'cameraman_info_tbl' );
		$where_str = "uid = {$uid}";
		$ret = $this->find($where_str);
		return $ret;
	}

	/*
     *��ȡ������Ϣ
     *@param int $uid
     *return array $ret
     *
	*/
	public function get_personal_info($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$this->setTableName('cameraman_personal_tbl');
		$where_str = "uid = {$uid}";
		$ret = $this->find($where_str);
		return $ret;
	}

	/*
     * ��ȡ������Ϣ
     * @param int $uid
     * return array $ret
     * 
	*/
	public function get_cameraman_other($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$this->setTableName( 'cameraman_other_tbl' );
		$where_str = "uid = {$uid}";
		$ret = $this->find($where_str);
		return $ret;
	}

	/*
     * ��ȡ������Ϣ
     * @param int $uid
     * return array $ret
     * 
	*/
	public function  get_cameraman_follow($b_select_count = false,$uid = 0, $limit = '0,3', $sort = 'uid DESC', $fields = '*')
	{ 
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$this->setTableName( 'cameraman_follow_infornation_tbl' );
		$where_str = "uid = {$uid}";
		if ($b_select_count == true) 
		{
			$ret = $this->findCount ( $where_str );
		}
		else
		{
			$ret = $this->findAll($where_str, $limit, $sort, $fields);
		}
		return $ret;
	}

	/*
     * ��ȡ�����Ϣ
     * @param int $uid
     * return array $ret
     * 
	*/
	public function get_cameraman_pic($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$this->setTableName( 'cameraman_pic_tbl' );
		$where_str = "uid = {$uid}";
		$ret = $this->find($where_str);
		return $ret;
	}

	/*
	*����ʱ������ȡ����ID
    * @param string $where_str
    * @param string DISTINCT(uid)
    * return array id
	*/

	public function get_follow_cameraman_uid($where_str, $limit = '',$sort = 'uid DESC', $fields = '*')
	{
		if (empty($where_str)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':��������Ϊ��' );
		}
		$this->setTableName( 'cameraman_follow_infornation_tbl' );
		$ret = $this->findAll($where_str, $limit, $sort, $fields );
		return $ret;
	}


	/* 
     * �����������������ѯuid
     * @param string $where_str
     * @param string DISTINCT(uid)
     * return array id
	*/
	public function get_cameraman_search_uid($where_str, $limit = '',$sort = 'uid DESC', $fields = '*')
	{
		/*if (empty($where_str)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':��������Ϊ��' );
		}*/
		$this->setTableName( 'cameraman_search_tbl' );
		$ret = $this->findAll($where_str, $limit, $sort, $fields );
		return $ret;
	}


	/*
     *��Ӱʦ��Ʒ
     *@param  uid
     *
	*/
	public function get_cameraman_pic_list($b_select_count = false,$where_str = '', $sort = 'uid DESC', $limit = '0,10', $fields = '*')
	{
		$this->setTableName( 'cameraman_pic_tbl' );
		if ($b_select_count == ture) 
		{
			$ret = $this->findCount($where_str);
		}
		else{
			$ret = $this->findAll($where_str, $limit, $sort, $fields);
		}
		
		return $ret;
	}

	/*
     *��Ӱʦ����
     *@param  uid
     *
	*/
	public function get_cameraman_honor_list($b_select_count = false,$where_str = '', $sort = 'uid DESC', $limit = '0,10', $fields = '*')
	{
		$this->setTableName( 'cameraman_honor_pic_tbl' );
		if ($b_select_count == ture) 
		{
			$ret = $this->findCount($where_str);
		}
		else{
			$ret = $this->findAll($where_str, $limit, $sort, $fields);
		}
		
		return $ret;
	}

	/*
     * ɾ��������
     *@param int uid
     * return boolean
	 */
	public function cameraman_del_style($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$this->setTableName( 'cameraman_style_tbl' );
		$where_str = "uid = {$uid}";
		return $this->delete($where_str);
	}

	/*
     *��������� 
     * @param int uid
     * @param array $data
     *
	*/ 
	public function cameraman_add_style($uid, $data)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		if (empty($data) || !is_array($data)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':��������Ϊ��' );
		}
		$this->setTableName( 'cameraman_style_tbl' );
		$data['uid'] = $uid;
		return $this->insert($data);
	}

	/*
	 *��ȡ������
	 *@param int uid
	 *return array data
     *
	*/
	public function cameraman_list_style($uid, $sort = 'uid ASC', $limit = '0,10', $fields = '*')
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$this->setTableName( 'cameraman_style_tbl' );
		$where_str = "uid = {$uid}";
		return $this->findAll($where_str, $limit, $sort, $fields);
	}


	/*
	 * ������ȡ������
	 * $where_str
     *
	*/
	public function cameraman_search_style($where_str = '', $sort = 'uid DESC', $limit = '', $fields = 'uid')
	{
		$this->setTableName( 'cameraman_style_tbl' );
		if (empty($where_str)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':��ѯ��������Ϊ��' );
		}
		$ret = $this->findAll($where_str, $limit, $sort, $fields);
		return $ret;
	}


	/*
	 *ɾ����Ӱʦ��Ʒ
	 *@param int id
     * return void
	*/
	public function cameraman_del_pic($id)
	{
		$id = (int)$id;
		if (empty($id)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
		}
		$this->setTableName( 'cameraman_pic_tbl' );
		$where_str = "id = {$id}";
		return $this->delete($where_str);

	}
    
    /*
	 *ɾ������
	 *@param int id
     * return void
	*/
	public function cameraman_del_honor($id)
	{
		$id = (int)$id;
		if (empty($id)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
		}
		$this->setTableName( 'cameraman_honor_pic_tbl' );
		$where_str = "id = {$id}";
		return $this->delete($where_str);

	}

}

?>
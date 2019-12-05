<?php
/*
 * ģ��¼��ģ����
 */

class pai_model_add_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_user_library_db' );
		$this->setTableName ( 'model_info_tbl' );
	}

	public function get_model_id_by_phone($phone)
	{
		if ( !preg_match ( '/^1\d{10}$/isU',$phone ) )
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':��������' );
		}
		$this->setTableName ( 'model_info_tbl' );
		$row = $this->find ( "phone={$phone}" );
		return $row['uid'];
	}

	/**
	 * ��ȡ¼����inputer_id
	 * @param  [type] $b_select_count [description]
	 * @param  [type] $where_str      [����]
	 * @param  string $limit          [ѭ������]
	 * @param  string $sort           [����]
	 * @param  string $fields         [�ֶ�]
	 * @return [type]                 [��������]
	 */
	public function get_inputer_list($b_select_count = false, $where_str = '', $limit = '', $sort = 'uid DESC', $fields = '*' )
	{
		$this->setTableName ( 'model_info_tbl' );
		if ($b_select_count == true) 
		{
			return $this->findCount ( $where_str );
		}
		$ret = $this->findAll ($where_str, $limit, $order_by, $fields );
		return $ret;
	}

	/**
	 * ��ȡ¼����
	 * @param  [type] $b_select_count [description]
	 * @param  [type] $where_str      [����]
	 * @param  string $limit          [ѭ������]
	 * @param  string $sort           [����]
	 * @param  string $fields         [description]
	 * @return [type]                 [�ֶ�]
	 */
	public function get_inputer_list_v2($b_select_count = false, $where_str = '', $limit = '', $sort = 'uid DESC', $fields = '*' )
	{
		$this->setTableName ( 'model_info_tbl' );
		$user_obj  = POCO::singleton('pai_user_class');
		if ($b_select_count == true) 
		{
			return $this->findCount ( $where_str );
		}
		$ret = $this->findAll ($where_str, $limit, $order_by, $fields );
		if(!is_array($ret)) $ret = array();
		$where_tmp_str = '';
		$list  = array();
		foreach ($ret as $key => $val) 
		{
			if($key != 0) $where_tmp_str .= ',';
			$where_tmp_str .= $val['inputer_id'];
			
		}
		if(strlen($where_tmp_str) > 0)
		{
			$where_in_str .= "user_id IN ({$where_tmp_str})";
			$list =  $user_obj->get_user_list(false, $where_in_str, $order_by = 'user_id DESC', '0,99999999', 'nickname as inputer_name,user_id as inputer_id');
		}
		return $list;
	}


	/*
	 * ���ģ�ػ�����Ϣ
	 * 
	 * @param data   ����
	 * 
	 * return id 
	 */
	public function insert_model_info($b_select_count = false ,$uid, $data)
	{
		$this->setTableName ( 'model_info_tbl' );
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
		//die('nooo');
		if ($b_select_count == true) 
		{
		  //$tmp_data['province'] = $data['province'];
		  $tmp_data['location_id']  = $data['location_id'];
		  $tmp_data['inputer_name'] = $data['inputer_name']; 
		  $tmp_data['inputer_id']   = $data['inputer_id']; 
		  $this->update_search_data($uid, $tmp_data);
		}
	}

	/*
     * ����model ����
     *
	*/
	public function update_model($data, $uid)
	{
		$uid = (int)$uid;
		//die($uid);
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ�ղ���Ϊ��' );
		}
		if (empty($data) || !is_array($data)) 
		{
				throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ�ղ���Ϊ��' );
		}
		$this->setTableName ( 'model_info_tbl' );
		$where_str = "uid = {$uid}";
		//print_r($data);exit;
		$ret = $this->update($data,$where_str);
		return $ret;
	}

	/*
     * ��������
	*/
	public function update_search_data($uid, $data)
	{
		$uid = (int)$uid;
		//die($uid);
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ�ղ���Ϊ��' );
		}
		if (empty($data) || !is_array($data)) 
		{
				throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ�ղ���Ϊ��' );
		}
		$this->setTableName ( 'model_search_tbl' );
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
	 * ���ְҵ��Ϣ
     * @param int uid
     * @param $data array
	 * return void
	*/	
	public function insert_model_profession($uid, $data)
	{
		$this->setTableName ( 'model_profession_info_tbl' );
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ�ղ���Ϊ��' );
		}
		
		if(empty($data) || !is_array($data))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ�ղ���Ϊ��' );
		}
		$where_str = "uid = {$uid}";
		if ($this->find($where_str)) 
		{
			$this->update($data, $where_str);
		}
		else
		{
			$data['uid'] = $uid;
			$this->insert($data);
		}
		$tmp_data['state'] = $data['p_state'];
		$tmp_data['p_join_time'] = $data['p_join_time'];
		$tmp_data['p_enter_school_time'] = $data['p_enter_school_time'];
		$tmp_data['p_school'] = $data['p_school'];
		$tmp_data['p_specialty'] = $data['p_specialty'];
		$this->update_search_data($uid, $tmp_data);
	}

	/*
     * ��ӷ���
     * @param int uid
     * @param $data array
	 * return void
     *
	*/
	public function insert_model_score($uid, $data)
	{
		$this->setTableName ( 'model_score_tbl' );
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ�ղ���Ϊ��' );
		}
		if(empty($data) || !is_array($data))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ�ղ���Ϊ��' );
		}
		$where_str = "uid = {$uid}";
		if ($this->find($where_str)) 
		{
			$this->update($data, $where_str);
		}
		else
		{
			$data['uid'] = $uid;
		    $this->insert($data);
		}
		$tmp_data['total_score'] = $data['join_score']+$data['honor_score']+$data['appearance_score']+$data['makeup_score']+$data['expressiveness_score']+$data['height_score']+$data['weight_score']+$data['bwh_score']+$data['cup_score'];
		$this->update_search_data($uid, $tmp_data);
	}

	/*
     * �����������
     * @param int uid
     * @param $data array
	 * return void
     *
	*/
	public function insert_model_other($uid, $data)
	{
		$this->setTableName ( 'model_other_tbl' );
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ�ղ���Ϊ��' );
		}
		
		if(empty($data) || !is_array($data))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ�ղ���Ϊ��' );
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
		$tmp_data['organization'] = isset($data['organization']) ? $data['organization'] : '';
		$tmp_data['information_sources'] = isset($data['information_sources']) ?$data['information_sources'] : '';
		$this->update_search_data($uid, $tmp_data);
	}

	/*
     * ��������Ϣ
     * @param int uid
     * @param $data array
	 * return void
     *
	*/
	public function insert_model_stature($uid, $data)
	{
		$this->setTableName ( 'model_stature_tbl' );
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ�ղ���Ϊ��' );
		}
		if(empty($data) || !is_array($data))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ�ղ���Ϊ��' );
		}
		$where_str = "uid = {$uid}";
		if ($this->find($where_str)) 
		{
			$this->update($data, $where_str);
		}
		else
		{
			$data['uid'] = $uid;
			$this->insert($data);
		}
		$tmp_data['sex'] = $data['sex'];
		$tmp_data['age'] = $data['age'];
		$tmp_data['height'] = $data['height'];
		$tmp_data['weight'] = $data['weight'];
		$tmp_data['cup_id'] = $data['cup_id'];
		$tmp_data['cup_a']  = $data['cup_a'];
		//$tmp_data['bwh'] = $data['bwh'];
		$tmp_data['shoe_size'] = $data['shoe_size'];
		$this->update_search_data($uid, $tmp_data);
	}


	/*
	 * ���ͼƬ
	 *@param $data array
	 *@param int uid
     * return void
	*/

	public function insert_model_pic($data, $uid)
	{
		//print_r($data);
		if(empty($data) || !is_array($data))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ�ղ���Ϊ��' );
		}
		$uid = (int)$uid;
		if(empty($uid))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$this->setTableName ( 'model_pic_tbl' );
		$data['add_time'] = date('Y-m-d H:i:s', time());
		$data['uid']      = $uid;
		//print_r($data);exit;
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

	public function insert_model_follow($data)
	{
		//print_r($data);
		if(empty($data) || !is_array($data))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ�ղ���Ϊ��' );
		}
		$this->setTableName ( 'model_follow_infornatio_tbl' );
		$data['add_time'] = date('Y-m-d H:i:s', time());
		//print_r($data);exit;
		$id = $this->insert($data);
		//$id = $this->insert($data);
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
		$this->setTableName( 'model_search_tbl' );
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
	 * ¼�����û���
	 * @param int $user_id
	 * return array 
	 */
	public function get_user_inputer_name_by_user_id($uid)
	{
		$this->setTableName( 'model_info_tbl' );
		$uid = (int)$uid;
		if (empty($uid))
		{
			return false;
		}
		$row = $this->find ( "uid={$uid}",'','inputer_id' );
		if(is_array($row) && !empty($row))
		{
			return get_user_nickname_by_user_id($row['inputer_id']);
		}
		return false;
	}

	/*
     * ģ���б�
     * @param string $where_str
     * return array $ret
	*/
	public function get_model_list($b_select_count = false, $where_str = '', $order_by = 'uid DESC', $limit = '0,10', $fields = '*')
	{
		$this->setTableName( 'model_info_tbl' );
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
     * ��ȡ����������
     * 
	*/
	/*public function model_import($where_str ,$order_by = 'id DESC', $limit = '0,10', $fields = '*')
	{
		$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
	}*/


	/*
	* @param int $uid
	* return array $ret 
	*
	*/
	public function get_model_info($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ�ղ���Ϊ��' );
		}
		$this->setTableName ( 'model_info_tbl' );
		$where_str = "uid = {$uid}";
		$ret = $this->find($where_str);
		return $ret;
	}

	/*
     * ��ȡģ���û���
     *@param int $uid
     *return string name
	*/
	public function get_user_name_by_user_id($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ�ղ���Ϊ��' );
		}
		$where_str = "uid = {$uid}";
		$this->setTableName ( 'model_info_tbl' );
		$ret = $this->find($where_str, 'name');
		return $ret['name'];
	}

	/*
     * �ж��Ƿ����ģ�ؿ���
     * @param int $uid
     * retuen boolean
	*/
	public function get_is_set_by_user_id($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ�ղ���Ϊ��' );
		}
		$this->setTableName ( 'model_info_tbl' );
		$where_str = "uid = {$uid}";
		if ($this->find($where_str)) 
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	/*
     * ��ȡ������Ϣ
     * @param int $uid
     * return array $ret
     * 
	*/
	public function get_model_other($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ�ղ���Ϊ��' );
		}
		$this->setTableName( 'model_other_tbl' );
		$where_str = "uid = {$uid}";
		$ret = $this->find($where_str);
		return $ret;
	}

	/*
     * ��ȡother ��uid����
     *@param boolean $b_select_count
     *@param string $where_str
     *@param string $order by
     *@param sting limit
     *@param string $fields
     *return array $ret
	*/
	public function get_model_other_list($b_select_count = false, $where_str = '', $order_by = 'uid DESC', $limit = '0,10', $fields = '*')
	{
		$this->setTableName( 'model_other_tbl' );
		if ($b_select_count == true) 
		{
			$ret = $this->findCount($where_str);
		}
		else
		{
			//var_dump($order_by);
			$ret = $this->findAll( $where_str, $limit, $order_by, $fields );
			//var_dump($ret);
		}
		return $ret;
	}



	/*
     * ��ȡ������Ϣ
     * @param int $uid
     * return array $ret
     * 
	*/
	public function  get_model_follow($b_select_count = false,$uid, $limit = '0,3', $sort = 'uid DESC', $fields = '*')
	{ 
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ�ղ���Ϊ��' );
		}
		$this->setTableName( 'model_follow_infornatio_tbl' );
		$where_str = "uid = {$uid}";
		if ($b_select_count == true) 
		{
			$ret = $this->findCount($where_str);
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
	public function get_model_pic($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ�ղ���Ϊ��' );
		}
		$this->setTableName( 'model_pic_tbl' );
		$where_str = "uid = {$uid}";
		$ret = $this->find($where_str);
		return $ret;
	}

	/*
     * ְҵ��Ϣ
     * @param int $uid
     * return array $ret
     * 
	*/
	public function get_model_profession($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ�ղ���Ϊ��' );
		}
		$this->setTableName( 'model_profession_info_tbl' );
		$where_str = "uid = {$uid}";
		$ret = $this->find($where_str);
		return $ret;
	}


	/*
     * ������Ϣ
     * @param int $uid
     * return array $ret
     * 
	*/
	public function get_model_score($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ�ղ���Ϊ��' );
		}
		$this->setTableName( 'model_score_tbl' );
		$where_str = "uid = {$uid}";
		$ret = $this->find($where_str);
		return $ret;
	}

	/*
     * ������Ϣ
     * @param int $uid
     * return array $ret
     * 
	*/
	public function get_model_stature($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ�ղ���Ϊ��' );
		}
		$this->setTableName( 'model_stature_tbl' );
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

	public function get_follow_uid($where_str, $limit = '',$sort = 'uid DESC', $fields = '*')
	{
		if (empty($where_str)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':��������Ϊ�ղ���Ϊ��' );
		}
		$this->setTableName( 'model_follow_infornatio_tbl' );
		$ret = $this->findAll($where_str, $limit, $sort, $fields );
		return $ret;
	}


	/* 
     * �����������������ѯuid
     * @param string $where_str
     * @param string DISTINCT(uid)
     * return array id
	*/
	public function get_search_uid($where_str, $limit = '0,10',$sort = 'uid DESC', $fields = '*')
	{
		/*if (empty($where_str)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':��������Ϊ��' );
		}*/
		$this->setTableName( 'model_search_tbl' );
		$ret = $this->findAll($where_str, $limit, $sort, $fields );
		return $ret;
	}


	/*
     * ��ʾͼƬ
     *@param  uid
     *
	*/
	public function get_pic_list($b_select_count = false,$where_str = '', $sort = 'uid DESC', $limit = '0,10', $fields = '*')
	{
		$this->setTableName( 'model_pic_tbl' );
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
	public function del_style($uid)
	{
		$this->setTableName( 'model_style_tbl' );
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$where_str = "uid = {$uid}";
		return $this->delete($where_str);
	}

	/*
     *��������� 
     * @param int uid
     * @param array $data
     *
	*/ 
	public function add_style($uid, $data)
	{
		$this->setTableName( 'model_style_tbl' );
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		if (empty($data) || !is_array($data)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':��������Ϊ��' );
		}
		$data['uid'] = $uid;
		return $this->insert($data);
	}

	/*
	 *��ȡ������
	 *@param int uid
	 *return array data
     *
	*/
	public function list_style($uid, $sort = 'uid ASC', $limit = '0,10', $fields = '*')
	{
		$this->setTableName( 'model_style_tbl' );
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$where_str = "uid = {$uid}";
		return $this->findAll($where_str, $limit, $sort, $fields);
	}


	/*
	 * ������ȡ������
	 * $where_str
     *
	*/
	public function search_style($where_str = '', $sort = 'uid DESC', $limit = '0,99999999', $fields = 'uid')
	{
		$this->setTableName( 'model_style_tbl' );
		if (empty($where_str)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':��ѯ��������Ϊ��' );
		}
		$ret = $this->findAll($where_str, $limit, $sort, $fields);
		return $ret;
	}


	/*
	 *ɾ��ģ��ͼƬ
	 *@param int id
     * return void
	*/
	public function model_del_pic($id)
	{
		$id = (int)$id;
		if (empty($id)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
		}
		$this->setTableName( 'model_pic_tbl' );
		$where_str = "id = {$id}";
		return $this->delete($where_str);

	}

	/*
     * ɾ����ǩ
     *@param int $uid
     *
	*/
	public function delete_label($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
		}
		$this->setTableName( 'model_label' );
		$where_str = "uid = {$uid}";
		return $this->delete($where_str);
	}
	/*
     * ��ǩ����
     *@param int $uid
     *@param string $label
     *return void;
	*/
	public function insert_label($uid, $data)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
		}
		if (empty($data) || !is_array($data)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ��' );
		}
		$this->setTableName( 'model_label' );
		$data['uid'] = $uid;
		$this->insert($data);
		/*$where_str = "uid = {$uid}";
		if ($this->find($where_str)) 
		{
			$this->delete($where_str);
		}
		if (!empty($label)) 
		{
			$data['uid'] = $uid;
			$label_arr = explode(',', $label);
			foreach ($label_arr as $key => $vo) 
			{
				$data['label'] = $vo;
				$this->insert($data);
			}
		}*/
		//$data['uid'] = $uid;
	}

	/*
     *��ȡ��ǩ����
     *@param int $uid
     * return array data
	*/
	public function get_label_info($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
		}
		$this->setTableName( 'model_label' );
		$where_str = "uid = {$uid}";
		$ret = $this->findAll($where_str);
		return $ret;
	}

	/*
     *��ȡ���б�ǩ����
     *@param string $where_str
     * return array data
	*/
	public function get_label_list($where_str = '', $limit = '0,10', $sort = 'uid DESC', $fields = '*')
	{
		$this->setTableName( 'model_label' );
		$ret = $this->findAll($where_str, $limit, $sort, $fields);
		return $ret;
	}

	/*
     * ��ȡһ����ǩ����
     *@param int $uid
     * return array $ret
	*/
	public function find_label_info($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
		}
		$this->setTableName( 'model_label' );
		$where_str = "uid = {$uid}";
		$ret = $this->find($where_str);
		return $ret;
	}

	/*
	*��ȡһ����Ƭ��������
	*@param int $uid
	* return array $ret
	*/
	public function find_purl_info($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$where_str = "uid = {$uid}";
		$this->setTableName( 'model_purl' );
		$ret = $this->find($where_str);
		return $ret;
	}

	/*
     * ��ȡ��Ƭ����
     *@param int uid
     * return array $ret
     *
	*/
	public function get_purl($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
		}
		$where_str = "uid = {$uid}";
		$this->setTableName( 'model_purl' );
		$ret = $this->findAll($where_str);
		return $ret;
	}

	/*
     * ɾ����Ƭ����
	 *@param int uid
	*/
	public function delete_purl($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$where_str = "uid = {$uid}";
		$this->setTableName( 'model_purl' );
		return $this->delete($where_str);
	}
	/*
     * ��Ƭ�������
     *@param int uid
     *@pram array $data
	*/
	public function insert_purl($uid, $data)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		if (empty($data) || !is_array($data)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ��' );
		}
		$this->setTableName( 'model_purl' );
		$data['uid'] = $uid;
		return $this->insert($data);	
	}


	/*
     * ��ȡ���Χ
     *@param int uid
     * return array $ret
     *
	*/
	public function get_enter_info($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$where_str = "uid = {$uid}";
		$this->setTableName( 'model_activity_enter' );
		$ret = $this->findAll($where_str);
		return $ret;
	}

	/*
     * ɾ�����Χ
	 *@param int uid
	*/
	public function delete_enter($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$where_str = "uid = {$uid}";
		$this->setTableName( 'model_activity_enter' );
		return $this->delete($where_str);
	}
	/*
     * ���Χ���
     *@param int uid
     *@pram array $data
	*/
	public function insert_enter($uid, $data)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		if (empty($data) || !is_array($data)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ��' );
		}
		$this->setTableName( 'model_activity_enter' );
		$data['uid'] = $uid;
		return $this->insert($data);	
	}

	/*
     *��ȡ���Χ����
     *@param string $where_str
     * return array data
	*/
	public function get_enter_list($where_str = '', $limit = '0,100', $sort = 'uid DESC', $fields = '*')
	{
		$this->setTableName( 'model_activity_enter' );
		$ret = $this->findAll($where_str, $limit, $sort, $fields);
		return $ret;
	}

	/*
     * ��ȡһ�����Χ����
     *@param int $uid
     * 
	*/
	public function find_enter_info($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$where_str = "uid = {$uid}";
		$this->setTableName( 'model_activity_enter' );
		$ret = $this->find($where_str);
		return $ret;
	}


	/*
     * ��ȡ�����
     *@param int uid
     * return array $ret
     *
	*/
	public function get_join_info($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$where_str = "uid = {$uid}";
		$this->setTableName( 'model_activity_join' );
		$ret = $this->findAll($where_str);
		return $ret;
	}

	/*
     * ɾ�������
	 *@param int uid
	*/
	public function delete_join($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$where_str = "uid = {$uid}";
		$this->setTableName( 'model_activity_join' );
		return $this->delete($where_str);
	}
	/*
     * ��������
     *@param int uid
     *@pram array $data
	*/
	public function insert_join($uid, $data)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		if (empty($data) || !is_array($data)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ��' );
		}
		$this->setTableName( 'model_activity_join' );
		$data['uid'] = $uid;
		return $this->insert($data);	
	}

	/*
     *��ȡ���������
     *@param string $where_str
     * return array data
	*/
	public function get_join_list($where_str = '', $limit = '0,100', $sort = 'uid DESC', $fields = '*')
	{
		$this->setTableName( 'model_activity_join' );
		$ret = $this->findAll($where_str, $limit, $sort, $fields);
		return $ret;
	}

	/*
     * ��ȡһ�����������
     * @param int $uid
     * return array $ret
	*/
	public function find_join_info($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$where_str = "uid = {$uid}";
		$this->setTableName( 'model_activity_join' );
		$ret = $this->find($where_str);
		return $ret;
	}

	/*
     * ��һ���������Ķ�ά����
     * ת��Ϊһά����
     *@param array $data
     *return $tmp_data
	*/
	public function change_user_id_array($data)
	{
		$tmp_data  = array();
		if (!empty($data) && is_array($data)) 
		{
			foreach ($data as $key => $vo) 
		    {
			   $tmp_data[] = $vo['uid'];
		    }
		}
		
		return $tmp_data;
	}

	/*
     * ����ְҵ
     * @param int uid
     * return $state
	*/
	public function get_state_by_user_id($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$where_str = "uid = {$uid}";
		$this->setTableName( 'model_profession_info_tbl' );
		$ret = $this->find('p_state',$where_str);
		return $ret['p_state'];
	}

	/*
     * ��ȡѧУ����
     *@param int $uid
     *
	*/
	public function get_p_school_by_user_id($uid)
	{
		$uid = (int)$uid;
		if (empty($uid)) 
		{
			return false;
			//throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':UID����Ϊ��' );
		}
		$where_str = "uid = {$uid}";
		$this->setTableName( 'model_profession_info_tbl' );
		$ret = $this->find('p_school',$where_str);
		return $ret['p_school'];
	}

}

?>
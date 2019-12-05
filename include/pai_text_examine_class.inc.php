<?php
/*
 * ������˲�����
 */

class pai_text_examine_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_log_db' );
		$this->setTableName ( 'text_examine_log' );
	}

    /**
     * @param $insert_data
     * @return bool|int
     */
    private function add_info($insert_data)
    {
        if (empty ( $insert_data ) || !is_array($insert_data)) return false;

        return $this->insert ( $insert_data,'REPLACE');
    }

    /**
     * ���log����
     * @param int $user_id
     * @param string $type ����
     * @param string $before �޸�ǰ����
     * @param string $after �޸ĺ�����
     * @return int
     */
    public function audit_text($user_id,$type='',$before='',$after='')
    {
        $user_id = intval($user_id);
        $type = trim($type);
        $before = trim($before);
        $after = trim($after);
        $data['user_id'] = $user_id;
        if($user_id <1) return false;
        if(strlen($after)<1 && strlen($before) <1) return false;
        if($before == $after) return false;
        $data['type'] = $type;
        $data['before_edit'] = $before;
        $data['after_edit'] = $after;
        $data['add_time'] = date("Y-m-d H:i:s");
        return $this->add_info($data);

    }


###################################################�����ǾɵĴ��룬����ɾ��#############################################################################

    /**
     * �������
     * @param bool $b_select_count
     * @param string $where_str  ��ѯ����
     * @param string $order_by ����
     * @param string $limit
     * @param string $fields ��ѯ�ֶ�
     * @return array|int
     */
    public function get_text_examine_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
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
     *  ��ȡ�ǳ�
     * @param int $user_id
     * @return bool
     */
    public function get_nickname($user_id = 0)
	{
		$user_id = (int)$user_id;
		if (empty($user_id))
		{
			return false;
		}
		$where_str = "user_id = {$user_id}";
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_user_tbl' );
		$ret = $this->find ( $where_str );
		return $ret['nickname'];
	}

    /**
     * �Ӻ�ɫ��ע
     * @param $before_edit
     * @param $after_edit
     * @return string
     */
    public function add_red($before_edit, $after_edit)
	{
		if ($before_edit != $after_edit)
		{
			return "<font style='color:red'>{$after_edit}</font>";
		}
		else
		{
			return $after_edit;
		}

	}

    /**
     * �жϱ��Ƿ����
     * @param $tab
     * @return bool
     * @throws App_Exception
     */
    public function create_text_tab($tab)
	{
		$res = $this->query("SHOW TABLES FROM pai_log_db LIKE '{$tab}'");
		if (empty($res) || !is_array($res))
		{
			$creat_sql = "CREATE TABLE IF NOT EXISTS {$tab} (
                                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                `user_id` int(10) unsigned NOT NULL,
                                `type` varchar(50) NOT NULL,
                                `before_edit` text NOT NULL,
                                `after_edit` text NOT NULL,
                                `add_time` datetime NOT NULL,
                                `audit_id` int(10) unsigned NOT NULL,
                                `audit_time` datetime NOT NULL,
                                PRIMARY KEY (`id`),
                                KEY `user_id` (`user_id`),
                                KEY `audit_id` (`audit_id`)
                              )DEFAULT CHARSET=gbk";
			$this->query($creat_sql);
		}
		return true;

	}

    /**
     * ����ͨ����˺���
     * @param $audit_id
     * @param $audit_time
     * @param $ids
     * @return bool
     * @throws App_Exception
     */
    public function pass_examine_text($audit_id, $audit_time, $ids)
	{
		$audit_id = (int)$audit_id;
		if (empty($audit_id))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�����ID����Ϊ��' );
		}
		if (!is_array($ids) || empty($ids))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�ύ���������ݲ���Ϊ��' );
		}
		$tmp_date = date('Ym', $audit_time);
		$tab = 'pai_log_db.text_examine_pass_log_'.$tmp_date;
		//�ж��Ƿ��Ѿ����ڱ���,�����ڴ���
		$this->create_text_tab($tab);
		$audit_time = date('Y-m-d H:i:s', $audit_time);
		global $yue_login_id;
		foreach ($ids as $key => $val)
		{
			$id = (int)$val;
			$where_str = "id = {$id}";
			$ret = $this->find($where_str);
			$user_id     = $ret['user_id'];
			$type        = $ret['type'];
			$before_edit = addslashes($ret['before_edit']);
			$after_edit  = addslashes($ret['after_edit']);
			$add_time    = $ret['add_time'];
			//��������
			$this->query("SET AUTOCOMMIT=0");
			//����ʼ
			$this->query("BEGIN");
			$insert_sql = "INSERT INTO {$tab} (`user_id`, `type`, `before_edit`,`after_edit`,`add_time`,`audit_id`, `audit_time`) VALUES ({$user_id},'{$type}','{$before_edit}','{$after_edit}','{$add_time}',{$audit_id},'{$audit_time}')";
			//die($insert_sql);
			$this->query($insert_sql);
			$res1 = $this->get_affected_rows();
			$this->delete($where_str);
			$res2 = $this->get_affected_rows();
			if (!$res1 || !$res2)
			{
				 $this->query("ROLLBACK"); //�ж�ִ��ʧ�ܻع�
			}
			//����ִ��
			$this->query("COMMIT"); //ִ������
			unset($where_str);
			unset($ret);
		}
		return true;
	}


    /**
     * ɾ�����ֺ���
     * @param $audit_id
     * @param $audit_time
     * @param $ids
     * @return bool
     * @throws App_Exception
     */
    public function del_examine_text($audit_id, $audit_time, $ids)
	{
		$audit_id = (int)$audit_id;
		if (empty($audit_id))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�����ID����Ϊ��' );
		}
		if (!is_array($ids) || empty($ids))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�ύ���������ݲ���Ϊ��' );
		}
		$tmp_date = date('Ym', $audit_time);
		$tab = 'pai_log_db.text_examine_del_log_'.$tmp_date;
		$this->create_text_tab($tab);
	    $audit_time = date('Y-m-d H:i:s', $audit_time);
	   foreach ($ids as $key => $val)
		{
			$id = (int)$val;
			$where_str = "id = {$id}";
			$ret = $this->find($where_str);
			$user_id     = $ret['user_id'];
			$type        = $ret['type'];
			$before_edit = addslashes($ret['before_edit']);
			$after_edit  = addslashes($ret['after_edit']);
			$add_time    = $ret['add_time'];
			//��������
			$this->query("SET AUTOCOMMIT=0");
			//����ʼ
			$this->query("BEGIN");
			$insert_sql = "INSERT INTO {$tab} (`user_id`, `type`, `before_edit`,`after_edit`,`add_time`,`audit_id`, `audit_time`) VALUES ({$user_id},'{$type}','{$before_edit}','{$after_edit}','{$add_time}',{$audit_id},'{$audit_time}')";
			//die($insert_sql);
			$this->query($insert_sql);
			$res1 = $this->get_affected_rows();
			$this->delete($where_str);
			$res2 = $this->get_affected_rows();
			if (!$res1 || !$res2)
			{
				 $this->query("ROLLBACK"); //�ж�ִ��ʧ�ܻع�
			}
			//����ִ��
			$this->query("COMMIT"); //ִ������
			//�û��ǳ��޸�Ϊ�ֻ��û�+���4λ
			$user_obj = POCO::singleton ('pai_user_class');
			//��ע�ӿ�
			$model_card_obj = POCO::singleton('pai_model_card_class');
			if ($type == 'nickname_text')
			{
				$user_res = $user_obj->get_user_info($user_id);
				$update_data['nickname'] = '�ֻ��û�'.substr($user_res['cellphone'], -4);
			    $res3 = $user_obj->update_user($update_data, $user_id);
			    unset($update_data);
			}
			elseif ($type == 'model_card_remark')
			{
				$update_data ['intro'] = '';
				$res3 = $model_card_obj->update_model_card($update_data, $user_id);
				unset($update_data);
			}
			unset($where_str);
			unset($ret);
		}
		return true;
	}


    /**
     * ���Ѿ�������ֵ�ɾ��
     * @param $ymonth
     * @param $audit_id
     * @param $audit_time
     * @param $ids
     * @return bool
     * @throws App_Exception
     */
    public function delPass_examine_text($ymonth,$audit_id, $audit_time,$ids)
	{
		if (empty($ymonth) || date('Ym',strtotime($ymonth)) != $ymonth)
		{
			$ymonth = date('Ym', time());
		}
		$audit_id = (int)$audit_id;
		if (empty($audit_id))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�����ID����Ϊ��' );
		}
		if (!is_array($ids) || empty($ids))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�ύ���������ݲ���Ϊ��' );
		}
		$tmp_date = date('Ym', $audit_time);
		$tab_pass = "text_examine_pass_log_".$ymonth;
		$tab_del  = "pai_log_db.text_examine_del_log_".$tmp_date;
		$this->create_text_tab($tab_del);
	    $this->setTableName ( $tab_pass );
	    $audit_time = date('Y-m-d H:i:s', $audit_time);
	    foreach ($ids as $key => $id)
		{
			$id = (int)$id;
			$where_str = "id = {$id}";
			$row = $this->find($where_str);
			$user_id     = $row['user_id'];
			$type        = $row['type'];
			$before_edit = addslashes($row['before_edit']);
			$after_edit  = addslashes($row['after_edit']);
			$add_time    = $row['add_time'];
			//��������
			$this->query("SET AUTOCOMMIT=0");
			//����ʼ
			$this->query("BEGIN");
			$insert_sql = "INSERT INTO {$tab_del} (`user_id`, `type`, `before_edit`,`after_edit`,`add_time`,`audit_id`, `audit_time`) VALUES ({$user_id},'{$type}','{$before_edit}','{$after_edit}','{$add_time}',{$audit_id},'{$audit_time}')";
			$this->query($insert_sql);
			$res1 = $this->get_affected_rows();
			$this->delete($where_str);
			$res2 = $this->get_affected_rows();
			if (!$res1 || !$res2)
			{
				 $this->query("ROLLBACK"); //�ж�ִ��ʧ�ܻع�
			}
			//����ִ��
			$this->query("COMMIT"); //ִ������
			//�û��ǳ��޸�$after_edit
			$user_obj = POCO::singleton ('pai_user_class');
			//��ע�ӿ�
			$model_card_obj = POCO::singleton('pai_model_card_class');
			$user_id = (int)$row['user_id'];
			if ($user_res['nickname'] == $after_edit)
			{
				if ($type == 'nickname_text')
				{
					$user_res = $user_obj->get_user_info($user_id);
					$update_data['nickname'] = '�ֻ��û�'.substr($user_res['cellphone'], -4);
				    $res3 = $user_obj->update_user($update_data, $user_id);
				    unset($update_data);
				}
				elseif ($type == 'model_card_remark')
				{
					$update_data ['intro'] = '';
					$res3 = $model_card_obj->update_model_card($update_data, $user_id);
					unset($update_data);
				}
			}

		}
		return true;
	}

    /**
     * ��ɾ�����Ѿ����
     * @param $ymonth
     * @param $audit_id
     * @param $audit_time
     * @param $ids
     * @return bool
     * @throws App_Exception
     */
    public function passDel_examine_text($ymonth,$audit_id, $audit_time, $ids)
	{
		if (empty($ymonth) || date('Ym',strtotime($ymonth)) != $ymonth)
		{
			$ymonth = date('Ym', time());
		}
		$audit_id = (int)$audit_id;
		if (empty($audit_id))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�����ID����Ϊ��' );
		}
		if (!is_array($ids) || empty($ids))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�ύ���������ݲ���Ϊ��' );
		}
		$ymonth   = (int)$ymonth;
		$tmp_date = date('Ym', $audit_time);
		$tab_del  = 'text_examine_del_log_'.$ymonth;
		$tab_pass = 'pai_log_db.text_examine_pass_log_'.$tmp_date;
		$this->create_text_tab($tab_pass);
		$this->setTableName ( $tab_del );
		$audit_time = date('Y-m-d H:i:s', $audit_time);
	    foreach ($ids as $key => $id)
		{
			$id = (int)$id;
			$where_str = "id = {$id}";
			$row  = $this->find($where_str);
			$user_id     = $row['user_id'];
			$type        = $row['type'];
			$before_edit = addslashes($row['before_edit']);
			$after_edit  = addslashes($row['after_edit']);
			$add_time    = $row['add_time'];
			//��������
			$this->query("SET AUTOCOMMIT=0");
			//����ʼ
			$this->query("BEGIN");
			$insert_sql = "INSERT INTO {$tab_pass} (`user_id`, `type`, `before_edit`,`after_edit`,`add_time`,`audit_id`, `audit_time`) VALUES ({$user_id},'{$type}','{$before_edit}','{$after_edit}','{$add_time}',{$audit_id},'{$audit_time}')";
			$this->query($insert_sql);
			$res1 = $this->get_affected_rows();
			$this->delete($where_str);
			$res2 = $this->get_affected_rows();
			if (!$res1 || !$res2)
			{
				 $this->query("ROLLBACK"); //�ж�ִ��ʧ�ܻع�
			}
			//����ִ��
			$this->query("COMMIT"); //ִ������
			//�û��ǳ��޸�$after_edit
			$user_obj = POCO::singleton ('pai_user_class');
			//��ע�ӿ�
			$model_card_obj  = POCO::singleton('pai_model_card_class');
			$user_id = (int)$row['user_id'];
			if ($type == 'nickname_text')
			{
				$user_res = $user_obj->get_user_info($user_id);
				if ($user_res['nickname'] == ('�ֻ��û�'.substr($user_res['cellphone'], -4)))
				{
					$update_data['nickname'] = $after_edit;
					$res3 = $user_obj->update_user($update_data, $user_id);
				    unset($update_data);
				}
			}
			elseif($type == 'model_card_remark')
			{
				$model_card_data = $model_card_obj->get_model_card_by_user_id($user_id);
				if ($model_card_data['intro'] == '���˺�����ʲô��û����')
				{
					$update_data ['intro'] = $after_edit;
					$res3 = $model_card_obj->update_model_card($update_data, $user_id);
					unset($update_data);
				}

			}


		}
		return true;
	}


}

?>
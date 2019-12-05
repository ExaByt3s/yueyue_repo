<?php
/*
 * ͼƬ��˲�����
 */

class pai_pic_examine_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_log_db' );
		$this->setTableName ( 'pic_examine_log' );
	}
	
	
	/*
	 * ��ȡͼƬ
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_pic_examine_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
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
	
	/*
     *�������ݱ�
     *
     * @param string $tab
     * @param return true
	*/

	public function create_pic_tab($tab)
	{
		if (empty($tab)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݱ����Ʋ���Ϊ��' );
		}
		$res = $this->query("SHOW TABLES FROM pai_log_db LIKE '{$tab}'");
		if (empty($res) || !is_array($res))
		{
			$tab_sql = "CREATE TABLE IF NOT EXISTS {$tab} (      
                               `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                               `role` varchar(100) NOT NULL,  
                               `user_id` int(10) NOT NULL,                     
                               `img_url` varchar(100) NOT NULL,                
                               `add_time` datetime NOT NULL,                   
                               `audit_id` int(10) NOT NULL,                    
                               `audit_time` datetime NOT NULL,
                               `img_type` varchar(100) NOT NULL,                 
                               PRIMARY KEY (`id`)                              
                             ) DEFAULT CHARSET=GBK";
			$this->query($tab_sql);
		}
		return true;
	}
	/*
	 * ɾ��ͼƬ���Ұ�ɾ�������ݷ������ݿ���pic_examine_del_log_201412��
	 * @param int $audit_id
	 * @param datetime $audit_time
	 * @param array $ids 
	 * 
	 * return true
	 */
	public function del_examine_pic($audit_id, $audit_time, $ids,$content, $tpl_id)
	{
		$send_message_obj = POCO::singleton('pai_pic_send_message_class');
		$pai_pic_obj =  POCO::singleton('pai_pic_class');
		$audit_id = ( int ) $audit_id;
		$tpl_id   = (int)$tpl_id;
		if (empty ( $audit_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�����ID����Ϊ��' );
		}
		if (empty($ids) || !is_array($ids)  ) {
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�ύ���������ݲ���Ϊ�ղ���Ϊ��' );
		}
		$tmp_date = date('Ym', $audit_time);
		$tab = "pai_log_db.pic_examine_del_log_".$tmp_date;
		$this->create_pic_tab($tab);
		$audit_time = date('Y-m-d H:i:s', $audit_time);
		foreach ($ids as $key => $id) 
		{
			$id = (int)$id;
			$where_str = "id= {$id}";
			$row  = $this->find($where_str);
			$user_id  = $row['user_id'];
			$img_url  = $row['img_url'];
			$img_url_rm = $row['img_url'].'.rm';
			$add_time = $row['add_time'];
			$role     = $row['role'];
			$img_type = $row['img_type'];
			//��������
			$this->query("SET AUTOCOMMIT=0");
			//����ʼ
			$this->query("BEGIN");
			$insert_sql = "INSERT INTO {$tab} (`user_id`, `img_url`, `add_time`, `audit_id`, `audit_time`, `role`, `img_type`) VALUES ({$user_id},'{$img_url_rm}','{$add_time}',{$audit_id},'{$audit_time}','{$role}','{$img_type}')";
			$this->query($insert_sql);
			$res1 = $this->get_affected_rows();
			$this->delete($where_str);
			$res2 = $this->get_affected_rows();
			if (!$res1 || !$res2) 
			{
				 $this->query("ROLLBACK"); //�ж�ִ��ʧ�ܻع�
			}

			//�Ƴ�ͼƬ
			$this->server_pic_rm($img_url,$img_type);
			$add_time = date('Y-m-d',strtotime($add_time));
			if (empty($tpl_id)) 
			{
				send_message_for_10005($user_id, $content);
				//$send_message_obj->add_send_message($user_id, $tpl_id, $add_time);
			}
			else
			{
				$info = $send_message_obj->get_pic_send_message_info($user_id, $tpl_id, $add_time);
				//var_dump($info);exit;
				if ($info) 
				{
					send_message_for_10005($user_id, $content);
					$send_message_obj->add_send_message($user_id, $tpl_id, $add_time);
				}
			}
			//����ִ��
			$this->query("COMMIT"); //ִ������
			//�Ƴ�app ͼƬ
			if ($img_type == 'works')
			{
				$img_url2 = str_replace('/disk/data/htdocs233/pic16/yueyue/', '', $img_url);
				$img_url2 = substr($img_url2 , 0,strrpos($img_url2, '.')) ;
				//echo $img_url2;
				/*echo $img_url2;
				echo "<br/>";
				echo $user_id;*/
				$info22 = $pai_pic_obj->del_audit_pic($user_id,$img_url2);
				/*echo "<br/>";
				echo $info22;*/
				unset($img_url2);
			}
		}
		return true;
	}

	/**
	 * �Ƴ�ͼƬ
	 * @param  [string] $img_url [ͼƬ��ַ]
	 * @param  [stirng] $img_type  [ͷ�������Ʒ]
	 * @return [type]            [description]
	 */
	public function server_pic_rm($img_url,$img_type)
	{
		/*$gmclient= new GearmanClient();
        $gmclient->addServers("172.18.5.236:9870");
        $gmclient->setTimeout(5000); // ���ó�ʱ
        $data[0] = $img_url;
        if($img_type == 'works' || $img_type== 'merchandise')
        {
        	$data[1] = yueyue_resize_act_img_url($img_url,145);
        	$data[2] = yueyue_resize_act_img_url($img_url,260);
        	$data[3] = yueyue_resize_act_img_url($img_url,165);
        	$data[4] = yueyue_resize_act_img_url($img_url,320);
        	$data[5] = yueyue_resize_act_img_url($img_url,440);
        	$data[6] = yueyue_resize_act_img_url($img_url,640);
        }
        elseif($img_type == 'head')
        {
        	$data[1] = yueyue_resize_act_img_url($img_url,32);
        	$data[2] = yueyue_resize_act_img_url($img_url,64);
        	$data[3] = yueyue_resize_act_img_url($img_url,86);
        	$data[4] = yueyue_resize_act_img_url($img_url,100);
        	$data[5] = yueyue_resize_act_img_url($img_url,165);
        	$data[6] = yueyue_resize_act_img_url($img_url,468);
        }
        //var_dump($data);
        foreach ($data as $key => $val) 
        {
        	$req_param['source_path'] = $val;
        	$req_param['dest_path']   = $val.'.rm';
        	//var_dump($req_param);exit;
        	$result= $gmclient->do("file_rename",json_encode($req_param) );
        	unset($req_param);
        }*/
        //print_r($data);exit;

		$gmclient = POCO::singleton('pai_gearman_base_class');
		$gmclient->connect('172.18.5.236', '9870');

		$data[0] = $img_url;

		if($img_type == 'works' || $img_type== 'merchandise')
		{
			$data[1] = yueyue_resize_act_img_url($img_url,145);
			$data[2] = yueyue_resize_act_img_url($img_url,260);
			$data[3] = yueyue_resize_act_img_url($img_url,165);
			$data[4] = yueyue_resize_act_img_url($img_url,320);
			$data[5] = yueyue_resize_act_img_url($img_url,440);
			$data[6] = yueyue_resize_act_img_url($img_url,640);
		}
		elseif($img_type == 'head')
		{
			$data[1] = yueyue_resize_act_img_url($img_url,32);
			$data[2] = yueyue_resize_act_img_url($img_url,64);
			$data[3] = yueyue_resize_act_img_url($img_url,86);
			$data[4] = yueyue_resize_act_img_url($img_url,100);
			$data[5] = yueyue_resize_act_img_url($img_url,165);
			$data[6] = yueyue_resize_act_img_url($img_url,468);
		}

		foreach ($data as $key => $val)
		{
			$req_param['source_path'] = $val;
			$req_param['dest_path']   = $val.'.rm';
			$result= $gmclient->_do("file_rename",$req_param);
			unset($req_param);
		}

		return $result;
	}

	/**
	 * �Ƴ�ͼƬ
	 * @param  [string] $img_url [ͼƬ��ַ]
	 * @param  [stirng] $img_type  [ͷ�������Ʒ]
	 * @return [type]            [description]
	 */
	public function server_pic_recover($img_url,$img_type)
	{
		/*$gmclient= new GearmanClient();
        $gmclient->addServers("172.18.5.236:9870");
        $gmclient->setTimeout(5000); // ���ó�ʱ
        $data[0] = $img_url;
        //echo $img_url;
        if($img_type == 'works' || $img_type == 'merchandise')
        {
        	$data[1] = yueyue_resize_act_img_url($img_url,145);
        	$data[2] = yueyue_resize_act_img_url($img_url,260);
        	$data[3] = yueyue_resize_act_img_url($img_url,165);
        	$data[4] = yueyue_resize_act_img_url($img_url,320);
        	$data[5] = yueyue_resize_act_img_url($img_url,440);
        	$data[6] = yueyue_resize_act_img_url($img_url,640);
        }
        elseif($img_type == 'head')
        {
        	$data[1] = yueyue_resize_act_img_url($img_url,32);
        	$data[2] = yueyue_resize_act_img_url($img_url,64);
        	$data[3] = yueyue_resize_act_img_url($img_url,86);
        	$data[4] = yueyue_resize_act_img_url($img_url,100);
        	$data[5] = yueyue_resize_act_img_url($img_url,165);
        	$data[6] = yueyue_resize_act_img_url($img_url,468);
        }
        //var_dump($data);
        foreach ($data as $key => $val) 
        {
        	$req_param['source_path'] = $val;
        	$req_param['dest_path']   = str_replace('.rm', '', $val);
        	$result= $gmclient->do("file_rename",json_encode($req_param) );
        	unset($req_param);
        }*/
        //print_r($data);exit;

		$gmclient = POCO::singleton('pai_gearman_base_class');
		$gmclient->connect('172.18.5.236', '9870');

		$data[0] = $img_url;
		if($img_type == 'works' || $img_type == 'merchandise')
		{
			$data[1] = yueyue_resize_act_img_url($img_url,145);
			$data[2] = yueyue_resize_act_img_url($img_url,260);
			$data[3] = yueyue_resize_act_img_url($img_url,165);
			$data[4] = yueyue_resize_act_img_url($img_url,320);
			$data[5] = yueyue_resize_act_img_url($img_url,440);
			$data[6] = yueyue_resize_act_img_url($img_url,640);
		}
		elseif($img_type == 'head')
		{
			$data[1] = yueyue_resize_act_img_url($img_url,32);
			$data[2] = yueyue_resize_act_img_url($img_url,64);
			$data[3] = yueyue_resize_act_img_url($img_url,86);
			$data[4] = yueyue_resize_act_img_url($img_url,100);
			$data[5] = yueyue_resize_act_img_url($img_url,165);
			$data[6] = yueyue_resize_act_img_url($img_url,468);
		}

		foreach ($data as $key => $val)
		{
			$req_param['source_path'] = $val;
			$req_param['dest_path']   = str_replace('.rm', '', $val);
			$result= $gmclient->_do("file_rename",$req_param);
			unset($req_param);
		}

		return $result;
	}
	/*	
     *	���ͼƬ�ֶΣ����Ұ����ݿ����pic_examine_pass_log_201412
     * @param int $audit_id
	 * @param datetime $audit_time
	 * @param array $ids 	
     *	return true
	*/
	public function pass_examine_pic($audit_id, $audit_time, $ids)
	{
		$audit_id = ( int ) $audit_id;
		if (empty ( $audit_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�����ID����Ϊ��' );
		}
		if (empty($ids) || !is_array($ids)  ) {
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�ύ���������ݲ���Ϊ�ղ���Ϊ��' );
		}
		$tmp_date = date('Ym', $audit_time);
		$tab = 'pai_log_db.pic_examine_pass_log_'.$tmp_date;
		$this->create_pic_tab($tab);
		$audit_time = date('Y-m-d H:i:s', $audit_time);
		foreach ($ids as $key => $id) 
		{
			$id = (int)$id;
			$where_str = "id= {$id}";
			$row  = $this->find($where_str);
			$user_id  = $row['user_id'];
			$img_url  = $row['img_url'];
			$add_time = $row['add_time'];
			$role     = $row['role'];
			$img_type = $row['img_type'];
			//��������
			$this->query("SET AUTOCOMMIT=0");
			//����ʼ
			$this->query("BEGIN");
			$insert_sql = "INSERT INTO {$tab} (`user_id`, `img_url`, `add_time`, `audit_id`, `audit_time`, `role`, `img_type`) VALUES ({$user_id},'{$img_url}','{$add_time}',{$audit_id},'{$audit_time}','{$role}','{$img_type}')";
			$this->query($insert_sql);
			$res1 = $this->get_affected_rows();
			//die($del_sql);
			$this->delete($where_str);
			$res2 = $this->get_affected_rows();
			//die($del_sql);
			if (!$res1 || !$res2) 
			{
				 $this->query("ROLLBACK"); //�ж�ִ��ʧ�ܻع�
			}
			//����ִ��
			$this->query("COMMIT"); //ִ������
		}
		return true;
	}

	
	/*
	 * �����ͨ�������ݷ���ɾ������
	 * @param int $audit_id
	 * @param datetime $audit_time
	 * @param array $ids 
	 * 
	 * return true
	 */
	public function delpass_examine_pic($ymonth, $audit_id, $audit_time, $ids)
	{
        $ymonth = trim($ymonth);

        if(strlen($ymonth) >0)
        {
            $ymonth = date('Ym',strtotime($ymonth.'01'));
        }
        else
        {
            $ymonth = date('Ym', time());
        }
		$audit_id = (int)$audit_id;
		if (empty($audit_id)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�����ID����Ϊ�ղ���Ϊ��' );
		}
		if (empty($ids) || !is_array($ids)  ) {
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�ύ���������ݲ���Ϊ�ղ���Ϊ��' );
		}
		$tmp_date = date('Ym', $audit_time);
		$ymonth   = (int)$ymonth;
		$tab_pass = "pic_examine_pass_log_".$ymonth;
		$this->setTableName ( $tab_pass );
		$tab_del  = 'pai_log_db.pic_examine_del_log_'.$tmp_date;
		$this->create_pic_tab($tab_del);	
		$audit_time = date('Y-m-d H:i:s', $audit_time);
		foreach ($ids as $key => $id) 
		{
			$id = (int)$id;
			$where_str = "id = {$id}";
			$ret = $this->find($where_str);
			$user_id  = $ret['user_id'];
			$img_url  = $ret['img_url'];
			$img_url_rm  = $ret['img_url'].'.rm';
			$add_time = $ret['add_time'];
			$role     = $ret['role'];
			$img_type = $ret['img_type'];
			//��������
			$this->query("SET AUTOCOMMIT=0");
			//����ʼ
			$this->query("BEGIN");
			$insert_sql = "INSERT INTO {$tab_del} (`user_id`, `img_url`, `add_time`, `audit_id`, `audit_time`,`role`,`img_type`) VALUES ({$user_id},'{$img_url_rm}','{$add_time}',{$audit_id},'{$audit_time}','{$role}','{$img_type}')";
			$this->query($insert_sql);
			$res1 = $this->get_affected_rows();
			$this->delete($where_str);
			$res2 = $this->get_affected_rows();
			if (!$res1 || !$res2) 
			{
				$this->query("ROLLBACK"); //�ж�ִ��ʧ�ܻع�
			}
			$this->server_pic_rm($img_url,$img_type);
			//�Ƴ�app ͼƬ
			$pai_pic_obj =  POCO::singleton('pai_pic_class');
			if ($img_type == 'works') 
			{
				$img_url2 = str_replace('/disk/data/htdocs233/pic16/yueyue/', '', $img_url);
				$img_url2 = substr($img_url2 , 0,strrpos($img_url2, '.')) ;
				$info = $pai_pic_obj->del_audit_pic($user_id,$img_url2);
			}
			//����ִ��
			$this->query("COMMIT"); //ִ������
		}
		return true;
	}

	
	
	/*
	 * ��ɾ�������»ָ��������
	 * @param int $audit_id
	 * @param datetime $audit_time
	 * @param array $ids 
	 * 
	 * return true
	 */

	public function returnDel($ymonth, $audit_id, $audit_time,$ids)
	{
        $ymonth = trim($ymonth);
        if(strlen($ymonth) >0)
        {
            $ymonth = date('Ym',strtotime($ymonth.'01'));
        }
        else
        {
            $ymonth = date('Ym', time());
        }
		$audit_id = (int)$audit_id;
		if (empty($audit_id)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�����ID����Ϊ�ղ���Ϊ��' );
		}
		if (empty($ids) || !is_array($ids)  ) {
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�ύ���������ݲ���Ϊ�ղ���Ϊ��' );
		}
		$tmp_date = date('Ym', $audit_time);
		$tab_del = "pic_examine_del_log_".$ymonth;
		$this->setTableName ( $tab_del );
		$tab_pass  = 'pai_log_db.pic_examine_pass_log_'.$tmp_date;
		$this->create_pic_tab($tab_pass);	
		$audit_time = date('Y-m-d H:i:s', $audit_time);
		foreach ($ids as $key => $id) 
		{
			$id = (int)$id;
			$where_str = "id= {$id}";
			$row = $this->find($where_str);
			//print_r($row);exit;
			$user_id  = $row['user_id'];
			$img_url  = $row['img_url'];
			$img_url_recover = str_replace('.rm', '', $row['img_url']);
			$add_time = $row['add_time'];
			$role     = $row['role'];
			$img_type = $row['img_type'];
			//var_dump($row);
			//��������
			$this->query("SET AUTOCOMMIT=0");
			//����ʼ
			$this->query("BEGIN");
			$insert_sql = "INSERT INTO {$tab_pass} (`user_id`, `img_url`, `add_time`, `audit_id`, `audit_time`,`role`,`img_type`) VALUES ({$user_id},'{$img_url_recover}','{$add_time}',{$audit_id},'{$audit_time}','{$role}','{$img_type}')";
			//print_r($insert_sql);exit;
			$this->query($insert_sql);
			$res1 = $this->get_affected_rows();
			$this->delete($where_str);
			$res2 = $this->get_affected_rows();
			//die($del_sql);
			if (!$res1 || !$res2) 
			{
				 $this->query("ROLLBACK"); //�ж�ִ��ʧ�ܻع�
			}
			$this->server_pic_recover($img_url,$img_type);
			//����ִ��
			$this->query("COMMIT"); //ִ������
		}
		return true;
	}

	/*
	 * ��ȡͼƬ���Ե�ַת��ΪurlͼƬ��ַ
     * @param string $img_path
     * return URL (ͼƬurl��ַ)
	*/

	public function change_img_url($img_path = '')
	{
		if (empty($img_path)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ͼƬ·������Ϊ��' );
		}
		$img_url = '';
     	$arr = explode('/', $img_path);
     	$len = count($arr);
     	if ($arr[$len-3] == 'icon') 
     	{
     		$img_url = 'http://yue-icon.yueus.com/';
     	}
        elseif($arr[$len-3] == 'seller_icon')//�̼�ͷ��
        {
            $img_url = 'http://seller-icon.yueus.com/';
        }
     	else
     	{
     		$img_url = 'http://img16.poco.cn/yueyue/';
     	}
     	for ($i= $len-2; $i < $len; $i++) 
     	{ 
     		
     		$img_url .= $arr[$i];
     		if ($len -1 != $i) 
     		{
     			$img_url .= '/';
     		}
     		
     	}
     	return $img_url;
	}

	/**
	 * ����һ������
	 * @param  [int] $id [ͼƬID]
	 * @return [array|false]  [��������]
	 */
	public function get_info($id)
	{
		$id = (int)$id;
		if(empty($id))
		{
			return false;
		}
		return $this->find("id = {$id}");
	}

	/**
	 * ������ͼ
	 * @param  [int] $id [ͼƬID]
	 * @return [string]  [�����޸ĺ��ͼ]
	 */
	public function make_again_pic($id)
	{
		if(empty($id))
		{
			return false;
		}
		$ret = $this->get_info($id);
		if(!is_array($ret))
		{
			return false;
		}
		$width         = 260;
		$height        = 260;
		$src_file_name = $ret['img_url'];
		$dst_file_name = yueyue_resize_act_img_url($ret['img_url'],260);
		/*echo json_encode($req_param);
		exit;*/
		$gmclient= new GearmanClient();
		$gmclient->addServers("172.18.5.236:9880");
		$gmclient->setTimeout(5000); // ���ó�ʱ
		//do
		//{
			$result= $gmclient->do("autoresize","{$width} {$height} {$src_file_name} {$dst_file_name}" );
		//}
		//while($gmclient->returnCode() != GEARMAN_SUCCESS);
		return $this->change_img_url($dst_file_name);
	}
}

?>
<?php
/*
 * ��Ӱʦ��������
 */

class pai_cameraman_card_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_cameraman_card_tbl' );
	}
	
	/*
	 * ������Ӱʦ����
	 * 
	 * 
	 * return bool 
	 */
	public function add_cameraman_card($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
		}
		
		$insert_data ['user_id'] = ( int ) $insert_data ['user_id'];
		
		if (empty ( $insert_data ['user_id'] ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�û�ID����Ϊ��' );
		}
		
		return $this->insert ( $insert_data, "IGNORE" );
	}
	
	/*
	 * ������Ӱʦ����
	 * 
	 * @param array $update_data
	 * @param int   $user_id  
	 * 
	 * ������
	 * $update_data ['intro'] ���
	 * $update_data ['honor'] ����
	 * $update_data ['nickname'] �ǳ�
	 * $update_data['pic_arr'] ͼƬ����
	 */
	public function update_cameraman_card($update_data, $user_id)
	{
		$user_id = ( int ) $user_id;
		
		if (empty ( $update_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
		}
		
		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�û�ID����Ϊ��' );
		}
		
		$user_obj = POCO::singleton ( 'pai_user_class' );
		$pic_obj = POCO::singleton ( 'pai_pic_class' );
		$user_icon_obj = POCO::singleton ( 'pai_user_icon_class' );
		
		//��֤����ʱ�Ѳ����û�ID
		//$insert_data ['user_id'] = $user_id;
		//$this->add_cameraman_card ( $insert_data );
		
		$where_str = "user_id = {$user_id}";
		
		if($update_data ['intro'])
		{
			$cameraman_card_info = $this->get_cameraman_card_info($user_id);
			//�����
			$this->audit_text($user_id,'cameraman_card_remark',$cameraman_card_info['intro'],$update_data ['intro']);
		}

		$cameraman_update_data ['intro'] = $update_data ['intro'];
		
		
		$cameraman_update_data ['cover_img'] = $update_data ['cover_img'];
		
		
		if($cameraman_update_data)
		{
			$this->update ( $cameraman_update_data, $where_str );
		}
		
		//�û�����
		if ($update_data ['nickname'])
		{
			$nickname = $user_obj->get_user_nickname_by_user_id($user_id);
			//�����
			$this->audit_text($user_id,'nickname_text',$nickname,$update_data ['nickname']);
			
			$user_update_data ['nickname'] = $update_data ['nickname'];
		}
		
		if ($update_data ['location_id'])
		{
			$user_update_data ['location_id'] = $update_data ['location_id'];
		}
		
		if($user_update_data)
		{
			$user_obj->update_user ( $user_update_data, $user_id );
		}
		
		//ͼƬ��
		if ($update_data ['pic_arr'])
		{
			$pic_arr_update_data ['pic_arr'] = $update_data ['pic_arr'];
			$pic_obj->add_pic ( $user_id, $pic_arr_update_data ['pic_arr'] );
		} else
		{
			$pic_obj->del_pic ( $user_id );
		}
		
		/*
		 * ����ͷ��
		 */
		if ($update_data ['user_icon'])
		{
			$icon_update_data['icon_url'] = $update_data ['user_icon'];
			$user_icon_obj->replace_user_icon($icon_update_data);
		}
		
		return true;
	
	}
	
	/*
	 * ��ȡ��Ӱʦ����
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_cameraman_card_list($b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $limit = '0,10', $fields = '*')
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
	
	public function get_cameraman_card_info($user_id)
	{
		
		$user_id = ( int ) $user_id;
		$ret = $this->find ( "user_id={$user_id}" );
		$ret['intro'] = htmlspecialchars_decode($ret['intro']);
		return $ret;
	
	}
	
	/*
	 * �����û�ID��ȡ��Ӱʦ������
	 * @param int $user_id
	 * return array
	 */
	public function get_cameraman_card_by_user_id($user_id)
	{
		$user_id = ( int ) $user_id;
		
		$user_obj = POCO::singleton ( 'pai_user_class' );
		$pic_obj = POCO::singleton ( 'pai_pic_class' );
		$comment_score_rank_obj = POCO::singleton ( 'pai_comment_score_rank_class' );
		$user_follow_obj = POCO::singleton ( 'pai_user_follow_class' );
		$score_rank_obj = POCO::singleton ( 'pai_score_rank_class' );
		$date_rank_obj = POCO::singleton ( 'pai_date_rank_class' );
		$user_level_obj = POCO::singleton ( 'pai_user_level_class' );
		
		$ret = $this->get_cameraman_card_info ( $user_id );

		$user_info = $user_obj->get_user_info($user_id);
		unset($user_info['pwd_hash']);
		
		$ret = array_merge($ret, $user_info);
		
		$ret ['city_name'] = get_poco_location_name_by_location_id ( $ret ['location_id'] );
		
		$pic_arr = $pic_obj->get_user_pic ( $user_id, '0,15', 'img' );
		$ret['pic_arr'] = $pic_arr;
		
		//����ƽ����
		$comment_score = $comment_score_rank_obj->get_comment_score_rank ( $user_id );
		//û�з�����Ĭ��Ϊ3
		$comment_score = $comment_score ? $comment_score : 3;
		$ret ['score'] = round($comment_score * 2);
		
		//���մ���
		$ret ['take_photo_times'] = $date_rank_obj->count_model_take_photo_times ( $user_id );
		
		//��Ӱʦ�ȼ�
		$ret ['user_level'] = $user_level_obj->get_user_level($user_id);
		
		//��˿ ��ע��
		$ret ['fans'] = $user_follow_obj->get_user_be_follow_by_user_id ( $user_id, true );
		$ret ['follow'] = $user_follow_obj->get_user_follow_by_user_id ( $user_id, true );
		
		//�����İ�
		$ret ['share_text'] = $this->get_share_text($user_id);
		
		return $ret;
	}
	
	
	/*
	 * �������
	 */
	public function audit_text($user_id,$type='',$before='',$after='')
	{
		$data['user_id'] = $user_id;
		$data['type'] = $type;
		$data['before_edit'] = $before;
		$data['after_edit'] = $after;
		$data['add_time'] = date("Y-m-d H:i:s");
		
		$insert_str = db_arr_to_update_str($data);
		$sql = "replace into pai_log_db.text_examine_log set ".$insert_str;
 
		return $this->findBySql($sql);
		
	}
	
	
	/*
	 * ��Ӱʦ�������İ�
	 */
	public function get_share_text($user_id)
	{
		$user_id = (int)$user_id;
		$pai_user_obj = POCO::singleton ( 'pai_user_class' );
		$user_icon_obj = POCO::singleton ( 'pai_user_icon_class' );
	
		$nickname = $pai_user_obj->get_user_nickname_by_user_id ( $user_id );
		
		$title = '������Ӱʦ��'.$nickname.'��';
		$content = '����ԼԼ����Լ��Ӱ���񣬸о������գ���Ȼ�����Ч����Ӱ����ƽ̨��С����������һ����~';
		$weixin_title = "��ԼԼ�� 100000+ģ������Լ";
		$weixin_content = "��ģ����Լ��һ�ƶ�ƽ̨��������������������ķ羰";
		$share_url = 'http://www.yueus.com/cameraman/'.$user_id;
		$share_img = $user_icon_obj->get_user_icon ( $user_id, 468 );
		
		$share_text['title'] = $title;
		$share_text['content'] = $content;
		$share_text['weixin_title'] = $weixin_title;
		$share_text['weixin_content'] = $weixin_content;
		$share_text['sina_content'] = $title.' '.$share_url;
		$share_text['remark'] = '';
		$share_text['url'] = $share_url;
		$share_text['img'] = $share_img;
		$share_text['user_id'] = $user_id;
		$share_text['qrcodeurl'] = $share_url;
		
		return $share_text;
	}

}

?>
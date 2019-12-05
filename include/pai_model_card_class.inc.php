<?php
/*
 * ģ�ؿ�������
 */
class pai_model_card_class extends POCO_TDG
{
	private $cache_key = "YUEYUE_INTERFACE_MODELCARD4__";
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_model_card_tbl' );
	}
	
	/*
	 * ����ģ�ؿ�����
	 * 
	 * 
	 * return bool 
	 */
	public function add_model_card($insert_data)
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
	 * ����ģ�ؿ�����
	 * 
	 * @param array $update_data
	 * @param int   $user_id  
	 * 
	 * ������
	 * $update_data ['chest'] ��Χ
	 * $update_data ['cup'] �ֱ� A B C D E+
	 * $update_data ['waist'] ��Χ
	 * $update_data ['hip'] ��Χ
	 * $update_data ['height'] ���
	 * $update_data ['weight'] ����
	 * $update_data ['cameraman_require'] ���ý�Ҫ��
	 * 
	 * $update_data ['nickname'] �ǳ�
	 * $update_data ['sex'] �Ա�
	 * $update_data ['birthday'] ����
	 * $update_data ['location_id'] ����
	 * 
	 * $update_data ['pic_arr'] ͼƬ����
	 * 
	 * $update_data ['model_type_arr'] ģ����������
	 * 
	 * $update_data ['model_style_arr'] ģ�ط������
	 * $update_data ['model_price_arr'] ģ�ط���Ӧ�ļ۸�����
	 */
	public function update_model_card($update_data, $user_id)
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
		$model_style_obj = POCO::singleton ( 'pai_model_style_class' );
		$model_style_v2_obj = POCO::singleton ( 'pai_model_style_v2_class' );
		$user_icon_obj = POCO::singleton ( 'pai_user_icon_class' );
		$score_obj = POCO::singleton ( 'pai_score_class' );
		$fulltext_obj = POCO::singleton ( 'pai_fulltext_class' );
		
		//��֤����ʱ�Ѳ����û�ID
		$insert_data ['user_id'] = $user_id;
		$this->add_model_card ( $insert_data );
		
		//����ģ�ؿ���
		if ($update_data ['chest'])
		{
			$model_card_update_data ['chest'] = $update_data ['chest'];
		}
		if ($update_data ['chest_inch'])
		{
			$chest = $update_data ['chest'];
			
			if($update_data ['chest_inch']>40)
			{
				//��ʱ�����ү��BUG
				if ($chest < 80)
				{
					$inch = 30;
				}
				elseif ($chest >= 80 && $chest < 85)
				{
					$inch = 32;
				}
				elseif ($chest >= 85 && $chest < 90)
				{
					$inch = 34;
				}
				elseif ($chest >= 90 && $chest < 95)
				{
					$inch = 36;
				}
				elseif ($chest >= 95)
				{
					$inch = 38;
				}
			}
			else
			{
				$inch = $update_data ['chest_inch'];
			}
			
			$model_card_update_data ['chest_inch'] = $inch;
		}
		if ($update_data ['cup'])
		{
			$model_card_update_data ['cup'] = $update_data ['cup'];
		}
		if ($update_data ['waist'])
		{
			$model_card_update_data ['waist'] = $update_data ['waist'];
		}
		if ($update_data ['hip'])
		{
			$model_card_update_data ['hip'] = $update_data ['hip'];
		}
		if ($update_data ['height'])
		{
			$model_card_update_data ['height'] = $update_data ['height'];
		}
		if ($update_data ['weight'])
		{
			$model_card_update_data ['weight'] = $update_data ['weight'];
		}
		if ($update_data ['cameraman_require'])
		{
			$model_card_update_data ['cameraman_require'] = $update_data ['cameraman_require'];
		}
		if ($update_data ['level_require'])
		{
			$model_card_update_data ['level_require'] = $update_data ['level_require'];
		}
		if ($update_data ['limit_num'])
		{
			$model_card_update_data ['limit_num'] = $update_data ['limit_num'];
		}
		

		$model_card_update_data ['cover_img'] = $update_data ['cover_img'];
		
		 
		if($update_data ['intro'])
		{
			$model_card_info = $this->get_model_card_info($user_id);
			//�����
			$this->audit_text($user_id,'model_card_remark',$model_card_info['intro'],$update_data ['intro']);
		}

		$model_card_update_data ['intro'] = $update_data ['intro'];
		
		
		//�����û�����
		if (isset ( $update_data ['nickname'] ))
		{
			$nickname = $user_obj->get_user_nickname_by_user_id($user_id);
			//�����
			$this->audit_text($user_id,'nickname_text',$nickname,$update_data ['nickname']);
			$user_update_data ['nickname'] = $update_data ['nickname'];
		}
		if ($update_data ['sex'])
		{
			$user_update_data ['sex'] = $update_data ['sex'];
		}
		if ($update_data ['birthday'])
		{
			$user_update_data ['birthday'] = $update_data ['birthday'];
		}
		if ($update_data ['location_id'])
		{
			$user_update_data ['location_id'] = $update_data ['location_id'];
		}
		if ($user_update_data)
		{
			$user_obj->update_user ( $user_update_data, $user_id );
		}
		
		//����ͼƬ��
		if ($update_data ['pic_arr'])
		{
			$pic_arr_update_data ['pic_arr'] = $update_data ['pic_arr'];
			$pic_obj->add_pic ( $user_id, $pic_arr_update_data ['pic_arr'] );
		}
		
		if ($model_card_update_data)
		{
			$where_str = "user_id = {$user_id}";
			$this->update ( $model_card_update_data, $where_str );
		}
		
		//����ģ�ط���
		if ($update_data ['model_style_arr'] && $update_data ['model_price_arr'])
		{
			$model_style_arr_update_data ['model_style_arr'] = $update_data ['model_style_arr'];
			$model_style_arr_update_data ['model_price_arr'] = $update_data ['model_price_arr'];
			$model_style_obj->add_model_style ( $user_id, $model_style_arr_update_data ['model_style_arr'], $model_style_arr_update_data ['model_price_arr'] );
		}
		
		//����ģ���°���
		if ($update_data ['new_model_style_arr'])
		{
			$model_style_v2_arr_update_data ['new_model_style_arr'] = $update_data ['new_model_style_arr'];
			$model_style_v2_obj->add_model_style ( $user_id, $model_style_v2_arr_update_data ['new_model_style_arr'] );
		}
		
		
		$chat_user_obj = POCO::singleton('pai_chat_user_info');
		$chat_user_obj->redis_get_user_info($user_id);
		
		$fulltext_obj->add_fulltext_act ( $user_id );
		
		$score_obj->add_operate_queue ( $user_id, "update_model", $operate_num = 1, $remark = '' );
		
		$cache_key = $this->get_model_card_cache_key ( $user_id );
		POCO::deleteCache ( $cache_key ); //�建��
		

		return true;
	}
	
	/*
	 * ��ȡģ�ؿ�����
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_model_card_list($b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $limit = '0,10', $fields = '*')
	{
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
	
	public function get_model_card_info($user_id)
	{
		$user_id = ( int ) $user_id;
		$ret = $this->find ( "user_id={$user_id}" );
		$ret['intro'] = htmlspecialchars_decode($ret['intro']);
		return $ret;
	}
	
	/*
	 * ��ȡģ�ض���Ӱʦ�ĵȼ�Ҫ��
	 */
	public function get_model_level_require($user_id)
	{
		$user_id = ( int ) $user_id;
		$ret = $this->find ( "user_id={$user_id}", "", "level_require" );
		return $ret ['level_require'];
	}
	
	/*
	 * �����û�ID��ȡģ�ؿ�����
	 * @param int $user_id
	 * return array
	 */
	public function get_model_card_by_user_id($user_id, $pic_num = 15)
	{
		$user_id = ( int ) $user_id;
		$pic_num = ( int ) $pic_num;
		
		$user_obj = POCO::singleton ( 'pai_user_class' );
		$pic_obj = POCO::singleton ( 'pai_pic_class' );
		$model_type_obj = POCO::singleton ( 'pai_model_type_class' );
		$model_style_obj = POCO::singleton ( 'pai_model_style_class' );
		$model_style_v2_obj = POCO::singleton ( 'pai_model_style_v2_class' );
		$user_follow_obj = POCO::singleton ( 'pai_user_follow_class' );
		$score_rank_obj = POCO::singleton ( 'pai_score_rank_class' );
		$date_rank_obj = POCO::singleton ( 'pai_date_rank_class' );
		$comment_score_rank_obj = POCO::singleton ( 'pai_comment_score_rank_class' );
		
		//ȡCACHE����
		$cache_key = $this->get_model_card_cache_key ( $user_id );
		$model_info = POCO::getCache ( $cache_key );
		
		if (! $model_info)
		{
			
			$limit_pic = '0,' . $pic_num;
			
			$model_info = $this->get_model_card_info ( $user_id );
			
			$user_info = $user_obj->get_user_info ( $user_id );
			
			$chest = $model_info ['chest'];
			$cup = $model_info ['cup'];
			
			$model_info ['cup'] = $chest . $cup;
			$model_info ['cup_word'] = $cup;
			$model_info ['cup_v2'] = $model_info ['chest_inch'] . $cup;
			$model_info ['sex'] = $user_info ['sex'];
			$model_info ['attendance'] = $user_info ['attendance'];
			$model_info ['location_id'] = $user_info ['location_id'];
			$model_info ['nickname'] = get_user_nickname_by_user_id ( $user_id );
			
			$model_info ['city_name'] = get_poco_location_name_by_location_id ( $user_info ['location_id'] );
			
			$model_info ['user_name'] = $model_info ['nickname'];
			
			
			//��ģ�ط��
			$model_info ['model_style'] = $model_style_obj->get_model_style_by_user_id ( $user_id, 'style,price' );
			
			
			$model_info ['model_style_combo'] = $model_style_v2_obj->get_model_style_combo ( $user_id );
			
			//û����ʱ��Ĭ��
			if (! $model_info ['model_style_combo'])
			{
				$default_style_arr ['main'] [0] ['hour'] = 2;
				$default_style_arr ['main'] [0] ['price'] = "";
				$default_style_arr ['main'] [0] ['style'] = "";
				$default_style_arr ['main'] [0] ['continue_price'] = "";
				$model_info ['model_style_combo'] = $default_style_arr;
			}
			
			if ($model_info ['level_require'] == 1)
			{
				$model_info ['level_require_text'] = "V1�ֻ���֤����Լ��";
			}
			elseif ($model_info ['level_require'] == 2)
			{
				$model_info ['level_require_text'] = "V2ʵ����֤����Լ��";
			}
			elseif ($model_info ['level_require'] == 3)
			{
				$model_info ['level_require_text'] = "V3������֤����Լ��";
			}
			
			
			
			$cache_time = 3600 * 24 * 10;
			POCO::setCache ( $cache_key, $model_info, array ('life_time' => $cache_time ) );
		}
		
		//��ģ��ͼƬ
		$model_info ['model_pic'] = $pic_obj->get_user_pic ( $user_id, $limit_pic, 'img' );
		
		//����ƽ����
		$comment_score = $comment_score_rank_obj->get_comment_score_rank ( $user_id );
		//û�з�����Ĭ��Ϊ3
		$comment_score = $comment_score ? $comment_score : 3;
		$model_info ['score'] = $comment_score * 2;
		
		// ������������
		$comment_has_star = intval ( round ( $comment_score ) );
		$comment_miss_star = 5 - $comment_has_star;
		for($i = 0; $i < 5; $i ++)
		{
			if ($comment_has_star > 0)
			{
				$model_info ['comment_stars_list'] [$i] ['is_red'] = 1;
				$comment_has_star --;
			}
			else
			{
				$model_info ['comment_stars_list'] [$i] ['is_red'] = 0;
				$comment_miss_star --;
			}
		}
		
		
		$model_info ['model_style_v2'] = $model_style_v2_obj->get_model_style_by_user_id ( $user_id );
			
		if (! $model_info ['model_style_v2'])
		{
			$model_info ['model_style_v2'] = array ();
		}
		
		//ģ�����մ���
		$model_info ['take_photo_times'] = $date_rank_obj->count_model_take_photo_times ( $user_id );
		
		//��˿��
		$model_info ['be_follow_count'] = $user_follow_obj->get_user_be_follow_by_user_id ( $user_id, true );
		
		//����ֵȼ�
		$score_arr = $score_rank_obj->get_score_rank ( $user_id );
		
		//�ȼ�
		$model_info ['level'] = ( int ) $score_arr ['level'];
		
		//����
		$model_info ['jifen'] = ( int ) $score_arr ['score'];
		
		//ģ�ؿ������İ�
		$model_info ['share_text'] = $this->get_share_text($user_id);
		
		return $model_info;
	}
	
	/*
	 * �����Ӱʦ�Ƿ�ﵽģ��Ҫ��
	 * @param int $cameraman_user_id
	 * @param int $model_user_id
	 * 
	 * return bool
	 */
	public function check_cameraman_require($cameraman_user_id, $model_user_id)
	{
		if (empty ( $cameraman_user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':��ӰʦID����Ϊ��' );
		}
		if (empty ( $model_user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ģ��ID����Ϊ��' );
		}
		$pai_payment_obj = POCO::singleton ( 'pai_payment_class' );
		
		$account_info = $pai_payment_obj->get_bail_account_info ( $cameraman_user_id );
		
		$available_balance = $account_info ['available_balance'];
		
		$model_info = $this->get_model_card_info ( $model_user_id );
		
		$cameraman_require = ( int ) $model_info ['cameraman_require'];
		
		if ($available_balance >= $cameraman_require)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/*
	 * �����Ӱʦ���õȼ��Ƿ�ﵽģ��Ҫ��
	 * @param int $cameraman_user_id
	 * @param int $model_user_id
	 * 
	 * return bool
	 */
	public function check_cameraman_level_require($cameraman_user_id, $model_user_id, $type = '')
	{
		$level_obj = POCO::singleton ( 'pai_user_level_class' );
		$pai_payment_obj = POCO::singleton ( 'pai_payment_class' );
		$id_obj = POCO::singleton ( 'pai_id_class' );
		$id_audit_obj = POCO::singleton ( 'pai_id_audit_class' );
		
		if ($type == 'chat')
		{
			$date_obj = POCO::singleton ( 'event_date_class' );
			$check_date = $date_obj->check_cameraman_is_date ( $cameraman_user_id, $model_user_id );
			if ($check_date)
			{
				return true;
			}
		}
		
		//���֤��Ϣ
		$id_info = $id_obj->get_id_info ( $cameraman_user_id );
		
		//�����Ϣ
		$id_audit_info = $id_audit_obj->get_audit_info ( $cameraman_user_id );
		
		if ($id_audit_info && $id_audit_info ['status'] == 0)
		{
			$auditing = 1;
		}
		
		//���ý�
		$available_balance = $pai_payment_obj->get_bail_available_balance ( $cameraman_user_id );
		
		if ($available_balance >= $level_obj->price && ($id_info || $auditing))
		{
			$level = 3;
		}
		elseif ($id_info || $auditing)
		{
			$level = 2;
		}
		else
		{
			$level = 1;
		}
		
		$model_info = $this->get_model_card_info ( $model_user_id );
		
		$level_require = ( int ) $model_info ['level_require'];
		
		if ($level >= $level_require)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/*
	 * �����Ӱʦ���õȼ��Ƿ�ﵽģ��Ҫ��(�����΢��)��Ӱʦ�ȼ�Ϊ1 ��ģ��Ҫ��Ϊ2����������
	 * @param int $cameraman_user_id
	 * @param int $model_user_id
	 * 
	 * return bool
	 */
	public function check_cameraman_level_require_for_weixin($cameraman_user_id, $model_user_id)
	{
		$level_obj = POCO::singleton ( 'pai_user_level_class' );
		$pai_payment_obj = POCO::singleton ( 'pai_payment_class' );
		$id_obj = POCO::singleton ( 'pai_id_class' );
		$id_audit_obj = POCO::singleton ( 'pai_id_audit_class' );
		
		//���֤��Ϣ
		$id_info = $id_obj->get_id_info ( $cameraman_user_id );
		
		//�����Ϣ
		$id_audit_info = $id_audit_obj->get_audit_info ( $cameraman_user_id );
		
		if ($id_audit_info && $id_audit_info ['status'] == 0)
		{
			$auditing = 1;
		}
		
		//���ý�
		$available_balance = $pai_payment_obj->get_bail_available_balance ( $cameraman_user_id );
		
		if ($available_balance >= $level_obj->price && ($id_info || $auditing))
		{
			$level = 3;
		}
		elseif ($id_info || $auditing)
		{
			$level = 2;
		}
		else
		{
			$level = 2;
		}
		
		$model_info = $this->get_model_card_info ( $model_user_id );
		
		$level_require = ( int ) $model_info ['level_require'];
		
		if ($level >= $level_require)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/*
	 * ���ģ������������Ƿ�����
	 */
	public function check_input_is_complete($user_id)
	{
		$user_id = ( int ) $user_id;
		
		$model_style_v2_obj = POCO::singleton ( 'pai_model_style_v2_class' );
		
		$model_style = $model_style_v2_obj->get_model_style_by_user_id ( $user_id );
		
		$model_info = $this->get_model_card_info ( $user_id );
		
		if ($model_style && $model_info ['chest'] && $model_info ['waist'] && $model_info ['hip'] && $model_info ['height'] && $model_info ['weight'])
		{
			return true;
		}
		else
		{
			return false;
		}
	
	}
	
	/*
	 * ģ�ؿ������İ�
	 */
	public function get_share_text($user_id)
	{
		$user_id = (int)$user_id;
		$pai_user_obj = POCO::singleton ( 'pai_user_class' );
		$user_icon_obj = POCO::singleton ( 'pai_user_icon_class' );
	
		$nickname = $pai_user_obj->get_user_nickname_by_user_id ( $user_id );
		
		$title = '����ԼŮ��'.$nickname.'��������˵�߾��ߵ�Լ�ģ���ԼԼ�ɣ�';
		$content = '��ԼԼ��100000+ģ������Լ��';
		$sina_content = '����ԼŮ��'.$nickname.'��������˵�߾��ߵ�Լ�ģ���ԼԼ�ɣ�';
		$share_url = 'http://www.yueus.com/'.$user_id;
		$share_img = $user_icon_obj->get_user_icon ( $user_id, 468 );
		
		$share_text['title'] = $title;
		$share_text['content'] = $content;
		$share_text['sina_content'] = $sina_content.' '.$share_url;
		$share_text['remark'] = '';
		$share_text['url'] = $share_url;
		$share_text['img'] = $share_img;
		$share_text['user_id'] = $user_id;
		$share_text['qrcodeurl'] = 'http://www.yueus.com/share_card/' . $user_id;
		
		return $share_text;
	}
	
	
/*	public function get_share_text_v2($user_id)
	{
		$user_id = (int)$user_id;
		$pai_user_obj = POCO::singleton ( 'pai_user_class' );
		$user_icon_obj = POCO::singleton ( 'pai_user_icon_class' );
	
		$nickname = $pai_user_obj->get_user_nickname_by_user_id ( $user_id );
		
		$title = 'ԼŮ��'.$nickname;
		$content = 'ԼŮ��'.$nickname.'����������Ů��ô����ԼԼ��100000+ģ������Լ';
		$share_url = 'http://www.yueus.com/'.$user_id;
		$share_img = $user_icon_obj->get_user_icon ( $user_id, 468 );
		
		$share_text['weixin']['title'] = $title;
		$share_text['weixin']['content'] = $content;
		$share_text['weixin']['remark'] = '';
		$share_text['weixin']['url'] = $share_url;
		$share_text['weixin']['img'] = $share_img;
		
		$share_text['weixin_circle']['title'] = $title;
		$share_text['weixin_circle']['content'] = $content;
		$share_text['weixin_circle']['remark'] = '';
		$share_text['weixin_circle']['url'] = $share_url;
		$share_text['weixin_circle']['img'] = $share_img;
		
		$share_text['qq_zone']['title'] = $title;
		$share_text['qq_zone']['content'] = $content;
		$share_text['qq_zone']['remark'] = '';
		$share_text['qq_zone']['url'] = $share_url;
		$share_text['qq_zone']['img'] = $share_img;
		
		$share_text['qq']['title'] = $title;
		$share_text['qq']['content'] = $content;
		$share_text['qq']['remark'] = '';
		$share_text['qq']['url'] = $share_url;
		$share_text['qq']['img'] = $share_img;
		
		$share_text['sina_weibo']['title'] = $title;
		$share_text['sina_weibo']['content'] = $content;
		$share_text['sina_weibo']['remark'] = '';
		$share_text['sina_weibo']['url'] = $share_url;
		$share_text['sina_weibo']['img'] = $share_img;
		
		return $share_text;
	}*/
	
	private function get_model_card_cache_key($user_id)
	{
		return $this->cache_key . $user_id;
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
}
?>
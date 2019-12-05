<?php
/*
 * 模特卡操作类
 */
class pai_model_card_class extends POCO_TDG
{
	private $cache_key = "YUEYUE_INTERFACE_MODELCARD4__";
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_model_card_tbl' );
	}
	
	/*
	 * 插入模特卡数据
	 * 
	 * 
	 * return bool 
	 */
	public function add_model_card($insert_data)
	{
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
		}
		
		$insert_data ['user_id'] = ( int ) $insert_data ['user_id'];
		
		if (empty ( $insert_data ['user_id'] ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':用户ID不能为空' );
		}
		return $this->insert ( $insert_data, "IGNORE" );
	}
	
	/*
	 * 更新模特卡数据
	 * 
	 * @param array $update_data
	 * @param int   $user_id  
	 * 
	 * 参数：
	 * $update_data ['chest'] 胸围
	 * $update_data ['cup'] 罩杯 A B C D E+
	 * $update_data ['waist'] 腰围
	 * $update_data ['hip'] 臀围
	 * $update_data ['height'] 身高
	 * $update_data ['weight'] 体重
	 * $update_data ['cameraman_require'] 信用金要求
	 * 
	 * $update_data ['nickname'] 昵称
	 * $update_data ['sex'] 性别
	 * $update_data ['birthday'] 生日
	 * $update_data ['location_id'] 城市
	 * 
	 * $update_data ['pic_arr'] 图片数组
	 * 
	 * $update_data ['model_type_arr'] 模特类型数组
	 * 
	 * $update_data ['model_style_arr'] 模特风格数组
	 * $update_data ['model_price_arr'] 模特风格对应的价格数组
	 */
	public function update_model_card($update_data, $user_id)
	{
		$user_id = ( int ) $user_id;
		
		if (empty ( $update_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
		}
		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':用户ID不能为空' );
		}
		
		$user_obj = POCO::singleton ( 'pai_user_class' );
		$pic_obj = POCO::singleton ( 'pai_pic_class' );
		$model_style_obj = POCO::singleton ( 'pai_model_style_class' );
		$model_style_v2_obj = POCO::singleton ( 'pai_model_style_v2_class' );
		$user_icon_obj = POCO::singleton ( 'pai_user_icon_class' );
		$score_obj = POCO::singleton ( 'pai_score_class' );
		$fulltext_obj = POCO::singleton ( 'pai_fulltext_class' );
		
		//保证更新时已插入用户ID
		$insert_data ['user_id'] = $user_id;
		$this->add_model_card ( $insert_data );
		
		//更新模特卡表
		if ($update_data ['chest'])
		{
			$model_card_update_data ['chest'] = $update_data ['chest'];
		}
		if ($update_data ['chest_inch'])
		{
			$chest = $update_data ['chest'];
			
			if($update_data ['chest_inch']>40)
			{
				//临时解决鼎爷的BUG
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
			//加审核
			$this->audit_text($user_id,'model_card_remark',$model_card_info['intro'],$update_data ['intro']);
		}

		$model_card_update_data ['intro'] = $update_data ['intro'];
		
		
		//更新用户主表
		if (isset ( $update_data ['nickname'] ))
		{
			$nickname = $user_obj->get_user_nickname_by_user_id($user_id);
			//加审核
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
		
		//更新图片表
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
		
		//更新模特风格表
		if ($update_data ['model_style_arr'] && $update_data ['model_price_arr'])
		{
			$model_style_arr_update_data ['model_style_arr'] = $update_data ['model_style_arr'];
			$model_style_arr_update_data ['model_price_arr'] = $update_data ['model_price_arr'];
			$model_style_obj->add_model_style ( $user_id, $model_style_arr_update_data ['model_style_arr'], $model_style_arr_update_data ['model_price_arr'] );
		}
		
		//更新模特新版风格
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
		POCO::deleteCache ( $cache_key ); //清缓存
		

		return true;
	}
	
	/*
	 * 获取模特卡数据
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
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
	 * 获取模特对摄影师的等级要求
	 */
	public function get_model_level_require($user_id)
	{
		$user_id = ( int ) $user_id;
		$ret = $this->find ( "user_id={$user_id}", "", "level_require" );
		return $ret ['level_require'];
	}
	
	/*
	 * 根据用户ID获取模特卡数据
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
		
		//取CACHE数据
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
			
			
			//查模特风格
			$model_info ['model_style'] = $model_style_obj->get_model_style_by_user_id ( $user_id, 'style,price' );
			
			
			$model_info ['model_style_combo'] = $model_style_v2_obj->get_model_style_combo ( $user_id );
			
			//没数据时出默认
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
				$model_info ['level_require_text'] = "V1手机认证才能约拍";
			}
			elseif ($model_info ['level_require'] == 2)
			{
				$model_info ['level_require_text'] = "V2实名认证才能约拍";
			}
			elseif ($model_info ['level_require'] == 3)
			{
				$model_info ['level_require_text'] = "V3达人认证才能约拍";
			}
			
			
			
			$cache_time = 3600 * 24 * 10;
			POCO::setCache ( $cache_key, $model_info, array ('life_time' => $cache_time ) );
		}
		
		//查模特图片
		$model_info ['model_pic'] = $pic_obj->get_user_pic ( $user_id, $limit_pic, 'img' );
		
		//评价平均分
		$comment_score = $comment_score_rank_obj->get_comment_score_rank ( $user_id );
		//没有分数的默认为3
		$comment_score = $comment_score ? $comment_score : 3;
		$model_info ['score'] = $comment_score * 2;
		
		// 评价评分星星
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
		
		//模特拍照次数
		$model_info ['take_photo_times'] = $date_rank_obj->count_model_take_photo_times ( $user_id );
		
		//粉丝数
		$model_info ['be_follow_count'] = $user_follow_obj->get_user_be_follow_by_user_id ( $user_id, true );
		
		//查积分等级
		$score_arr = $score_rank_obj->get_score_rank ( $user_id );
		
		//等级
		$model_info ['level'] = ( int ) $score_arr ['level'];
		
		//积分
		$model_info ['jifen'] = ( int ) $score_arr ['score'];
		
		//模特卡分享文案
		$model_info ['share_text'] = $this->get_share_text($user_id);
		
		return $model_info;
	}
	
	/*
	 * 检查摄影师是否达到模特要求
	 * @param int $cameraman_user_id
	 * @param int $model_user_id
	 * 
	 * return bool
	 */
	public function check_cameraman_require($cameraman_user_id, $model_user_id)
	{
		if (empty ( $cameraman_user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':摄影师ID不能为空' );
		}
		if (empty ( $model_user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':模特ID不能为空' );
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
	 * 检查摄影师信用等级是否达到模特要求
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
		
		//身份证信息
		$id_info = $id_obj->get_id_info ( $cameraman_user_id );
		
		//审核信息
		$id_audit_info = $id_audit_obj->get_audit_info ( $cameraman_user_id );
		
		if ($id_audit_info && $id_audit_info ['status'] == 0)
		{
			$auditing = 1;
		}
		
		//信用金
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
	 * 检查摄影师信用等级是否达到模特要求(特殊给微信)摄影师等级为1 ，模特要求为2，都给他过
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
		
		//身份证信息
		$id_info = $id_obj->get_id_info ( $cameraman_user_id );
		
		//审核信息
		$id_audit_info = $id_audit_obj->get_audit_info ( $cameraman_user_id );
		
		if ($id_audit_info && $id_audit_info ['status'] == 0)
		{
			$auditing = 1;
		}
		
		//信用金
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
	 * 检查模特填入的资料是否完整
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
	 * 模特卡分享文案
	 */
	public function get_share_text($user_id)
	{
		$user_id = (int)$user_id;
		$pai_user_obj = POCO::singleton ( 'pai_user_class' );
		$user_icon_obj = POCO::singleton ( 'pai_user_icon_class' );
	
		$nickname = $pai_user_obj->get_user_nickname_by_user_id ( $user_id );
		
		$title = '我是约女神'.$nickname.'，想来场说走就走的约拍？来约约吧！';
		$content = '在约约，100000+模特随心约。';
		$sina_content = '我是约女神'.$nickname.'，想来场说走就走的约拍？来约约吧！';
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
		
		$title = '约女神'.$nickname;
		$content = '约女神'.$nickname.'我是你的灵感女神么？上约约，100000+模特随心约';
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
	 * 审核文字
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
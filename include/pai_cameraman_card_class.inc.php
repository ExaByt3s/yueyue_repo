<?php
/*
 * 摄影师卡操作类
 */

class pai_cameraman_card_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_cameraman_card_tbl' );
	}
	
	/*
	 * 插入摄影师数据
	 * 
	 * 
	 * return bool 
	 */
	public function add_cameraman_card($insert_data)
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
	 * 更新摄影师数据
	 * 
	 * @param array $update_data
	 * @param int   $user_id  
	 * 
	 * 参数：
	 * $update_data ['intro'] 简介
	 * $update_data ['honor'] 荣誉
	 * $update_data ['nickname'] 昵称
	 * $update_data['pic_arr'] 图片数组
	 */
	public function update_cameraman_card($update_data, $user_id)
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
		$user_icon_obj = POCO::singleton ( 'pai_user_icon_class' );
		
		//保证更新时已插入用户ID
		//$insert_data ['user_id'] = $user_id;
		//$this->add_cameraman_card ( $insert_data );
		
		$where_str = "user_id = {$user_id}";
		
		if($update_data ['intro'])
		{
			$cameraman_card_info = $this->get_cameraman_card_info($user_id);
			//加审核
			$this->audit_text($user_id,'cameraman_card_remark',$cameraman_card_info['intro'],$update_data ['intro']);
		}

		$cameraman_update_data ['intro'] = $update_data ['intro'];
		
		
		$cameraman_update_data ['cover_img'] = $update_data ['cover_img'];
		
		
		if($cameraman_update_data)
		{
			$this->update ( $cameraman_update_data, $where_str );
		}
		
		//用户主表
		if ($update_data ['nickname'])
		{
			$nickname = $user_obj->get_user_nickname_by_user_id($user_id);
			//加审核
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
		
		//图片表
		if ($update_data ['pic_arr'])
		{
			$pic_arr_update_data ['pic_arr'] = $update_data ['pic_arr'];
			$pic_obj->add_pic ( $user_id, $pic_arr_update_data ['pic_arr'] );
		} else
		{
			$pic_obj->del_pic ( $user_id );
		}
		
		/*
		 * 更换头像
		 */
		if ($update_data ['user_icon'])
		{
			$icon_update_data['icon_url'] = $update_data ['user_icon'];
			$user_icon_obj->replace_user_icon($icon_update_data);
		}
		
		return true;
	
	}
	
	/*
	 * 获取摄影师数据
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
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
	 * 根据用户ID获取摄影师卡数据
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
		
		//评价平均分
		$comment_score = $comment_score_rank_obj->get_comment_score_rank ( $user_id );
		//没有分数的默认为3
		$comment_score = $comment_score ? $comment_score : 3;
		$ret ['score'] = round($comment_score * 2);
		
		//拍照次数
		$ret ['take_photo_times'] = $date_rank_obj->count_model_take_photo_times ( $user_id );
		
		//摄影师等级
		$ret ['user_level'] = $user_level_obj->get_user_level($user_id);
		
		//粉丝 关注数
		$ret ['fans'] = $user_follow_obj->get_user_be_follow_by_user_id ( $user_id, true );
		$ret ['follow'] = $user_follow_obj->get_user_follow_by_user_id ( $user_id, true );
		
		//分享文案
		$ret ['share_text'] = $this->get_share_text($user_id);
		
		return $ret;
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
	
	
	/*
	 * 摄影师卡分享文案
	 */
	public function get_share_text($user_id)
	{
		$user_id = (int)$user_id;
		$pai_user_obj = POCO::singleton ( 'pai_user_class' );
		$user_icon_obj = POCO::singleton ( 'pai_user_icon_class' );
	
		$nickname = $pai_user_obj->get_user_nickname_by_user_id ( $user_id );
		
		$title = '我是摄影师【'.$nickname.'】';
		$content = '刚在约约尝试约摄影服务，感觉萌萌哒，果然是最高效的摄影服务平台，小伙伴快来和我一起玩~';
		$weixin_title = "【约约】 100000+模特随心约";
		$weixin_content = "来模特邀约第一移动平台，不经意的遇上最美丽的风景";
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
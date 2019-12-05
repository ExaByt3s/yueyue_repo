<?php
/*
 * ģ��������
 */

class pai_model_comment_log_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_model_comment_log_tbl' );
	}
	
	/*
	 * �������
	 * 
	 * @param int    $date_id    Լ��ID
	 * @param int    $cameraman_user_id ��Ӱʦ�û�ID
	 * @param int    $model_user_id     ģ���û�ID
	 * @param enum   $overall_score     ����  1-5
	 * @param enum   $expressive_score    ����  1-5
     * @param enum   $truth       ��ʵ����ʵ
     * @param enum   $time_sense      ׼ʱ��׼ʱ
	 * @param string $comment     ����
	 * @param int    $is_anonymous     �Ƿ���������
	 * 
	 * return int 
	 */
	public function add_comment($date_id, $cameraman_user_id, $model_user_id, $overall_score, $expressive_score, $truth, $time_sense, $comment, $is_anonymous = 0)
	{
		$date_id = ( int ) $date_id;
		$cameraman_user_id = ( int ) $cameraman_user_id;
		$model_user_id = ( int ) $model_user_id;
		
		if (empty ( $date_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':Լ��ID����Ϊ��' );
		}
		
		if (empty ( $cameraman_user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':��ӰʦID����Ϊ��' );
		}
		
		if (empty ( $model_user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ģ��ID����Ϊ��' );
		}
		
		$insert_data ['date_id'] = $date_id;
		$insert_data ['cameraman_user_id'] = $cameraman_user_id;
		$insert_data ['model_user_id'] = $model_user_id;
		$insert_data ['overall_score'] = $overall_score;
		$insert_data ['expressive_score'] = $expressive_score;
		$insert_data ['truth'] = $truth;
		$insert_data ['time_sense'] = $time_sense;
		$insert_data ['comment'] = str_replace("\u2006","",$comment);
		$insert_data ['is_anonymous'] = $is_anonymous;
		$insert_data ['add_time'] = time ();
		
		$ret = $this->insert ( $insert_data, 'IGNORE' );
		
		if ($ret)
		{
			$msg_obj = POCO::singleton ( 'pai_information_push' );
			
			$model_nickname = get_user_nickname_by_user_id ( $model_user_id );
			
			$send_data ['media_type'] = 'card';
			$send_data ['card_style'] = 2;
			$send_data ['card_text1'] = '������ģ��:' . $model_nickname;
			$send_data ['card_title'] = '�鿴Լ������';
			
			$to_send_data ['media_type'] = 'card';
			$to_send_data ['card_style'] = 2;
			$to_send_data ['card_text1'] = '��������';
			$to_send_data ['card_title'] = '�鿴Լ������';
			
			if (! defined ( 'YUE_OA_IMPORT_ORDER' ))
			{
				if ($is_anonymous)
				{
					$msg_obj->send_msg_data ( $cameraman_user_id, $model_user_id, $send_data, $to_send_data, $date_id, 0 );
				}
				else
				{
					$msg_obj->send_msg_data ( $cameraman_user_id, $model_user_id, $send_data, $to_send_data, $date_id,2 );
				}
			}
		}
		
		return $ret;
	}
	
	/*
	 * ��ȡ����
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_comment_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
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
	
	/*
	 * ��Ӱʦ�Ƿ�������Լ��
	 * @param int    $date_id              Լ��ID
	 * @param int    $cameraman_user_id    ��Ӱʦ�û�ID
	 * 
	 * return bool 
	 */
	public function is_comment_by_cameraman($date_id, $cameraman_user_id)
	{
		$date_id = ( int ) $date_id;
		$cameraman_user_id = ( int ) $cameraman_user_id;
		
		if (empty ( $date_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':Լ��ID����Ϊ��' );
		}
		
		if (empty ( $cameraman_user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':��Ӱʦ�û�ID����Ϊ��' );
		}
		
		$where_str = "date_id={$date_id} and cameraman_user_id={$cameraman_user_id}";
		
		$ret = $this->get_comment_list ( true, $where_str );
		
		if ($ret)
		{
			return true;
		}
		else
		{
			return false;
		}
	
	}
	
	/*
	 * ��ȡģ����������
	 * @param int    $model_user_id
	 * @param bool    $b_select_count
	 * @param string    $limit
	 */
	public function get_model_comment_list($model_user_id, $b_select_count = false, $limit = '0,10')
	{
		$model_user_id = ( int ) $model_user_id;
		$list = $this->get_comment_list ( $b_select_count, 'model_user_id=' . $model_user_id, 'id DESC', $limit, 'date_id,cameraman_user_id,overall_score,comment,add_time,is_anonymous' );
		foreach ( $list as $k => $val )
		{
			$list [$k] ['add_time'] = date ( "Y-m-d H:s", $val ['add_time'] );
			
			if ($val ['is_anonymous'] == 1)
			{
				$list [$k] ['nickname'] = '�����û�';
			}
			else
			{
				$list [$k] ['nickname'] = get_user_nickname_by_user_id ( $val ['cameraman_user_id'] );
			}
		
		}
		
		return $list;
	}
	
	/*
	 * ����Լ��ID��ȡģ����������
	 * @param int    $date_id
	 */
	public function get_model_comment_by_date_id($date_id)
	{
		$date_id = ( int ) $date_id;
		$ret = $this->find ( "date_id={$date_id}" );
		return $ret;
	}
	
	public function comment_log($date_id)
	{
		$date_id = ( int ) $date_id;
		
		global $yue_login_id;
		
		$cameraman_comment_obj = POCO::singleton ( 'pai_cameraman_comment_log_class' );
		
		$model_comment = $this->get_model_comment_by_date_id ( $date_id );
		$model_comment = array_merge ( $model_comment, array ("type" => "model_comment" ) );
		
		if ($model_comment ['id'] && $model_comment ['is_anonymous']==0)
		{
			$comment [] = $model_comment;
		}
		
		$cameraman_comment = $cameraman_comment_obj->get_cameraman_comment_by_date_id ( $date_id );
		$cameraman_comment = array_merge ( $cameraman_comment, array ("type" => "cameraman_comment" ) );
		
		if ($cameraman_comment ['id'] && $cameraman_comment ['is_anonymous']==0)
		{
			$comment [] = $cameraman_comment;
		}
		
		//ð������ ʱ������ǰ��
		for($i = 0; $i < count ( $comment ); $i ++)
		{
			for($j = 0; $j < $i; $j ++)
			{
				if ($comment [$i] ['add_time'] > $comment [$j] ['add_time'])
				{
					$temp = $comment [$i];
					$comment [$i] = $comment [$j];
					$comment [$j] = $temp;
				}
			}
		}
		
		foreach ( $comment as $k => $val )
		{
			if ($k == 0)
			{
				$comment [$k] ['high_light'] = 1;
			}
			else
			{
				$comment [$k] ['high_light'] = 0;
			}
			
			$comment [$k] ['add_time'] = date ( "Y-m-d H:i", $val ['add_time'] );
			
			if ($val ['type'] == 'model_comment')
			{
				if ($yue_login_id == $val ['cameraman_user_id'])
				{
					$nickname = get_user_nickname_by_user_id ( $val ['model_user_id'] );
					$text = "��������" . $nickname;
					$user_icon = get_user_icon ( $val ['cameraman_user_id'] );
				}
				else
				{
					$nickname = get_user_nickname_by_user_id ( $val ['cameraman_user_id'] );
					$text = $nickname . "��������";
					$user_icon = get_user_icon ( $val ['cameraman_user_id'] );
					
				}
			
			}
			elseif ($val ['type'] == 'cameraman_comment')
			{
				if ($yue_login_id == $val ['cameraman_user_id'])
				{
					$nickname = get_user_nickname_by_user_id ( $val ['model_user_id'] );
					$text = $nickname . "��������";
					$user_icon = get_user_icon ( $val ['model_user_id'] );
					
				}
				else
				{
					$nickname = get_user_nickname_by_user_id ( $val ['cameraman_user_id'] );
					$text = "��������" . $nickname;
					$user_icon = get_user_icon ( $val ['model_user_id'] );
				}
			} 
			
			$comment [$k] ['text'] = $text;
			$comment [$k] ['user_icon'] = $user_icon;
			
			// ����������������
			if($val ['overall_score'])
			{
				$overall_score_has_star = intval ( $val ['overall_score'] );
				$overall_score_miss_star = 5 - $overall_score_has_star;
				
				for($i = 0; $i < 5; $i ++)
				{
					if ($overall_score_has_star > 0)
					{
						$comment [$k] ['overall_score_stars_list'] [$i] ['is_red'] = 1;
						
						$overall_score_has_star --;
					}
					else
					{
						$comment [$k] ['overall_score_stars_list'] [$i] ['is_red'] = 0;
						
						$overall_score_miss_star --;
					}
				}
			}
			
			if ($val ['rp_score'])
			{
				// RP��������
				$rp_has_star = intval ( $val ['rp_score'] );
				$rp_miss_star = 5 - $rp_has_star;
				
				for($i = 0; $i < 5; $i ++)
				{
					if ($rp_has_star > 0)
					{
						$comment [$k] ['rp_stars_list'] [$i] ['is_red'] = 1;
						
						$rp_has_star --;
					}
					else
					{
						$comment [$k] ['rp_stars_list'] [$i] ['is_red'] = 0;
						
						$rp_miss_star --;
					}
				}
			}
			
			if ($val ['expressive_score'])
			{
				// ��������������
				$expressive_score_has_star = intval ( $val ['expressive_score'] );
				$expressive_score_miss_star = 5 - $expressive_score_has_star;
				
				for($i = 0; $i < 5; $i ++)
				{
					if ($expressive_score_has_star > 0)
					{
						$comment [$k] ['expressive_score_stars_list'] [$i] ['is_red'] = 1;
						
						$expressive_score_has_star --;
					}
					else
					{
						$comment [$k] ['expressive_score_stars_list'] [$i] ['is_red'] = 0;
						
						$expressive_score_miss_star --;
					}
				}
			}
		
		}
		
		return $comment;
	}
}

?>
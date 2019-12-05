<?php
/*
 * �û��Ի������
 */

class pai_event_comment_log_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_event_comment_log_tbl' );
	}
	
	/*
	 * �������
	 * 
	 * @param int    $event_id    �ID
	 * @param int    $table_id    ����ID
	 * @param int    $user_id �û�ID
	 * @param enum   $overall_score     ����  1-5
	 * @param enum   $organize_score    ����  1-5
     * @param enum   $model_score       ����  1-5
	 * @param string $comment     ����
	 * @param int    $is_anonymous     �Ƿ���������
	 * 
	 * return int 
	 */
	public function add_comment($event_id, $table_id, $user_id, $overall_score, $organize_score, $model_score, $comment, $is_anonymous = 0)
	{
		$event_id = ( int ) $event_id;
		$user_id = ( int ) $user_id;
		
		if (empty ( $event_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':Լ��ID����Ϊ��' );
		}
		
		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�û�ID����Ϊ��' );
		}
		
		$event_info = get_event_list ( "event_id={$event_id}" );
		
		$insert_data ['event_id'] = $event_id;
		$insert_data ['event_user_id'] = $event_info [0] ['user_id'];
		$insert_data ['table_id'] = $table_id;
		$insert_data ['user_id'] = $user_id;
		$insert_data ['overall_score'] = $overall_score;
		$insert_data ['organize_score'] = $organize_score;
		$insert_data ['model_score'] = $model_score;
		$insert_data ['comment'] = $comment;
		$insert_data ['is_anonymous'] = $is_anonymous;
		$insert_data ['add_time'] = time ();
		
		$ret = $this->insert ( $insert_data, 'IGNORE' );
		
		if ($ret)
		{
			
			$send_user_id = $event_info [0] ['user_id'];
			$title = $event_info [0] ['title'];
			
			$now_time = date ( "j��Gʱ" );
			$nickname = get_user_nickname_by_user_id($user_id);
			$content = $nickname.' �����Ļ��'.$title.'�������ˣ�����ȥ������';
			
			$to_url = "/mall/user/act/comment_list.php?event_id={$event_id}&type=event";
			send_message_for_10002 ( $send_user_id, $content, $to_url,'yuebuyer' );
		}
		
		return $ret;
	}
	
	/*
	 * ��ȡ�����
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
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}
	
	/*
	 * ��ȡ�û����ۻ�ķ���
	 * @param int    $event_id �ID
	 * @param int    $user_id  �û�ID
	 */
	public function get_event_comment_by_event_id_user_id($event_id, $user_id)
	{
		$event_id = ( int ) $event_id;
		$user_id = ( int ) $user_id;
		
		if (empty ( $event_id ))
		{
            return false;
		}
		
		if (empty ( $user_id ))
		{
            return false;
		}
		
		$where_str = "event_id={$event_id} and user_id={$user_id}";
		
		$ret = $this->get_comment_list ( false, $where_str );
		
		return $ret;
	
	}
	
	/*
	 * �û��Ƿ������
	 * @param int    $event_id �ID
	 * @param int    $table_id ����ID
	 * @param int    $user_id  �û�ID
	 * 
	 * return bool 
	 * 
	 */
	public function is_event_comment_by_user($event_id, $table_id, $user_id)
	{
		$event_id = ( int ) $event_id;
		$user_id = ( int ) $user_id;
		$table_id = ( int ) $table_id;
		
		if (empty ( $event_id ))
		{
			return false;
		}
		
		if (empty ( $table_id ))
		{
            return false;
		}
		
		if (empty ( $user_id ))
		{
            return false;
		}
		
		$where_str = "event_id={$event_id} and user_id={$user_id} and table_id={$table_id}";
		
		$ret = $this->get_comment_list ( true, $where_str );
		
		if ($ret)
		{
			return true;
		} else
		{
			return false;
		}
	
	}
	
	/*
	 * ��ȡ���������
	 * @param int    $event_id
	 * @param bool    $b_select_count
	 * @param string    $limit
	 */
	public function get_event_comment_list($event_id, $b_select_count = false, $limit = '0,10')
	{
		$event_id = ( int ) $event_id;
		$list = $this->get_comment_list ( $b_select_count, 'event_id=' . $event_id, 'id DESC', $limit, 'event_id,user_id,overall_score,comment,add_time,is_anonymous' );
		foreach ( $list as $k => $val )
		{
			$list [$k] ['add_time'] = date ( "Y-m-d H:i", $val ['add_time'] );
			if ($val ['is_anonymous'] == 1)
			{
				$list [$k] ['nickname'] = '�����û�';
			} else
			{
				$list [$k] ['nickname'] = get_user_nickname_by_user_id ($val ['user_id'] );
			}
		}
		
		return $list;
	}
	
	
	/*
	 * ��ȡ�û����л��������
	 * @param int    $event_id
	 * @param bool    $b_select_count
	 * @param string    $limit
	 */
	public function get_user_comment_list($user_id, $b_select_count = false, $limit = '0,10')
	{
		$user_id = ( int ) $user_id;
		$list = $this->get_comment_list ( $b_select_count, 'event_user_id=' . $user_id, 'add_time DESC', $limit, 'event_id,user_id,overall_score,comment,add_time,is_anonymous' );
		foreach ( $list as $k => $val )
		{
			$list [$k] ['add_time'] = date ( "Y-m-d H:i", $val ['add_time'] );
			if ($val ['is_anonymous'] == 1)
			{
				$list [$k] ['nickname'] = '�����û�';
			} else
			{
				$list [$k] ['nickname'] = get_user_nickname_by_user_id ($val ['user_id'] );
			}
            if($val['is_anonymous']==1)
            {
                $list [$k] ['user_icon'] = 'http://yue-icon.yueus.com/default_86.jpg';
            }
            else
            {
                $list [$k] ['user_icon'] = get_user_icon($val ['user_id']);
            }

		}
		
		return $list;
	}
}

?>
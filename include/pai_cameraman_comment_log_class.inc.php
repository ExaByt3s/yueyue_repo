<?php
/*
 * ��Ӱʦ������
 */

class pai_cameraman_comment_log_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_cameraman_comment_log_tbl' );
	}
	
	/*
	 * �������
	 * 
	 * @param int    $date_id    Լ��ID
	 * @param int    $model_user_id     ģ���û�ID
	 * @param int    $cameraman_user_id ��Ӱʦ�û�ID
	 * @param enum   $overall_score     ����  1-5
	 * @param enum   $rp_score    ����  1-5
     * @param enum   $time_sense       ׼ʱ��׼ʱ
	 * @param string $comment     ����
	 * @param int    $is_anonymous     �Ƿ���������
	 * 
	 * return int 
	 */
	public function add_comment($date_id, $model_user_id, $cameraman_user_id, $overall_score, $rp_score, $time_sense, $comment, $is_anonymous = 0)
	{
		$date_id = ( int ) $date_id;
		$cameraman_user_id = ( int ) $cameraman_user_id;
		$model_user_id = ( int ) $model_user_id;
		
		if (empty ( $date_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':Լ��ID����Ϊ��' );
		}
		
		if (empty ( $model_user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ģ��ID����Ϊ��' );
		}
		
		if (empty ( $cameraman_user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':��ӰʦID����Ϊ��' );
		}
		
		$insert_data ['date_id'] = $date_id;
		$insert_data ['model_user_id'] = $model_user_id;
		$insert_data ['cameraman_user_id'] = $cameraman_user_id;
		$insert_data ['overall_score'] = $overall_score;
		$insert_data ['rp_score'] = $rp_score;
		$insert_data ['time_sense'] = $time_sense;
		$insert_data ['comment'] = str_replace("\u2006","",$comment);
		$insert_data ['is_anonymous'] = $is_anonymous;
		$insert_data ['add_time'] = time ();
		
		$ret = $this->insert ( $insert_data, 'IGNORE' );
		
		if ($ret)
		{
			$msg_obj = POCO::singleton ( 'pai_information_push' );
			
			$cameraman_nickname = get_user_nickname_by_user_id ( $cameraman_user_id );
			$model_nickname = get_user_nickname_by_user_id ( $model_user_id );
			
			$send_data ['media_type'] = 'card';
			$send_data ['card_style'] = 2;
			$send_data ['card_text1'] = '��������Ӱʦ:' . $cameraman_nickname;
			$send_data ['card_title'] = '�鿴Լ������';
			
			$to_send_data ['media_type'] = 'card';
			$to_send_data ['card_style'] = 2;
			$to_send_data ['card_text1'] = '��������';
			$to_send_data ['card_title'] = '�鿴Լ������';
			
			if (! defined ( 'YUE_OA_IMPORT_ORDER' ))
			{
				if ($is_anonymous)
				{
					$msg_obj->send_msg_data ( $model_user_id, $cameraman_user_id, $send_data, $to_send_data, $date_id, 0 );
				}
				else
				{
					$msg_obj->send_msg_data ( $model_user_id, $cameraman_user_id, $send_data, $to_send_data, $date_id,2 );
					
					$date_info = get_date_info ( $date_id );
					
					//΢����Ϣ
					$weixin_pub_obj = POCO::singleton ( 'pai_weixin_pub_class' );
					$user_id = $cameraman_user_id;
					$template_code = 'G_PAI_WEIXIN_MT_CMT';
					$data = array ('datetime' => date ( "Y��n��j�� H:i", $date_info ['date_time'] ), 'nickname' => $model_nickname );
					
					$version_control = include ('/disk/data/htdocs232/poco/pai/m/config/version_control.conf.php');
					$cache_ver = trim ( $version_control ['wx'] ['cache_ver'] );
					$to_url = "http://yp.yueus.com/m/wx?{$cache_ver}#comment/list/cameraman/{$user_id}";
					
					$weixin_pub_obj->message_template_send_by_user_id ( $user_id, $template_code, $data, $to_url );
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
	 * ģ���Ƿ�������Լ��
	 * @param int    $date_id              Լ��ID
	 * @param int    $model_user_id    ��Ӱʦ�û�ID
	 * 
	 * return bool 
	 */
	public function is_comment_by_model($date_id, $model_user_id)
	{
		$date_id = ( int ) $date_id;
		$model_user_id = ( int ) $model_user_id;
		
		if (empty ( $date_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':Լ��ID����Ϊ��' );
		}
		
		if (empty ( $model_user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ģ���û�ID����Ϊ��' );
		}
		
		$where_str = "date_id={$date_id} and model_user_id={$model_user_id}";
		
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
	 * ��ȡ��Ӱʦ��������
	 * @param int    $cameraman_user_id
	 * @param bool    $b_select_count
	 * @param string    $limit
	 */
	public function get_cameraman_comment_list($cameraman_user_id, $b_select_count = false, $limit = '0,10')
	{
		$cameraman_user_id = ( int ) $cameraman_user_id;
		$list = $this->get_comment_list ( $b_select_count, 'cameraman_user_id=' . $cameraman_user_id, 'id DESC', $limit, 'date_id,model_user_id,overall_score,comment,add_time,is_anonymous' );
		foreach ( $list as $k => $val )
		{
			$list [$k] ['add_time'] = date ( "Y-m-d H:s", $val ['add_time'] );
			
			if ($val ['is_anonymous'] == 1)
			{
				$list [$k] ['nickname'] = '�����û�';
			}
			else
			{
				$list [$k] ['nickname'] = get_user_nickname_by_user_id ( $val ['model_user_id'] );
			}
		}
		
		return $list;
	}
	
	/*
	 * ����Լ��ID��ȡ��Ӱʦ��������
	 * @param int    $date_id
	 */
	public function get_cameraman_comment_by_date_id($date_id)
	{
		$date_id = ( int ) $date_id;
		$ret = $this->find ( "date_id={$date_id}" );
		return $ret;
	}
	
	
}

?>
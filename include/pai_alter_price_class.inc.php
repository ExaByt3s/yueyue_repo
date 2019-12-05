<?php
/*
 * �ļ۲�����
 */

class pai_alter_price_class extends POCO_TDG
{
	var $topic_arr;
	/**
	 * ���캯��
	 *
	 */
	public function __construct($online_topic=false)
	{

		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_alter_price_topic_tbl' );
		

		if($online_topic)
		{
			$now = time();
			$topic_list = $this->get_topic_list(false, "begin_time<{$now} and end_time>{$now} and del_status=0" ,'alter_topic_id DESC', '0,100000', 'alter_topic_id');
			foreach($topic_list as $val)
			{
				$this->topic_arr[] = $val['alter_topic_id'];
			}
		}
	}
	
	/*
	 * ���ר��
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_topic($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
		}
		
		return $this->insert ( $insert_data );
	
	}
	
	/*
	 * ɾ��ר��
	 */
	public function del_topic($id)
	{
		$id = ( int ) $id;
		
		if (empty ( $id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
		}
		
		$where_str = "alter_topic_id = {$id}";
		return $this->delete ( $where_str );
	
	}
	
	/**
	 * ����ר��
	 *
	 * @param array $data
	 * @param int $id
	 * @return bool
	 */
	public function update_topic($data, $id)
	{
		if (empty ( $data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
		}
		$id = ( int ) $id;
		if (empty ( $id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID����Ϊ��' );
		}
		
		$where_str = "alter_topic_id = {$id}";
		return $this->update ( $data, $where_str );
	}
	
	/*
	 * ��ȡר���б�
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_topic_list($b_select_count = false, $where_str = '', $order_by = 'alter_topic_id DESC', $limit = '0,100000', $fields = '*')
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
	
	public function get_topic_info($id)
	{
		$id = ( int ) $id;
		$ret = $this->find ( "alter_topic_id={$id}" );
		return $ret;
	}
	
	/*
	 * ����û���ǰ�Ƿ��л���ڽ���
	 */
	public function check_topic_user_online($user_id)
	{
		$user_id = ( int ) $user_id;
		$now = time ();
		
		$alter_price_obj = POCO::singleton ( 'pai_alter_price_user_class' );
		
		$topic_list = $this->get_topic_list ( false, "begin_time<{$now} and end_time>{$now} and del_status=0", 'alter_topic_id DESC', '0,100000', 'alter_topic_id' );
		
		foreach ( $topic_list as $val )
		{
			$topic_arr [] = $val ['alter_topic_id'];
		}
		
		if ($topic_arr)
		{
			$topic_id_str = implode ( ",", $topic_arr );
			$ret = $alter_price_obj->get_user_list ( false, "alter_topic_id in ({$topic_id_str}) and user_id={$user_id}", "id DESC", "0,1" );
			if ($ret)
			{
				$topic_info = $this->get_topic_info ( $ret [0] ['alter_topic_id'] );
				$ret [0] ['begin_time'] = $topic_info ['begin_time'];
				$ret [0] ['end_time'] = $topic_info ['end_time'];
				return $ret [0];
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	
	}
	
	/*
	 * ��ȡ��ǰ�ʣ��ʱ��
	 */
	public function get_topic_left_time($topic_id)
	{
		$now = time ();
		$ret = $this->get_topic_info ( $topic_id );
		
		$left_time = $ret ['end_time'] - $now;
		
		if ($left_time <= 0)
		{
			$left = 0;
		}
		elseif ($left_time > 0)
		{
			$left = floor ( $left_time / (3600 * 24) );
		}
		
		return $left;
	}
	
	/*
	 * ����Ƿ����ظ�������ר����û�ID
	 * @param int $alter_topic_id ר��ID
	 * @param array $user_arr �û�ID����
	 * @param int $begin_time
	 * @param int $end_time
	 * 
	 * return bool
	 */
	public function check_repeat_user($alter_topic_id,$user_arr,$begin_time=0,$end_time=0)
	{
		$alert_price_user_obj = POCO::singleton('pai_alter_price_user_class');
		
		$alter_topic_info = $this->get_topic_info ( $alter_topic_id );
		
		if(!$begin_time)
		{
			$begin_time = $alter_topic_info ['begin_time'];
		}
		if(!$end_time)
		{
			$end_time = $alter_topic_info ['end_time'];
		}
		
		$alter_topic_list = $this->get_topic_list ( false, "(begin_time between {$begin_time} and {$end_time} or end_time between {$begin_time} and {$end_time}) and del_status=0 and alter_topic_id!={$alter_topic_id}", 'alter_topic_id DESC', '0,100', 'alter_topic_id' );
		foreach ( $alter_topic_list as $val )
		{
			$alter_topic_arr [] = $val ['alter_topic_id'];
		}
		
		if ($alter_topic_arr)
		{
			$alter_topic_id_str = implode ( ",", $alter_topic_arr );
			$user_id_str = implode ( ",", $user_arr );
			
			$user_ret = $alert_price_user_obj->get_user_list ( false, "alter_topic_id in ({$alter_topic_id_str}) and user_id in ({$user_id_str})" );
			
			if ($user_ret)
			{
				return true;
			}
		}
		
		return false;
		
	}
	
	/*
	 * ��ȡ�۸�ר���û���ǩ
	 */
	public function get_topic_user_tag($user_id)
	{
		$alter_price_user_obj = POCO::singleton ( 'pai_alter_price_user_class' );
		
		$user_id = (int)$user_id;
		
		$topic_arr = $this->topic_arr;
	
		if($topic_arr)
		{
			$topic_id_str = implode(",",$topic_arr);
			$topic_user_list = $alter_price_user_obj->get_user_list(false, "alter_topic_id in ($topic_id_str) and user_id={$user_id}", 'id DESC', '0,1', 'tag');
			$tag = $topic_user_list[0]['tag'];
			if($tag)
			{
				return $tag;
			}
			else
			{
				return '';
			}
		}
		else
		{
			return '';
		}
	}
	
	
	public function get_alter_price_user_detail($user_id)
	{
		$alter_price_log_obj = POCO::singleton('pai_alter_price_log_class');
		
		$topic_arr = $this->topic_arr;
		if($topic_arr)
		{
			$topic_id_str = implode(",",$topic_arr);
			$topic_user_list = $alter_price_log_obj->get_log_list(false, "alter_topic_id in ($topic_id_str) and user_id={$user_id} group by style", 'log_id DESC', '0,100', '*');
			foreach($topic_user_list as $k=>$val)
			{
				$md5_key = md5($val['user_id'].$val['style']);
				$alter_log_arr[$md5_key] = $val;
			}
			
			return $alter_log_arr;
		}
		else
		{
			return false;
		}
	}

}

?>
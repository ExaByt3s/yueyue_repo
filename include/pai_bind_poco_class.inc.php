<?php
/*
 * PC版绑定POCO操作类
 */

class pai_bind_poco_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_bind_poco_tbl' );
	}
	
	
	public function add_bind_log($data)
	{
		$this->setTableName ( 'pai_bind_poco_log_tbl' );
		$data['add_time'] = time();
		$this->insert($data);
	}
	
	
	public function get_bind_info_by_user_id($user_id)
	{
		$id = ( int ) $id;
		$ret = $this->find ( "user_id={$user_id}" );
		return $ret;
	}
	
	public function get_bind_poco_id($user_id)
	{
        $user_id = ( int ) $user_id;
		$ret = $this->find ( "user_id={$user_id}" );
		return (int)$ret['poco_id'];
	}
	
	/*
	 * 检查是否有在PC上绑定过POCOID
	 */
	public function check_bind_poco($poco_id)
	{
        $poco_id = (int)$poco_id;
		$ret = $this->find ( "poco_id={$poco_id}" );
		if($ret)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	

	
	/*
	 * 绑定POCO帐号
	 * @param int $user_id 约约用户ID
	 * @param int $poco_id POCO ID 
	 * return int
	 */
	public function bind_poco_id($user_id, $poco_id)
	{
		$user_id = ( int ) $user_id;
		$poco_id = ( int ) $poco_id;
		
		if (empty ( $user_id ))
		{
			return false;
		}
		
		if (empty ( $poco_id ))
		{
			return false;
		}
		
		$insert_data ['user_id'] = $user_id;
		$insert_data ['poco_id'] = $poco_id;
		$this->insert ( $insert_data ,"REPLACE");
		
		//关联约约
		$relate_poco_obj = POCO::singleton('pai_relate_poco_class');
		
		$relate_info = $relate_poco_obj->get_relate_info($user_id);
		$relate_poco_id = $relate_info['poco_id'];
		$relate_user_id = $relate_info['user_id'];
		
		//检查有无关联过约约
		if($relate_poco_id)
		{
			//加LOG
			$log_data['new_poco_id'] = $poco_id;
			$log_data['old_poco_id'] = $relate_poco_id;
			$this->add_bind_log($log_data);
				
			//当前POCOID等于原来关联的POCOID就不做操作
			if($relate_poco_id!=$poco_id)
			{
				$data['poco_id'] = $poco_id;
				$relate_poco_obj->update_info($data, $user_id);
				
				$enroll_obj = POCO::singleton('event_enroll_class');
				$details_obj = POCO::singleton('event_details_class');
				
				//更新活动表和报名表
				//$details_obj->update_event_by_user_id(array("user_id"=>$poco_id),$relate_poco_id);
				$enroll_obj->update_enroll_by_user_id(array("user_id"=>$poco_id),$relate_poco_id);
				
			}
		}
		else
		{
			$relate_poco_obj->add_relate_poco_id($user_id,$poco_id);
		}
		
		return $poco_id;
	}

}

?>
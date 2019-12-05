<?php
/*
 * 图片审核发送信息类
 */

class pai_pic_send_message_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_log_db' );
		$this->setTableName ( 'pic_send_message_log' );
	}
	
	
	/*
	 * 图片发送log
	 * @param bool   $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_pic_send_message_info($user_id, $tpl_id, $add_time)
	{
		//echo $user_id."||".$tpl_id."||".$add_time;
		$user_id = (int)$user_id;
		if (empty ( $user_id ))
		{
			return false;
		}
		$tpl_id = (int)$tpl_id;
		if (empty($tpl_id)) 
		{
			return false;
		}
		if (empty($add_time)) 
		{
			return false;
		}
		$where_str = "add_time = '{$add_time}' AND user_id = {$user_id} AND tpl_id = {$tpl_id}";
		//var_dump($where_str);
		$id = $this->find($where_str, 'id');
		//var_dump($id);
		if ($id) 
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public function add_send_message($user_id, $tpl_id, $add_time)
	{
		$user_id = (int)$user_id;
		if (empty($user_id)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':用户ID不能为空' );
		}
		$tpl_id  = (int)$tpl_id;
		if (empty($tpl_id)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':模板ID不能为空' );
		}
		if (empty($add_time)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':发送时间不能为空' );
		}
		$data['user_id'] = $user_id;
		$data['tpl_id']  = $tpl_id;
		$data['add_time'] = $add_time;
		return $this->insert($data);
	}
	
}

?>
<?php
/*
 * 信息验证码code表
 * xiao xiao
 */

class pai_send_code_log_class extends POCO_TDG
{
	
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_log_db' );
		$this->setTableName ( 'tmp_code_log' );
	}
	
	/*
	 * 添加
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_info($insert_data)
	{
		global $yue_login_id;
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
		}
		$insert_data['user_id'] = $yue_login_id;
		return $this->insert ( $insert_data ,"REPLACE" );
	
	}
	/**
	* 获取code信息
	* @param  [int] $id  [user_id]
	* @return [boolean]  [true|false]
	*/
   public function get_code_info($user_id)
   {
		$user_id = ( int ) $user_id;
		if(!$user_id)
		{
			return false;
		}
		$ret = $this->find ( "user_id={$user_id}" );
		if (is_array($ret) && !empty($ret)) 
		{
			return $ret;
		}
		else
		{
			return false;
		}
   }

}

?>
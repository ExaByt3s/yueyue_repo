<?php
class heyh_test_class extends POCO_TDG
{
	public function __construct()
	{
		$this->setServerId(false);
		$this->setDBName('pai_db');
	}

	/**
	 * 设置错误提示
	 * @param string $msg
	 */
	protected function set_err_msg($msg)
	{
		$this->_last_err_msg = $msg;
	}

	/**
	 * 获取错误提示
	 */
	public function get_err_msg()
	{
		return $this->_last_err_msg;
	}

	/**
	 * 获取数据库结构
	 **/
	 public function get_db_info()
	 {
		 $sql_str = "show table status from `pai_db`";	
		 $result  = db_simple_getdata($sql_str, FALSE);
		 print_r($result);
	 }
		
}

?>

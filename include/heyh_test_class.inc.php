<?php
class heyh_test_class extends POCO_TDG
{
	public function __construct()
	{
		$this->setServerId(false);
		$this->setDBName('pai_db');
	}

	/**
	 * ���ô�����ʾ
	 * @param string $msg
	 */
	protected function set_err_msg($msg)
	{
		$this->_last_err_msg = $msg;
	}

	/**
	 * ��ȡ������ʾ
	 */
	public function get_err_msg()
	{
		return $this->_last_err_msg;
	}

	/**
	 * ��ȡ���ݿ�ṹ
	 **/
	 public function get_db_info()
	 {
		 $sql_str = "show table status from `pai_db`";	
		 $result  = db_simple_getdata($sql_str, FALSE);
		 print_r($result);
	 }
		
}

?>

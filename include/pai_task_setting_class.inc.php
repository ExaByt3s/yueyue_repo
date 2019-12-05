<?php
/**
 * ���ò�����
 * 
 * @author Henry
 */

class pai_task_setting_class extends POCO_TDG
{
	/**
	 * ���캯��
	 */
	public function __construct()
	{
		$this->setServerId(101);
		$this->setDBName('pai_task_db');
	}
	
	/**
	 * ָ����
	 */
	private function set_task_setting_tbl()
	{
		$this->setTableName('task_setting_tbl');
	}
	
	/**
	 * ��ȡ
	 * @param string $name
	 * @return string|false
	 */
	public function get($name)
	{
		$name = trim($name);
		if( strlen($name)<1 )
		{
			return false;
		}
		$where_str = "name=:x_name";
		sqlSetParam($where_str, 'x_name', $name);
		$this->set_task_setting_tbl();
		$info = $this->find($where_str);
		if( empty($info) )
		{
			return false;
		}
		$value = trim($info['value']);
		return $value;
	}
	
	/**
	 * ��ȡ
	 * @param string $name
	 * @return int|false
	 */
	public function get_int($name)
	{
		$value = $this->get($name);
		if( $value!==false ) $value = intval($value);
		return $value;
	}
	
	/**
	 * ��ȡ
	 * @param string $name
	 * @return float|false
	 */
	public function get_float($name)
	{
		$value = $this->get($name);
		if( $value!==false ) $value = floatval($value);
		return $value;
	}
	
	/**
	 * ����
	 * @param string $name
	 * @param string $value
	 * @return bool
	 */
	public function set($name, $value)
	{
		$name = trim($name);
		if( strlen($name)<1 )
		{
			return false;
		}
		$value = trim($value);
		$data = array(
			'name' => $name,
			'value' => $value,
		);
		$this->set_task_setting_tbl();
		$this->insert($data, 'REPLACE');
		return true;
	}
}

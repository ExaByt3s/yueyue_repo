<?php
/**
 * M操作类示例
 *
 * @author erldy
 */

class sms_send_list_class extends POCO_TDG 
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		//数据库名
		$this->setServerId(false);
		//服务器ID
		$this->setDBName('sms_service_db');
		//操作表名
		$this->setTableName('sms_queue_tbl');
	}
	
}

<?php
/**
 * 活动报名类
 *
 * @author tom
 * @copyright 2010-12-31
 */



class event_photo_class extends POCO_TDG
{
	/**
	 * 最后一次错误提示
	 * @var string
	 */
	protected $_last_err_msg = null;
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		//$this->setServerId(false);
		//$this->setDBName('event_db');
		//$this->setTableName('event_photo_tbl');
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
	 * 取相关属性的值,并且详细化
	 *
	 * @param array $event_info			活动信息
	 * @return array 
	 */
	public function get_related_info_detail($event_info)
	{

        $param[] = $event_info;
        $ret = curl_event_data('event_photo_class','get_related_info_detail',$param);
        return $ret;
	}		

	
}
?>
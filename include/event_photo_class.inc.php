<?php
/**
 * �������
 *
 * @author tom
 * @copyright 2010-12-31
 */



class event_photo_class extends POCO_TDG
{
	/**
	 * ���һ�δ�����ʾ
	 * @var string
	 */
	protected $_last_err_msg = null;
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		//$this->setServerId(false);
		//$this->setDBName('event_db');
		//$this->setTableName('event_photo_tbl');
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
	 * ȡ������Ե�ֵ,������ϸ��
	 *
	 * @param array $event_info			���Ϣ
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
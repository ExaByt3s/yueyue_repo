<?php
/**
 * M������ʾ��
 *
 * @author erldy
 */

class sms_send_list_class extends POCO_TDG 
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		//���ݿ���
		$this->setServerId(false);
		//������ID
		$this->setDBName('sms_service_db');
		//��������
		$this->setTableName('sms_queue_tbl');
	}
	
}

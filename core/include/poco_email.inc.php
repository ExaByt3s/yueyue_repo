<?php
/**
 * ���ʼ���
 * 
 * @author ERLDY
 * @version 2010-08-12
 */

class POCO_Email 
{
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		
	}
	
	/**
	 * ����ʼ������ʼ�����
	 *
	 * @param string $mail_from	 �����˵�ַ������Ϊ��
	 * @param string $mail_to 	�����˵�ַ������Ϊ��
	 * @param string $mail_subject �ż����⣬����Ϊ��
	 * @param string $mail_content �ż����壬����Ϊ��
	 * @param string $auth 			������ǩ�� ����Ϊ��
	 * @param bool   $is_sended_now �Ƿ���������
	 * @return mix
	 */
	public function email_send($mail_from, $mail_to, $mail_subject, $mail_content, $auth = '', $is_sended_now = false, &$err_msg)
	{
		return false;
	}
}
?>
<?php
/**
 * 发邮件类
 * 
 * @author ERLDY
 * @version 2010-08-12
 */

class POCO_Email 
{
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		
	}
	
	/**
	 * 添加邮件到发邮件队列
	 *
	 * @param string $mail_from	 寄信人地址，不能为空
	 * @param string $mail_to 	收信人地址，不能为空
	 * @param string $mail_subject 信件标题，不能为空
	 * @param string $mail_content 信件主体，可以为空
	 * @param string $auth 			发件人签名 可以为空
	 * @param bool   $is_sended_now 是否立即发送
	 * @return mix
	 */
	public function email_send($mail_from, $mail_to, $mail_subject, $mail_content, $auth = '', $is_sended_now = false, &$err_msg)
	{
		return false;
	}
}
?>
<?
/**
 * Ӧ�ÿ����Ŀ�����ļ�
 */

//Ӧ����Ŀ��������
global $my_app_sms_service;
//����Ӧ����Ŀ������Ϣ
$app_sms_config = require(dirname(__FILE__) . '/config/app_config.php');
//����Ӧ�ÿ�����ļ�
require $app_sms_config['POCO_APP_DIR'] . '/poco.php';
//����Ӧ����Ŀ�����ʼ����
require dirname(__FILE__) . '/include/poco_app_sms_service.inc.php';
//����Ӧ�ó��򲢷���Ӧ�ó������Ψһʵ��
$my_app_sms_service = POCO_APP_SMS_SERVICE::instance($app_sms_config);

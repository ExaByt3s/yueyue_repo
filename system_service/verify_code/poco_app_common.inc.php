<?
/**
 * Ӧ�ÿ����Ŀ�����ļ�
 */

//Ӧ����Ŀ��������
global $myAppVerifyCode;
//����Ӧ����Ŀ������Ϣ
$app_config = require(dirname(__FILE__) . '/config/app_config.php');
//����Ӧ�ÿ�����ļ�
require $app_config['POCO_APP_DIR'] . '/poco.php';
//����Ӧ����Ŀ�����ʼ����
require dirname(__FILE__) . '/include/poco_app_verify_code.inc.php';
//����Ӧ�ó��򲢷���Ӧ�ó������Ψһʵ��
$myAppVerifyCode = POCO_APP_VERIFY_CODE::instance($app_config);

define('G_YUEYUE_VERIFY_CODE_ROOT_PATH', realpath(dirname(__FILE__)));

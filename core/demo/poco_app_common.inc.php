<?
/**
 * Ӧ�ÿ����Ŀ�����ļ�
 */

//Ӧ����Ŀ��������
global $my_app_demo;
//����Ӧ����Ŀ������Ϣ
$app_demo_config = require(dirname(__FILE__) . '/config/app_config.php');
//����Ӧ�ÿ�����ļ�
require $app_demo_config['POCO_APP_DIR'] . '/poco.php';
//����Ӧ����Ŀ�����ʼ����
require dirname(__FILE__) . '/include/poco_app_demo.inc.php';
//����Ӧ�ó��򲢷���Ӧ�ó������Ψһʵ��
$my_app_demo = POCO_APP_DEMO::instance($app_demo_config);

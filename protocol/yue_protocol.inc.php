<?php

/**
 * ԼԼ ͨ�� Э��
 * 
 * @author willike <chenwb@yueus.com>
 * @since 2015-7-29
 * @version 1.0 Beta
 */
// ����Э���Ŀ¼
define('YUE_PROTOCOL_ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');

// Э�鴦��ʼʱ�� ( ȫ�� )
$GLOBALS['protocol_start_time'] = microtime(TRUE);

// ���� ͨ�÷���
require YUE_PROTOCOL_ROOT . 'common/function.php';

$v = conf('PROTOCOL_VERSION', 'config');   // Э��汾
include_once(YUE_PROTOCOL_ROOT . 'include/' . $v . '/yue_protocol_request.cls.php');
include_once(YUE_PROTOCOL_ROOT . 'include/' . $v . '/yue_protocol_response.cls.php');
include_once(YUE_PROTOCOL_ROOT . 'include/' . $v . '/yue_protocol_oauth.cls.php');
include_once(YUE_PROTOCOL_ROOT . 'include/' . $v . '/yue_protocol_log.cls.php');
include_once(YUE_PROTOCOL_ROOT . 'include/' . $v . '/yue_protocol_cache.cls.php');
include_once(YUE_PROTOCOL_ROOT . 'include/' . $v . '/yue_protocol_system.cls.php');

//// ���� yueЭ����
//if (!function_exists('yue_protocol_spl_autoload')) {
//
//    /**
//     * �Զ�������
//     * 
//     * @author willike <chenwb@yueus.com>
//     * @since 2015-7-30
//     */
//    function yue_protocol_spl_autoload($class_name) {
//        if (strpos($class_name, 'yue_protocol_') === 0) {
//            $file_path = YUE_PROTOCOL_ROOT . 'include/' . strtolower($class_name) . '.cls.php';
//            if (file_exists($file_path)) {
//                include_once($file_path);
//            }
//        }
//    }
//
//}
//
//if (!defined('YUE_PROTOCOL_AUTOLOAD')) {
//    //�Զ�������
//    define('YUE_PROTOCOL_AUTOLOAD', 1);
//    spl_autoload_register('yue_protocol_spl_autoload');
//}

// ���ݾ�Э��, ���������,  PHP > 5.3
//class_alias('yue_protocol_system','poco_communication_protocol_class');

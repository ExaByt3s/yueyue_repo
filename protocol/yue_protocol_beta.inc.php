<?php

/**
 * ԼԼ ͨ�� Э��
 * 
 * @author willike <chenwb@yueus.com>
 * @since 2015-8-11
 * @version 1.0 Beta
 */
// ����Э���Ŀ¼
define('YUE_PROTOCOL_ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');

// Э�鴦��ʼʱ�� ( ȫ�� )
$GLOBALS['protocol_start_time'] = microtime(TRUE);

// ���� ͨ�÷���
require YUE_PROTOCOL_ROOT . 'common/function.php';
// ���� ������ļ�
require dirname(YUE_PROTOCOL_ROOT) . '/core/yue.php';   // YUE ���
require '/disk/data/htdocs233/mypoco/apps/poco_v2/poco.php';  // POCO ���
// TODO:: ����Э��
POCO::import(YUE_PROTOCOL_ROOT . '/include');
POCO::register('yue_protocol_log');
yue_protocol_log::dump_log('poco_yuepai_android','2015-8-4');


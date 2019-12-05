<?php
/**
 * Ӧ�ó�����������ļ����ṩӦ�ó������еĹؼ�������Ϣ
 */

$root_dir = dirname(dirname(__FILE__));

/**
 * ���Ҫ���ɵ������� PHP �⣬���󱨸�Ҳ��Ҫ�޸�Ϊ��
 *
 * error_reporting(E_ALL & ~(E_STRICT | E_NOTICE));
 */
//error_reporting(E_ALL | E_STRICT);

/**
 * Ӧ�ó���������Ϣ
 */
return array(

    /**
     * Ӧ�ÿ������Ŀ¼
     */
    'POCO_APP_DIR'          => "/disk/data/htdocs232/poco/pai/core",

    /**
     * ���ݿ������ID
     */
    'SERVER_ID'             => false,
    
    /**
     * ���ݿ�����
     */
    'DB_NAME'               => '',

    /**
     * Ӧ�ó��������
     */
    'APP_NAME'              => 'sms_service',
    
    /**
     * Ӧ�ó������ַ
     */
    'APP_URL'               => 'http://www1.poco.cn/sms_service/',

    /**
     * Ӧ�ó����Ŀ¼
     */
    'ROOT_DIR'              => $root_dir,

    /**
     * ���������װ����Ŀ¼
     */
    'INCLUDE_DIR'           => "{$root_dir}/include",

    /**
     * �����ļ�����Ŀ¼
     */
    'CONFIG_DIR'            => "{$root_dir}/config",

    /**
     * ��ҳ�ؼ���չĿ¼
     */
    'WEB_CONTROLS_EXTENDS_DIR'=> "{$root_dir}/webcontrols",
    
    /**
     * ���建�������ļ�Ҫʹ�õĻ������
     *
     * ָ��ʹ��������񣬾���Ҫ�ں���� CONFIG_CACHE_SETTINGS �н�����Ӧ������
     * �������ÿ����湤�ߵ���չ�࣬����Ĭ��ʹ��POCO_Cache������Ҫ��������á�
     */
    'CONFIG_CACHE_BACKEND'  => 'POCO_Cache',

    /**
     * ָʾ�Ƿ񻺴������ļ�������
     */
    'CONFIG_CACHED'         => false,

    /**
     * ��������
     */
    'CONFIG_CACHE_SETTINGS' => array(
    ),
	
    /**
     * �Զ�������
     */
    'CUSTOM_ARRAY_SETTING' => array(	
	)
);

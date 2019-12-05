<?php
/**
 * Ĭ�������ļ�
 */

return array
(
    // {{{ ���л������
    
    /**
     * cache ϵ�к���ʹ�õĻ���Ŀ¼
     * Ӧ�ó���������ø�ѡ�����ʹ�� cache ���ܡ�
     */
    'runtime_cache_dir'         => null,

    /**
     * Ĭ��ʹ�õĻ������
     */
    'runtime_cache_backend'     => 'POCO_Cache',

    /**
     * ��ҳ�ؼ�Ŀ¼
     */
    'web_controls_dir'          => G_POCO_APP_PATH . DS . 'webcontrols',

    // }}}
    
    // {{{ ��־�ʹ�����

    /**
     * ָʾ�Ƿ������¼��־
     */
    'log_enabled' => true,

    /**
     * ָʾ��¼��Щ���ȼ�����־�������������Ļ�ֱ�ӹ��ˣ�
     */
    'log_priorities' => 'EMERG, ALERT, CRIT, ERR, WARN, NOTICE, INFO, DEBUG',

    /**
     * ��־������С����λKB��
     *
     * ��С�Ļ������Խ�Լ�ڴ棬��д����־�Ĵ�����Ƶ�������ܸ��͡�
     */
    'log_cache_chunk_size' => 64,  // 64KB

    /**
     * ������־�ļ���Ŀ¼
     */
    'log_writer_dir' => null,

    /**
     * ��־�ļ����ļ���
     */
    'log_writer_filename' => 'access.log',
    
    /**
     * ָʾ�Ƿ���ʾ������Ϣ����һ����ȫ���գ�
     *
     * ��������������رմ˹��ܡ�
     */
    'error_display'             => true,

    /**
     * ָʾ�Ƿ���ʾ�ѺõĴ�����Ϣ���а�ȫ���գ�
     *
     * ��������������رմ˹��ܡ�
     */
    'error_display_friendly'    => true,

    /**
     * ָʾ�Ƿ��ڴ�����Ϣ����ʾ����λ�õ�Դ���루�а�ȫ���գ�
     *
     * ��������������رմ˹��ܡ�
     */
    'error_display_source'      => true,
    
    // }}}
);

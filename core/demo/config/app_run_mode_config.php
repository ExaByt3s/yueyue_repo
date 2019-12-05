<?php
/**
 * Ӧ�ó�������ʱ����־��¼�ʹ���������
 */

return array(

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
     * ������־�ļ���Ŀ¼
     */
    'log_writer_dir' => dirname(dirname(__FILE__)) . '/log',

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

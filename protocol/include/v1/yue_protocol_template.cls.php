<?php

defined('YUE_PROTOCOL_ROOT') || die('INC file not loaded!');

/**
 * ԼЭ�� ģ�����  ( δ�� | �ݲ����� )
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-7-31
 * @version 1.0 Beta
 */
class yue_protocol_template {

    /**
     * @var string Smarty·��
     */
    private $tmp_root = '';

    /**
     * @var string ��ҳ�ؼ���չĿ¼
     */
    private $webcontrols = null;

    /**
     * @var string ��Դ����
     */
    private $_app_name = null;

    /**
     * ��ʼ��
     */
    public function __construct($app_name = null) {
        $tmp_root = $this->tmp_root;
        $this->_app_name = $app_name;
        // ����������·��
        POCO::import(G_POCO_APP_PATH . '/model');
        POCO::import($tmp_root . '/include');
        // ����ģ�峣��
        if (!defined('G_POCO_APP_TEMPLATE_DIR')) {
            define('G_POCO_APP_TEMPLATE_DIR', $tmp_root);
        }
        // ע��Ӧ�ó������
        POCO::register($this, 'app');
    }

    /**
     * ��д�����INI��������ȡ��ǰӦ����Ŀ��ָ������������
     *
     * @param string $option ����ָ��Ҫ��ȡ��������
     * @return mixed �����������ֵ
     */
    public function init($option) {
        if (empty($option)) {
            $option = '/';
        } elseif ($option == '/') {
            $option = $this->_app_name;
        } else {
            $option = $this->_app_name . "/{$option}";
        }
        return POCO::ini($option);
    }

    /**
     * ����ģ����ͼ����
     * ����Ҫ�Լ��ֶ�����ģ�����ֱ��ʹ�ø÷������ɻ��ģ�����
     *
     * @param string $tpl_filename ��ͼģ���ļ�
     * @param boolean $new_obj ǿ�������µĶ���
     * 
     * @return object
     */
    public function getView($tpl_filename = '', $new_obj = false) {
        if ($new_obj === true) {
            return new SmartTemplate($tpl_filename);
        } else {
            return POCO::singleton('SmartTemplate', $tpl_filename);
        }
    }

    /**
     * ִ��ָ������ͼ
     *
     * @param string $tpl_filename ��ͼģ���ļ�
     * @param array  $view_data    ģ�����
     */
    public function executeView($tpl_filename, $view_data) {
        if (empty($tpl_filename)) {
            throw new App_Exception('ģ���ļ�����Ϊ��');
        }
        if (empty($view_data) && !is_array($view_data)) {
            throw new App_Exception('ģ���������Ϊ�ջ�������');
        }
        $tpl = $this->getView($tpl_filename);
        $tpl->assign($view_data);
        $tpl->output();
    }

    /**
     * ����һ���ؼ��� HTML ����
     *
     * @param string $type    �ؼ�����
     * @param string $name    ���ɿؼ�������
     * @param array $attribs  �ؼ��������
     * @param boolean $return �Ƿ񷵻�HTML
     *
     * @return string
     */
    public function webControl($name, $attribs = null, $return = false) {
        if (empty($name)) {
            throw new App_Exception('����ָ�����ÿؼ�������');
        }
        $data = array(
            $this->webcontrols,
            $this->init('web_controls_dir')
        );
        $web_control_obj = POCO::singleton('Web_Controls', array($data));
        return $web_control_obj->control($name, $attribs, $return);
    }

}

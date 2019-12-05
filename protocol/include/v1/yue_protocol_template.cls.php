<?php

defined('YUE_PROTOCOL_ROOT') || die('INC file not loaded!');

/**
 * 约协议 模板操作  ( 未测 | 暂不可用 )
 *
 * @author willike <chenwb@yueus.com>
 * @since 2015-7-31
 * @version 1.0 Beta
 */
class yue_protocol_template {

    /**
     * @var string Smarty路径
     */
    private $tmp_root = '';

    /**
     * @var string 网页控件扩展目录
     */
    private $webcontrols = null;

    /**
     * @var string 来源名称
     */
    private $_app_name = null;

    /**
     * 初始化
     */
    public function __construct($app_name = null) {
        $tmp_root = $this->tmp_root;
        $this->_app_name = $app_name;
        // 导入类搜索路径
        POCO::import(G_POCO_APP_PATH . '/model');
        POCO::import($tmp_root . '/include');
        // 定义模板常量
        if (!defined('G_POCO_APP_TEMPLATE_DIR')) {
            define('G_POCO_APP_TEMPLATE_DIR', $tmp_root);
        }
        // 注册应用程序对象
        POCO::register($this, 'app');
    }

    /**
     * 重写父类的INI方法，获取当前应用项目中指定的设置内容
     *
     * @param string $option 参数指定要获取的设置名
     * @return mixed 返回设置项的值
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
     * 返回模板视图对象
     * 不需要自己手动声明模板对象，直接使用该方法即可获得模板对象
     *
     * @param string $tpl_filename 视图模板文件
     * @param boolean $new_obj 强制声明新的对象
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
     * 执行指定的视图
     *
     * @param string $tpl_filename 视图模板文件
     * @param array  $view_data    模板变量
     */
    public function executeView($tpl_filename, $view_data) {
        if (empty($tpl_filename)) {
            throw new App_Exception('模板文件不能为空');
        }
        if (empty($view_data) && !is_array($view_data)) {
            throw new App_Exception('模板变量不能为空或不是数组');
        }
        $tpl = $this->getView($tpl_filename);
        $tpl->assign($view_data);
        $tpl->output();
    }

    /**
     * 构造一个控件的 HTML 代码
     *
     * @param string $type    控件类型
     * @param string $name    生成控件的名称
     * @param array $attribs  控件相关属性
     * @param boolean $return 是否返回HTML
     *
     * @return string
     */
    public function webControl($name, $attribs = null, $return = false) {
        if (empty($name)) {
            throw new App_Exception('必须指定调用控件的类型');
        }
        $data = array(
            $this->webcontrols,
            $this->init('web_controls_dir')
        );
        $web_control_obj = POCO::singleton('Web_Controls', array($data));
        return $web_control_obj->control($name, $attribs, $return);
    }

}

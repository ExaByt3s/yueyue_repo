<?php
/**
 * Web_Controls 页面控件调度器
 *
 * @author erldy
 * @package Core
 */

class Web_Controls
{
    /**
     * 扩展的控件
     *
     * @var array
     */
    protected static $_extends = array();

    /**
     * 保存扩展控件的目录
     *
     * @var array
     */
    protected $_extendsDir = array();

    /**
     * 构造函数
     *
     * @param string|array $extendsDir
     *
     * @return FLEA_WebControls
     */
    public function __construct($extendsDir)
    {
        $this->set_extendsDir($extendsDir);
    }
    
    /**
     * 设置搜索文件路径
     *
     * @param unknown_type $extendsDir
     */
    public function set_extendsDir($extendsDir)
    {
        if (is_array($extendsDir)) 
        {
            $this->_extendsDir = array_merge($this->_extendsDir, $extendsDir);
        } 
        elseif ($extendsDir != '') 
        {
            $this->_extendsDir[] = $extendsDir;
        }
        
        $this->_extendsDir = array_unique($this->_extendsDir);
    }    

    /**
     * 构造一个控件的 HTML 代码
     *
     * @param string $name    控件名称
     * @param array $attribs  控件的参数
     * @param boolean $return 是否返回HTML
     *
     * @return string
     */
    public function control($name, $attribs, $return = false)
    {
        //$name = strtolower($name);
        //$render = '_ctl' . ucfirst($name);
        $render = "_ctl{$name}";
        $attribs = (array)$attribs;
		
        $__ctl_out = false;
        if (method_exists($this, $render)) 
        {
            $__ctl_out = $this->{$render}($attribs);
        } 
        else 
        {
            //$extfilename = ucfirst($name) . '.php';
            $extfilename = "{$name}.php";
            if (!isset($this->_extends[$name])) 
            {
                foreach ($this->_extendsDir as $dir) 
                {
                    if (file_exists($dir . DS . $extfilename)) 
                    {
                        require($dir . DS . $extfilename);
                        $this->_extends[$name] = true;
                        break;
                    }
                }
            }

            if (isset($this->_extends[$name])) 
            {
                $__ctl_out = call_user_func_array($render,
                        array('attribs' => $attribs));
            }
        }

        if ($__ctl_out === false) 
        {
            $__ctl_out = "无效的控件类型: \"{$name}\"";
        }

        if ($return) { return $__ctl_out; }
        echo $__ctl_out;
        return '';
    }
}

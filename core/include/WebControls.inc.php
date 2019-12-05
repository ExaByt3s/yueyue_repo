<?php
/**
 * Web_Controls ҳ��ؼ�������
 *
 * @author erldy
 * @package Core
 */

class Web_Controls
{
    /**
     * ��չ�Ŀؼ�
     *
     * @var array
     */
    protected static $_extends = array();

    /**
     * ������չ�ؼ���Ŀ¼
     *
     * @var array
     */
    protected $_extendsDir = array();

    /**
     * ���캯��
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
     * ���������ļ�·��
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
     * ����һ���ؼ��� HTML ����
     *
     * @param string $name    �ؼ�����
     * @param array $attribs  �ؼ��Ĳ���
     * @param boolean $return �Ƿ񷵻�HTML
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
            $__ctl_out = "��Ч�Ŀؼ�����: \"{$name}\"";
        }

        if ($return) { return $__ctl_out; }
        echo $__ctl_out;
        return '';
    }
}

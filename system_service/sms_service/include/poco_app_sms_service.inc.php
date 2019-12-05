<?php
/**
 * POCO_APP 继承了框架核心基类，封装了应用程序的基本启动流程和初始化操作，并为应用程序提供一些公共服务。
 *
 * 主要完成下列任务：
 * - 初始化运行环境
 * - 为应用程序提供公共服务
 * 
 * @author ERLDY
 * @package core
 */

class POCO_APP_SMS_SERVICE
{
    /**
     * 应用程序的基本设置
     *
     * @var array
     */
    protected $_app_config;
    
    /**
     * 当前应用项目名称
     *
     * @var string
     */
    public static $_app_name;

    /**
     * 构造函数
     *
     * @param array $app_config
     *
     * 构造应用程序对象
     */
    protected function __construct(array $app_config)
    {
        /**
         * 初始化运行环境
         */
        // 禁止 magic quotes
        set_magic_quotes_runtime(0);

        // 处理被 magic quotes 自动转义过的数据
        if (get_magic_quotes_gpc())
        {
            $in = array(& $_GET, & $_POST, & $_COOKIE, & $_REQUEST);
            while (list ($k, $v) = each($in))
            {
                foreach ($v as $key => $val)
                {
                    if (! is_array($val))
                    {
                        $in[$k][$key] = stripslashes($val);
                        continue;
                    }
                    $in[] = & $in[$k][$key];
                }
            }
            unset($in);
        }

        // 设置异常处理函数
        set_exception_handler(array($this, 'exception_handler'));

        // 初始化应用程序设置
        //self::cleanIni('/');
        $this->_app_config = $app_config;
        $this->_initConfig();
		self::$_app_name = $app_config['APP_NAME'];
		
        // 导入类搜索路径
        POCO::import(G_POCO_APP_PATH . '/model');
        POCO::import($app_config['INCLUDE_DIR']);
        
        // 定义模板常量
        if (!defined('G_POCO_APP_TEMPLATE_DIR')) 
        {
        	define('G_POCO_APP_TEMPLATE_DIR', $app_config['ROOT_DIR']);
        }
        
        // 注册应用程序对象
        POCO::register($this, 'app');
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
    	
    }

    /**
	 * 返回模板视图对象
	 * 不需要自己手动声明模板对象，直接使用该方法即可获得模板对象
	 *
	 * @param string $tpl_filename 视图模板文件
	 * @param boolean $new_obj     强制声明新的对象
	 * 
	 * @return object
	 */
	public function getView($tpl_filename = '', $new_obj = false)
	{
		if ($new_obj === true) 
		{
			return new SmartTemplate($tpl_filename);
		}
		else 
		{
			return POCO::singleton('SmartTemplate', $tpl_filename);
		}
	}
	
	/**
	 * 执行指定的视图
	 *
	 * @param string $tpl_filename 视图模板文件
	 * @param array  $view_data    模板变量
	 */
	public function executeView($tpl_filename, $view_data)
	{
		if (empty($tpl_filename)) 
		{
			throw new App_Exception('模板文件不能为空');
		}
		if (empty($view_data) && !is_array($view_data)) 
		{
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
	public function webControl($name, $attribs = null, $return = false)
	{
		if (empty($name)) 
		{
			throw new App_Exception('必须指定调用控件的类型');
		}
		$web_control_obj = POCO::singleton('Web_Controls', array(array($this->_app_config['WEB_CONTROLS_EXTENDS_DIR'], self::ini('web_controls_dir'))));
		return $web_control_obj->control($name, $attribs, $return);
	}
	
    /**
     * 检查应用是否已经注册
     *
     * @return bool
     */
    public function checkAppRegistered()
    {
    	$app_name = $this->_app_config['APP_NAME'];
    	if (!empty($app_name)) 
    	{
    		$app_obj = POCO::singleton('app_register_class');
	    	$ret = $app_obj->check_app_registered($app_name);
	    	if ($ret === false)
	    	{
	    		throw new App_Exception("未定义的应用项目:{$app_name}");
	    	}
	    	elseif ($ret === -1)
	    	{
	    		throw new App_Exception("应用项目已停止:{$app_name}");
	    	}
    	}  	
    	return ;
    }
    
    /**
     * 返回应用程序类的唯一实例
     *
     * @param array $app_config
     *
     * @return MyApp
     */
    static function instance(array $app_config = null)
    {
    	static $instance;
        if (is_null($instance))
        {
            if (empty($app_config))
            {
                die('请先配置应用环境');
            }
            $instance = new POCO_APP_SMS_SERVICE($app_config);
            // 检查应用是否已经注册
            //$instance->checkAppRegistered();
        }
        return $instance;
    }

    /**
     * 重写父类的INI方法，获取当前应用项目中指定的设置内容
     *
     * @param string $option 参数指定要获取的设置名
     * @return mixed 返回设置项的值
     */
    public static function ini($option)
    {
    	if (empty($option)) 
    	{
    		$option = '/';
    	}
    	elseif ($option == '/')
    	{
    		$option = self::$_app_name;
    	}
    	else
    	{
    		$option = self::$_app_name . "/{$option}";
    	}
    	
    	return POCO::ini($option);
    }
    
    /**
     * 返回应用程序基础配置的内容
     *
     * 如果没有提供 $item 参数，则返回所有配置的内容
     *
     * @param string $item
     *
     * @return mixed
     */
    public function config($item = null)
    {
        if ($item)
        {
            return isset($this->_app_config[$item]) ? $this->_app_config[$item] : null;
        }
        return $this->_app_config;
    }

    /**
     * 载入配置文件内容
     *
     * @param array $app_config
     *
     * @return array
     */
    static function loadConfigFiles(array $app_config)
    {
		/************** 载入框架默认配置 *****************/
        $config = require(G_POCO_APP_PATH . '/config/default_config.php');
        $poco_api = require(G_POCO_APP_PATH . '/config/poco_api_config.php');
        $config['poco_api'] = $poco_api;
        /************** 载入框架默认配置 *****************/
        
        /************** 载入项目配置 *******************/
        $cfg = $app_config['CONFIG_DIR'];
        $files = $app_config['CUSTOM_ARRAY_SETTING'];
        foreach ($files as $filename => $scope)
        {
            if (!file_exists("{$cfg}/$filename")) continue;
            $contents = POCO::loadFile($filename, "{$cfg}/"); 
            if ($scope == 'global')
            {
                $config = array_merge($config, $contents);
            }
            else
            {            
	            $config[$scope] = array();
	            $config[$scope] = $config[$scope] + $contents;
            }
        }
		/************** 载入项目配置 *******************/
        return $config;
    }
    
    /**
     * 初始化应用程序设置
     */
    protected function _initConfig()
    {
    	// 配置缓存的KEY
		$cache_id = $this->_app_config['APP_NAME'] . '_poco_app_config_info';
        // 载入配置文件
        if ($this->_app_config['CONFIG_CACHED'])
        {
            /**
             * 从缓存载入配置文件内容
             */

            // 构造缓存服务对象
            $backend = $this->_app_config['CONFIG_CACHE_BACKEND'];
            $settings = isset($this->_app_config['CONFIG_CACHE_SETTINGS'][$backend]) ? $this->_app_config['CONFIG_CACHE_SETTINGS'][$backend] : null;
            $cache = new $backend($settings);

            // 载入缓存内容
            $config = $cache->get($cache_id);
            if (!empty($config))
            {
            	POCO::replaceIni('poco_api', $config['poco_api']);
            	unset($config['poco_api']);
                POCO::replaceIni($this->_app_config['APP_NAME'], $config);
                return;
            }
        }

        // 没有使用缓存，或缓存数据失效
        $config = self::loadConfigFiles($this->_app_config);
        $config['app_config'] = $this->_app_config;
        if ($this->_app_config['CONFIG_CACHED'])
        {
            $cache->set($cache_id, $config);
        }

        POCO::replaceIni('poco_api', $config['poco_api']);
        unset($config['poco_api']);
        POCO::replaceIni($this->_app_config['APP_NAME'], $config);
        return;
    }

	/**
	 * 默认的异常处理
	 */
	public function exception_handler(Exception $ex)
	{
		if (!self::ini('error_display')) { return ; }
	    if (self::ini('error_display_friendly')) 
	    {
	       echo $ex->getMessage();
	       dump('如果要改变对异常的处理，请修改文件 "' . __FILE__ . '" 的 exception_handler() 方法');
	    } 
	    else 
	    {
	        App_Exception::dump($ex);
	    }
	}
}


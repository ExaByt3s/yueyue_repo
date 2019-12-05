<?php
/**
	* SmartTemplate Class
	* 
	* 'Compiles' HTML-Templates to PHP Code
	*
	*
	* Usage Example I:
	*
	* $page = new SmartTemplate( "template.html" );
	* $page->assign( 'TITLE',  'TemplateDemo - Userlist' );
	* $page->assign( 'user',   DB_read_all( 'select * from ris_user' ) );
	* $page->output();
	*	
	* Usage Example II:
	*
	* $data = array(
	*             'TITLE' => 'TemplateDemo - Userlist',  
	*             'user'  => DB_read_all( 'select * from ris_user' )
	*         );
	* $page = new SmartTemplate( "template.html" );
	* $page->output( $data );
	*
	*
	* @author Philipp v. Criegern philipp@criegern.com
	* @version 1.0.2 02.04.2005 Tony Ver
	*/
class SmartTemplate
{
	/**
		* Whether to store compiled php code or not (for debug purpose)
		*
		* @access public
		*/
	var $reuse_code     =  true;

	/**
		* Directory where all templates are stored
		* Can be overwritten by global configuration array $_CONFIG['template_dir']
		*
		* @access public
		*/
	var $template_dir       =  '';

	/**
		* Where to store compiled templates
		* Can be overwritten by global configuration array $_CONFIG['smarttemplate_compiled']
		*
		* @access public
		*/
	var $temp_dir       =  '/tmp/';

	/**
		* Temporary folder for output cache storage
		* Can be overwritten by global configuration array $_CONFIG['smarttemplate_cache']
		*
		* @access public
		*/
	var $cache_dir      =  '/tmp/';

	/**
		* Default Output Cache Lifetime in Seconds
		* Can be overwritten by global configuration array $_CONFIG['cache_lifetime']
		*
		* @access public
		*/
	var $cache_lifetime =  600;

	var $cache_ignore_str_arr =array();
	/**
		* Temporary file for output cache storage
		*
		* @access private
		*/
	var $cache_filename;

	/**
		* The template filename
		*
		* @access private
		*/
	var $tpl_file;

	/**
		* The compiled template filename
		*
		* @access private
		*/
	var $cpl_file;

	/**
		* Template content array
		*
		* @access private
		*/
	var $data           =  array();

	/**
		* Parser Class
		*
		* @access private
		*/
	var $parser;

	/**
		* Debugger Class
		*
		* @access private
		*/
	var $debugger;


	/**
		* SmartTemplate Constructor
		*
		* @access public
		* @param string $template_filename Template Filename
		*/
	function SmartTemplate ( $template_filename = '' )
	{
		global $_CONFIG;

		if (!empty($_CONFIG['smarttemplate_compiled']))
		{
			$this->temp_dir  =  $_CONFIG['smarttemplate_compiled'];
		}
		if (!empty($_CONFIG['smarttemplate_cache']))
		{
			$this->cache_dir  =  $_CONFIG['smarttemplate_cache'];
		}
		if (is_numeric($_CONFIG['cache_lifetime']))
		{
			$this->cache_lifetime  =  $_CONFIG['cache_lifetime'];
		}
		if (!empty($_CONFIG['template_dir'])  &&  is_file($_CONFIG['template_dir'] . '/' . $template_filename))
		{
			$this->template_dir  =  $_CONFIG['template_dir'];
		}
		if (!empty($_CONFIG['cache_ignore_str_arr']))
		{
			$this->cache_ignore_str_arr  =  $_CONFIG['cache_ignore_str_arr'];
		}

		$this->tpl_file  =  $template_filename;
	}

	//	Methods used in older parser versions
	function set_templatefile ($template_filename)	{	$this->tpl_file  =  $template_filename;	}
	function add_value ($name, $value )				{	$this->assign($name, $value);	}
	function add_array ($name, $value )				{	$this->append($name, $value);	}


	/**
		* Assign Template Content
		*
		* Usage Example:
		* $page->assign( 'TITLE',     'My Document Title' );
		* $page->assign( 'userlist',  array(
		*                                 array( 'ID' => 123,  'NAME' => 'John Doe' ),
		*                                 array( 'ID' => 124,  'NAME' => 'Jack Doe' ),
		*                             );
		*
		* @access public
		* @param string $name Parameter Name
		* @param mixed $value Parameter Value
		* @desc Assign Template Content
		*/
	function assign ( $name, $value = '' )
	{
		if (is_array($name))
		{
			foreach ($name as $k => $v)
			{
				$this->data[$k]  =  $v;
			}
		}
		else
		{
			$this->data[$name]  =  $value;
		}
	}


	/**
		* Assign Template Content
		*
		* Usage Example:
		* $page->append( 'userlist',  array( 'ID' => 123,  'NAME' => 'John Doe' ) );
		* $page->append( 'userlist',  array( 'ID' => 124,  'NAME' => 'Jack Doe' ) );
		*
		* @access public
		* @param string $name Parameter Name
		* @param mixed $value Parameter Value
		* @desc Assign Template Content
		*/
	function append ( $name, $value )
	{
		if (is_array($value))
		{
			$this->data[$name][]  =  $value;
		}
		elseif (!is_array($this->data[$name]))
		{
			$this->data[$name]  .=  $value;
		}
	}


	/**
		* Parser Wrapper
		* Returns Template Output as a String
		*
		* @access public
		* @param array $_top Content Array
		* @return string  Parsed Template
		* @desc Output Buffer Parser Wrapper
		*/
	function result ( $_top = '' )
	{
		ob_start();
		$this->output( $_top );
		$result  =  ob_get_contents();
		ob_end_clean();
		return $result;
	}


	/**
		* Execute parsed Template
		* Prints Parsing Results to Standard Output
		*
		* @access public
		* @param array $_top Content Array
		* @desc Execute parsed Template
		*/
	function output ( $_top = '' )
	{
		global $_top;

		//	Make sure that folder names have a trailing '/'
		if (strlen($this->template_dir)  &&  substr($this->template_dir, -1) != '/')
		{
			$this->template_dir  .=  '/';
		}
		if (strlen($this->temp_dir)  &&  substr($this->temp_dir, -1) != '/')
		{
			$this->temp_dir  .=  '/';
		}
		//	Prepare Template Content
		if (!is_array($_top))
		{
			if (strlen($_top))
			{
				$this->tpl_file  =  $_top;
			}
			$_top  =  $this->data;
		}
		$_obj  =  &$_top;
		$_stack_cnt  =  0;
		$_stack[$_stack_cnt++]  =  $_obj;

		//	Check if template is already compiled
		$this->cpl_file  =  $this->temp_dir . preg_replace('/[:\/.\\\\]/', '_', $this->tpl_file) . '.php';
		$compile_template  =  true;
		if ($this->reuse_code)
		{
			if (is_file($this->cpl_file))
			{
				if ($this->mtime($this->cpl_file) > $this->mtime($this->template_dir . $this->tpl_file))
				{
					//TODO 临时注释掉
					//$compile_template  =  false;
				}
			}
		}
		if ($compile_template)
		{
			include_once(dirname(__FILE__) . '/class.smarttemplateparser.php');
			$this->parser = new SmartTemplateParser($this->template_dir . $this->tpl_file);
			if (!$this->parser->compile($this->cpl_file))
			{
				exit( "SmartTemplate Compiler Error: " . $this->parser->error );
			}
		}


		ob_start();

		include($this->cpl_file);
		$cdn_content=ob_get_clean();
		if ((defined("G_SMARTTEMPLATE_PARSE_CDN_IMG_LINK") && G_SMARTTEMPLATE_PARSE_CDN_IMG_LINK) || (defined("POCO_CONTENT_OUPUT_BGP_PARSER_LINK") && POCO_CONTENT_OUPUT_BGP_PARSER_LINK))  //做cdn替换
		{
			$content_output_cdn_parser_obj = new poco_content_output_cdn_parser_class();
			$cdn_content = $content_output_cdn_parser_obj->parse($cdn_content);
			//ob_end_flush();
		}
		
		/**
		 * 请cdn的链接替换为正常链接
		 */
		if(defined('G_SMARTTEMPLATE_REPLACE_NOCDN_URL') && defined("G_SMARTTEMPLATE_PARSE_CDN_IMG_LINK"))
		{
			$cdn_content = str_replace('__http-poco-nocdn__://','http://',$cdn_content);
		}


		if (stripos($_SERVER['HTTP_HOST'],'tcm9.com')) 
		{
			$cdn_content=str_replace("http://img","http://www.tcm9.com/img.php?a=",$cdn_content);
			$cdn_content=str_replace(".poco.cn/","&b=",$cdn_content);
			$cdn_content=str_replace("best_pocoers","best",$cdn_content);
		}		
		
		/**
		 * 增加页面运行时间的输入
		 */
		if (class_exists("poco_run_time_class") && !defined("IGNORE_TPL_RUN_TIME_SCRIPT") && false===stripos($_SERVER['HTTP_HOST'],'tcm9.com')) 
		{
			$page_totalrun_time = poco_run_time_class::end();
			$cdn_content =  preg_replace("/\s<\/head>\s/i","</head>\n<script>__pocoStieStat={prt:'{$page_totalrun_time}',pst:new Date().getTime()};</script>\n",$cdn_content);
		}

		echo $cdn_content;




		//	Execute Compiled Template


		//	Delete Global Content Array in order to allow multiple use of SmartTemplate class in one script
		//unset ($_top);
		unset ($GLOBALS["_top"]);


	}


	/**
		* Debug Template
		*
		* @access public
		* @param array $_top Content Array
		* @desc Debug Template
		*/
	function debug ( $_top = '' )
	{
		//	Prepare Template Content
		if (!$_top)
		{
			$_top  =  $this->data;
		}
		include_once(dirname(__FILE__) . '/class.smarttemplatedebugger.php');
		$this->debugger = new SmartTemplateDebugger($this->template_dir . $this->tpl_file);
		$this->debugger->start($_top);
	}


	/**
		* Start Ouput Content Buffering
		*
		* Usage Example:
		* $page = new SmartTemplate('template.html');
		* $page->use_cache();
		* ...
		*
		* @access public
		* @desc Output Cache
		*/
	function use_cache ( $key = '')
	{
		if (empty($_POST)  && !isset($_GET["_no_cache"]))
		{
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  // always modified now
			header("Expires: " .gmdate ("D, d M Y H:i:s", time() + $this->cache_lifetime). " GMT"); // Expires one hour later

			if(defined("G_SMARTTEMPLATE_USE_POCO_CACHE") )
			{
				$md5_get_vars_arr = $_GET;
				unset($md5_get_vars_arr["_no_cache"]);
				unset($md5_get_vars_arr["no_cache"]);

				ksort($md5_get_vars_arr);
				$md5_get_vars_str = serialize($md5_get_vars_arr);

				$poco_cache_obj = new poco_cache_class();
				$this->cache_filename  = $this->cache_dir . 'cache_'. urlencode($_SERVER['HTTP_HOST']). "_" . urlencode($_SERVER['SCRIPT_NAME']). "_". md5($md5_get_vars_str . serialize($key)) . '.ser';
				$cache_content = $poco_cache_obj->get_cache($this->cache_filename);

				if (!empty($cache_content))
				{
					echo $cache_content;
					echo "<!-- is cache -->";
					exit;
				}
				ob_start( array( &$this, 'cache_callback' ),1024000*10,false );
			}



			/*$this->cache_dir=realpath($this->cache_dir);
			if (strlen($this->cache_dir)  &&  substr($this->cache_dir, -1) != '/')
			{
			$this->cache_dir  .=  '/';
			}

			//$this->cache_filename  =  $this->cache_dir . 'cache_' . md5($_SERVER['REQUEST_URI'] . serialize($key)) . '.ser';
			$this->cache_filename  =  $this->cache_dir . 'cache_' . urlencode($_SERVER['SCRIPT_NAME']). "_". md5($_SERVER['REQUEST_URI'] . serialize($key)) . '.ser';

			if (($_SERVER['HTTP_CACHE_CONTROL'] != 'no-cache')  &&  ($_SERVER['HTTP_PRAGMA'] != 'no-cache')  &&  @is_file($this->cache_filename))
			{
			if ((time() - filemtime($this->cache_filename)) < $this->cache_lifetime)
			{
			$cache_content=file_get_contents($this->cache_filename);

			if (is_array($this->cache_ignore_str_arr) && count($this->cache_ignore_str_arr>0))
			{
			$b_found=false;
			foreach ($this->cache_ignore_str_arr as $ignorestr)
			{
			if (eregi($ignorestr,$cache_content))
			{
			$b_found=true;
			}
			}

			if (!$b_found)
			{
			echo $cache_content;
			exit;
			}
			}
			else
			{
			exit;
			}
			}
			}
			ob_start( array( &$this, 'cache_callback' ) );*/
		}
	}

	/**
		* Output Buffer Callback Function
		*
		* @access private
		* @param string $output
		* @return string $output
		*/
	function cache_callback ( $output )
	{
		if (!empty($output))
		{
			$poco_cache_obj = new poco_cache_class();
			$cache_content = $output."\n<!-- cache time:".date("Y-m-d H:i:s")." -->";
			$poco_cache_obj->save_cache($this->cache_filename, $cache_content, $this->cache_lifetime);
			/*
			if ($hd = @fopen($this->cache_filename, 'w'))
			{
			fputs($hd,  $output);
			fclose($hd);
			}*/
		}

		return $output;
	}


	/**
		* Determine Last Filechange Date (if File exists)
		*
		* @access private
		* @param string $filename
		* @return mixed
		* @desc Determine Last Filechange Date 
		*/
	function mtime ( $filename )
	{
		if (@is_file($filename))
		{
			return filemtime($filename);
		}
	}

}

?>
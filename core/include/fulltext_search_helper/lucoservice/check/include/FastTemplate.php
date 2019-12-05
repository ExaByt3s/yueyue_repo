<?php
class FastTemplate
{
  /* relative filenames are relative to this pathname */
  var $root   = "";
  
  /* block { count,block_str|key_map,pointer,localvarkeys,localvarvals }*/
  var $blocks = array();
  
  /* $varkeys[key] = "key"; $varvals[key] = "value"; */
  var $varkeys = array();
  var $varvals = array();
  
  var $LAST		=	"";
  
  /***************************************************************************/
  /* public: Constructor.
   * root:     template directory.
   * unknowns: how to handle unknown variables.
   */
  function FastTemplate($root = "./")
  {
  	global $http_cgi,$http_img;
  	
	$this->set_root($root);
	
	$this->assign("HTTP_CGI",$http_cgi);
	$this->assign("HTTP_IMG",$http_img);
  }

     /* public: setroot(pathname $root)
   * root:   new template directory.
   */  
  function set_root($root)
  {
  	global $doc_cgi;
  	if(!$root)
  		$root = $doc_cgi."template";
  		
    if (!is_dir($root))
    {
		$this->error("set_root: $root is not a directory");
		return false;
    }
    
    $this->root = $root;
    return true;
  }

  /* public: define(array $filelist)
   * filelist: array of handle, filename pairs.
   *
   * public: define(string $handle, string $filename)
   * handle: handle for a filename,
   * filename: name of template file
   */
  function define($handle, $filename = "")
  {
    if (is_string($handle))
    {
      if ($filename == "")
      {
        $this->error("define: For handle $handle filename is empty");
      }
      $this->loadfile($handle,$this->filename($filename));
    }
    else if(is_array($handle))
    {
      reset($handle);
      while(list($h, $f) = each($handle))
      {
      	if(!$f)
      		$this->error("define: For handle $h filename is empty");
        $this->loadfile($h,$this->filename($f));
      }
    }
    else
    	$this->error("define: wrong handle type ".gettype($handle));
  }
    
  function define_block($parent,$block_handle,$default_value = "")
  {
  	if(!$parent)
    	$this->error("define_block: empty parent");
      
    if(!$block_handle)
    	$this->error("define_block: empty block_handle");
    	
   	$var_name = $block_handle;
  
    $str = $this->get_block($parent);
    				
    $reg = "/<!--\s+DEFINE BLOCK $block_handle\s+-->(.*)\s*<!--\s+ENDDEF BLOCK $block_handle\s+-->/sm";
    $c = preg_match_all($reg, $str, $m);

	if(!$c)
    	$this->error("define_block: not found block $block_handle");
    
    $mblock = $m[1][0];
    if(!$mblock)
    	$this->error("define_block: block $block_handle is empty");
    
    $str = preg_replace($reg, "{" . "$var_name}", $str);
    $this->replace_block($parent, $str);
    $this->set_var($var_name,$default_value);
    
    $block_c = 0;
    
    $reg = "/<!--\s+BEGIN CHILDBLOCK $block_handle\s+([\w\s\=]*)-->(.*?)\s*<!--\s+END CHILDBLOCK $block_handle\s+-->/sm";
    $block_c = preg_match_all($reg, $mblock, $m);
    
    if(!$block_c)
    {    	
    	$this->set_block($block_handle,$mblock);
    }
    else
    {
        $child = array();
    
    	for($i=0;$i<$block_c;$i++)
    	{
    		if(!$m[2][$i])
    			$this->error("define_childblock: no.$i childblock $block_handle is empty");
    		$this->set_block($block_handle."__".$i, $m[2][$i]);   
    		    		
    		if($m[1][$i])
    		{
    			$key = trim($m[1][$i]);
    			if(substr($key,0,4)=="KEY=")
    				$child[substr($key,4)] = $i;
    		}
    	}

    	$this->set_block($block_handle,$child,$block_c);
    }
  }
  
  function get_block_count($handle)
  {
  	if(isset($this->blocks[$handle]))
  		return $this->blocks[$handle][0];
  	return 0;
  }
  
  /* public: assign(array $values)
   * values: array of variable name, value pairs.
   *
   * public: assign(string $varname, string $value)
   * varname: name of a variable that is to be defined
   * value:   value of that variable
   */
  function assign($varname, $value = "")
  {
    if (is_string($varname))
    {
      if (!empty($varname))
      {
        $this->varkeys[$varname] = "/".$this->varname($varname)."/";
        $this->varvals[$varname] = $value;
	  }
    }
    else if(is_array($varname))
    {
      reset($varname);
      while(list($k, $v) = each($varname)) {
        if (!empty($k))
        {
          $this->varkeys[$k] = "/".$this->varname($k)."/";
          $this->varvals[$k] = $v;
        }
      }
    }
    else
    	$this->error("set_var: wrong varname type ".gettype($varname));
  }
  
  function assign_local($handle,$varname,$value)
  {
  	if(!isset($this->blocks[$handle]))
  			$this->error("assign_local: block[$handle] is invalid");
  		
	if(!$this->blocks[$handle][3])$this->blocks[$handle][3] = array();
	$this->blocks[$handle][3][$varname] = "/".$this->varname($varname)."/";
	if(!$this->blocks[$handle][4])$this->blocks[$handle][4] = array();
	$this->blocks[$handle][4][$varname] = $value;
  }

  /* public: parse_template(string $handle)
   * handle: handle of template where variables are to be parse.
   */
  function parse_template($handle,$k,$v)
  {
    $str = $this->get_block($handle);
    
    if($k && $v)
    	$str = @preg_replace($k, $v, $str);
    $str = @preg_replace($this->varkeys, $this->varvals, $str);
    return $str;
  }
  
  function reset_block($handle)
  {
  	if(isset($this->blocks[$handle]))
  		$this->blocks[$handle][2] = 0;  	
  }
    
  function parse_block($handle,$key="",$append = true,$k=0,$v=0)
  {
  	if(!isset($this->blocks[$handle]))
  			$this->error("parse_block: block[$handle] is invalid");
  		
  	if(!$key)
  	{  		
  		if(is_string($this->blocks[$handle][1]))
  		{
  			if(!$k)$k=$this->blocks[$handle][3];
  			if(!$v)$v=$this->blocks[$handle][4];
  			$this->parse($handle,$handle,$append,$k,$v);
  		}
  		else
  		{
  			$c = $this->blocks[$handle][0];	
  			$i = $this->blocks[$handle][2];
  			if(!$k)$k=$this->blocks[$handle][3];
  			if(!$v)$v=$this->blocks[$handle][4];  			
  			$this->parse($handle,$handle."__".$i,$append,$k,$v);
  			$i++;
  			if($i>=$c)$i=0;
  			$this->blocks[$handle][2] = $i;
  		}
  	}
  	else
  	{
  		if(is_string($this->blocks[$handle][1]))
  			$this->error("parse_block: not found block[$handle]'s child[$key]");
  		else
  		{
  			if(isset($this->blocks[$handle][1][$key]))
  			{
  				$i = $this->blocks[$handle][1][$key];
  				$this->parse($handle,$handle."__".$i,$append);
  			}
  			else
  				$this->error("parse_block: not found block[$handle]'s child[$key]");
  		}
  	}
  }
    
  /* public: parse(string $target, string $handle, boolean append)
   * public: parse(string $target, array  $handle, boolean append)
   * target: handle of variable to generate
   * handle: handle of template to substitute
   * append: append to target handle
   */
  function parse($target, $handle, $append = false,$k=0,$v=0)
  {
  	$this->LAST = $target;
  	
    if(is_string($handle))
    {
    	if( (substr($handle,0,1)) == '.' )
			{
				// Append this template to a previous ReturnVar

				$append = true;
				$handle = substr($handle,1);
			}
    	
      $str = $this->parse_template($handle,$k,$v);
      if ($append)
        $this->set_var($target, $this->get_var($target).$str);
      else
        $this->set_var($target, $str);
    }
    else if(is_array($handle))
    {
      reset($handle);
      while(list($i, $h) = each($handle))
      {
        $str = $this->parse_template($h,$k,$v);
        $this->set_var($target, $str);
      }
    }
    else
    	$this->error("parse: wrong handle type ".gettype($varname));
  }
  
  function clear()
  {
  	$blocks = array();
  	$varkeys = array();
  	$varvals = array();  	
  }
  
  function clear_var($handle)
  {
  	$this->set_var($handle,"");
  }
  
  function fetch($var_name)
  {
  	return $this->get_var($var_name);
  }
  
  function NoCatch()
  {
  	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    			// Date in the past
	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

	header ("Cache-Control: no-cache, must-revalidate");  			// HTTP/1.1
	header ("Pragma: no-cache");                          			// HTTP/1.0
  }
  
  function FastPrint($var_name = "",$nocatch=0)
  {
  	if(empty($var_name))
		{
			$var_name = $this->LAST;
		}
  	
  	global $_DEBUG_;
	
	if($nocatch)
		$this->NoCatch();
	
	if($_DEBUG_)
		$this->finish($var_name);
    echo $this->get_var($var_name);
  }
  
  function FastSave($filename, $var_name = "")
  {
  	global $_DEBUG_;
  	
  	if(empty($var_name))
		{
			$var_name = $this->LAST;
		}
  	
  	if($filename == "")
		$this->error("FastSave: No filename specific.");

	if($_DEBUG_)
		$this->finish($var_name);
    		  
	$fp = fopen( $filename, "w");
	if($fp)
	{
		$b = fwrite($fp, $this->get_var($var_name));
		fclose($fp);
		return $b;
	}
	return 0;
  }
  
  function is_block_exists($handle)
  {
  	return isset($this->blocks[$handle]);
  }
  
  function get_block($handle)
  {
	if(!isset($this->blocks[$handle]))
		$this->error("block[$handle] do not exists");
		
	if(!is_string($this->blocks[$handle][1]))
	{
		$i = $this->blocks[$handle][2];
		return $this->get_block($handle."__".$i);
	}
	else
		return $this->blocks[$handle][1];
  }
  
  function replace_block($handle,$str)
  {
  	if(!isset($this->blocks[$handle]))
		$this->error("block[$handle] do not exists");
		
	if(!is_string($this->blocks[$handle][1]))
	{
		$i = $this->blocks[$handle][2];;
		return $this->replace_block($handle."__".$i,$str);
	}
	else
		$this->blocks[$handle][1] = $str;
  }
  
  function set_block($handle,$info,$child = 1)
  {
  	$this->blocks[$handle] = array($child,$info,0,0,0);
  }
  
  /* public: set_var(array $values)
   * values: array of variable name, value pairs.
   *
   * public: set_var(string $varname, string $value)
   * varname: name of a variable that is to be defined
   * value:   value of that variable
   */
  function set_var($varname, $value = "")
  {
    $this->assign($varname,$value);
  }
 
  /* public: get_var(string varname)
   * varname: name of variable.
   *
   * public: get_var(array varname)
   * varname: array of variable names
   */
  function get_var($varname)
  {
  	if (is_string($varname))
    {
      return $this->varvals[$varname];
    }
    else if(is_array($varname))
    {
      reset($varname);
      while(list($k, $v) = each($varname))
        $result[$k] = $this->varvals[$k];
      return $result;
    }
    else
    	$this->error("get_var: wrong varname type ".gettype($varname));
  }
  
  /* public: finish($handle)
   * handle: handle of a template.
   */
  function finish($var_name)
  {
  	$template = $this->get_var($var_name);
  	
  	if (ereg("({[A-Z0-9_]+})",$template))
	{
		$unknown = split("\n",$template);
		
		while (list ($Element,$Line) = each($unknown) )
		{
			if(!(empty($Line)))
			{
				$m = array();
				if (ereg("({[A-Z0-9_]+})",$Line,$m))
				{
					$UnkVar = $m[1];
					if(!(empty($UnkVar)))
						echo "<b>template warning</b>: no value found for template variable: $UnkVar<br>\n";
				}
			}
		}
	}  	
  }  
  
  /***************************************************************************/
  /* private: filename($filename)
   * filename: name to be completed.
   */
  function filename($filename) {
    if (substr($filename, 0, 1) != "/") {
      $filename = $this->root."/".$filename;
    }
    
    if (!file_exists($filename))
      $this->error("filename: file $filename does not exist.");

    return $filename;
  }
  
  /* private: varname($varname)
   * varname: name of a replacement variable to be protected.
   */
  function varname($varname)
  {
    return preg_quote("{".$varname."}");
  }
   

  /* private: loadfile(string $handle)
   * handle:  load file defined by handle, if it is not loaded yet.
   */
  function loadfile($handle,$filename)
  {
  	if(!file_exists($filename))
  		$this->error("loadfile: $filename does not exist");
  		
    $str = implode("", @file($filename));
    if (empty($str))
      $this->error("loadfile: $filename is empty.");

    $this->set_block($handle,$str);
    
    return true;
  }

  /***************************************************************************/
  /* public: error(string $msg)
   * msg:    error message to show.
   */
  function error($msg)
  {
	global $_DEBUG_;
    
    echo "<b>template handle error</b>";
    if($_DEBUG_)
		echo ": $msg<br>\n";
    exit;
  }  
}
?>

<?php

class cms_xml_parse
{
	var $xml_file   = "";
	var $resource   = "";
	
	var $b_in_item  = false;
	var $tag_name   = "";
	var $item		= array();
	var $ret_items_arr	= array();

	function cms_xml_parse($xml_file)
	{
		$this->set_xml_file($xml_file);
	}
	
	function set_xml_file($xml_file)
	{
		$this->xml_file  = $xml_file;
	}
	
	function get_xml_encoding()
	{
		if (preg_match('/ encoding="([^"]*)"/i', $this->resource, $result))
		{
			return strtolower($result[1]);
		}
		else
		{
			return "utf-8";
		}
	}
	
	function conv_encoding($arr)
	{
		foreach ($arr as $key=>$value)
		{
			if (is_array($value))
			{
				$arr[$key] = $this->conv_encoding($value);
			}
			else
			{
				$arr[$key]=iconv("utf-8","gb2312",trim($value));
			}
		}
		return $arr;
	}	
	
	function explain()
	{
		$this->resource = poco_file_get_contents($this->xml_file);

		if (!$this->resource) return false;
		
		$encoding = $this->get_xml_encoding();

		if (phpversion()*1==4.4) 
		{
			if ($encoding != "utf-8")
			{
				$this->resource = iconv($encoding, "utf-8//ignore", $this->resource);
			}
	
			$xml_parser = xml_parser_create('utf-8');
		}
		else 
		{
			if ($encoding != "gbk")
			{
				$this->resource = iconv($encoding, "gbk//ignore", $this->resource);
			}
	
			$xml_parser = xml_parser_create();
		}

		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
		xml_set_object($xml_parser, $this);
		xml_set_element_handler($xml_parser, "startElement", "endElement");
		xml_set_character_data_handler($xml_parser, "characterData");
		xml_parse($xml_parser, $this->resource, true);
		xml_parser_free($xml_parser);
		
		$this->ret_items_arr = $this->conv_encoding($this->ret_items_arr);


		return $this->ret_items_arr;
	}

	function get_file()
	{
		$this->resource = "";
		$tmp = poco_file_get_contents($this->xml_file);
		if ($tmp == false)
		{
			return false;
		}
		else
		{
			$this->resource=$tmp;
			return true;
		}
	}

	function startElement($parser, $name, $attribs)
	{
		if( strtolower($name) == "item" ) 	$this->b_in_item = true;		
		if ($this->b_in_item) 				$this->tag_name = strtolower($name);
		
	}
	
	function characterData($parser, $data)
	{		
		if ($this->b_in_item && trim($data))
		{
			switch ($this->tag_name)
			{
				case "place_number":    $this->item['place_number'] = $data*1; 		break;
				case "title": 			$this->item['title'] 		.= trim($data); 	break;
				case "user_id":        	$this->item['user_id']      = $data*1; 		break;
				case "img_url":        	$this->item['img_url']      .= trim($data); 	break;
				case "link_url":     	$this->item['link_url']     .= trim($data); 	break;
				case "content":     	$this->item['content']     	.= $data; 		break;
				case "remark":    		$this->item['remark']     	.= $data; 		break;
			}
		}
	}

	function endElement($parser, $name)
	{
		if( strtolower($name) == "item" ) 
		{
			$this->b_in_item = false;
			$this->ret_items_arr[] = $this->item;
			$this->item = array();
		}
	}
}
?>
<?
include_once("MemCachedClient.inc.php");
class fulltext_search_helper_core_class
{
	var $host = "";		//IP地址
	var $port = "";		//端口

	function fulltext_search_helper_core_class($host="121.9.248.27",$port=9970)
	{
		$this->mc = new memcached(array( 
			"servers" => array($host.":".$port), 
			"debug"   => false, 
			"compress_threshold" => 10240, 
			"persistant" => true)
		); 
	}
	
	###增加索引方法_1###
	#$insert_1：$indexName（索引名），$values（数组）
	#$result：错误代码 0成功 6队列满 4格式错误
	function insert_1($indexName,$arr)
	{
		$key="Append|".$indexName;
		foreach($arr[0] as $fieldname => $aa)
		{
			$fields=$fields.$fieldname.",";
		}
		$value=substr($fields,0,-1)."\r\n";
		for($i=0;$i<count($arr);$i++)
		{
			$value.="\r\n";
			foreach($arr[$i] as $bb => $text)
			{
				$value.=strlen($text)."\r\n";
				$value.=$text."\r\n";
			}
			$value.="ROW END\r\n";
		}
		$result = $this->mc->set($key, $value);
		return $result;
	}
	
	###增加索引方法_2###
	#$insert_2：$indexName（索引名），$id_value（主键），$text_value（字段）
	#$result：错误代码 0成功 6队列满 4格式错误
	function insert_2($indexName,$id_value,$text_value,$add_time)
	{
		$key="Append|".$indexName;
		$value="id,text,add_time\r\n";
		$value.="\r\n";
		$value.=strlen($id_value)."\r\n";
		$value.=$id_value."\r\n";
		$value.=strlen($text_value)."\r\n";
		$value.=$text_value."\r\n";
		$value.=strlen($add_time)."\r\n";
		$value.=$add_time."\r\n";
		$value.="ROW END\r\n";
		$result = $this->mc->set($key, $value);
		return $result;
	}

	###删除###
	#$delete：$indexName（索引名），$values（字段名=内容）
	#$result：错误代码 0成功 6队列满 4格式错误
	function delete($indexName,$values)
	{
		$key="Remove|".$indexName;
		$tmp=explode("=",$values);
		$value="\r\n";
		$value.=$tmp[0]."\r\n";
		$value.=strlen($tmp[1])."\r\n";
		$value.=$tmp[1]."\r\n";
		$result = $this->mc->set($key, $value);
		return $result;
	}
	
	###查询###
	#数组字段解释：indexname（索引名）fields（返回字段）
	#where（查询内容： text:giggs AND sex:男 只返回两个条件都符合的数据
	# text:giggs OR sex:男 只要有其中一个条件的数据都返回，两个条件都符合的数据不返回
	# text:giggs liaocc 返回数据是先出都符合条件的，再出有一个条件符合的）
	#order（排序方法 格式：credit_point desc） limit（返回条数 格式：0,10）
	#source（来源） keyword（关键字） act_type_id（分类）
	#remark（备注 如：查98索引是要求按积分排序的jifen_sort）
	function Select($arr){
		$key="Searchpact";
		$query_times=count($arr);
		for($n=0;$n<$query_times;$n++)
		{
			$key.="|".$arr[$n]["indexname"].":".$arr[$n]["fields"].":".bin2hex("LUCENE ".$arr[$n]["where"]).":".$arr[$n]["order"].":".$arr[$n]["limit"].":".$arr[$n]["source"].":".bin2hex($arr[$n]["keyword"]).":".$arr[$n]["act_type_id"].":".$arr[$n]["remark"];
		}
		//echo $key."<br>";
		$str = $this->mc->get($key);
		//echo $str;
		if($query_times==1)
		{
			$str=substr($str,2);
			$tmp=$this->getlen($str);
			$result["indexname"]=$tmp[0];
			$str=$tmp[1];
			$tmp=$this->getlen($str);
			$result["err_code"]=$tmp[0];
			$str=$tmp[1];
			if($result["err_code"]==0)
			{
				$tmp=$this->getlen($str);
				$result["total"]=$tmp[0];
				$str=$tmp[1];
				$tmp=$this->getlen($str);
				$result["result_total"]=$tmp[0];
				$str=$tmp[1];
				if($result["result_total"]>0)
				{
					$tmp=$this->getlen($str);
					$str=$tmp[1];
					$fields=explode(",",$tmp[0]);
					$result["result"]=array();
					for($a=0;$a<$result["result_total"];$a++)
					{
						for($b=0;$b<count($fields);$b++)
						{
							$tmp=$this->getlen($str);
							$vlen=$tmp[0];
							$str=$tmp[1];
							$value=substr($str,0,$vlen);
							$result["result"][$a][$fields[$b]]=$value;
							$str=substr($str,$vlen);
							$tmp=$this->getlen($str);
							$str=$tmp[1];
						}
						$tmp=$this->getlen($str);
						$str=$tmp[1];
					}
				}
			}
		}
		else 
		{
			for($m=0;$m<$query_times;$m++)
			{
				$result[$m]=array();
				$str=substr($str,2);
				$tmp=$this->getlen($str);
				$result[$m]["indexname"]=$tmp[0];
				$str=$tmp[1];
				$tmp=$this->getlen($str);
				$result[$m]["err_code"]=$tmp[0];
				$str=$tmp[1];
				if($result[$m]["err_code"]==0)
				{
					$tmp=$this->getlen($str);
					$result[$m]["total"]=$tmp[0];
					$str=$tmp[1];
					$tmp=$this->getlen($str);
					$result[$m]["result_total"]=$tmp[0];
					$str=$tmp[1];
					if($result[$m]["result_total"]>0)
					{
						$tmp=$this->getlen($str);
						$str=$tmp[1];
						$fields=explode(",",$tmp[0]);
						$result[$m]["result"]=array();
						for($a=0;$a<$result[$m]["result_total"];$a++)
						{
							for($b=0;$b<count($fields);$b++)
							{
								$tmp=$this->getlen($str);
								$vlen=$tmp[0];
								$str=$tmp[1];
								$value=substr($str,0,$vlen);
								$result[$m]["result"][$a][$fields[$b]]=$value;
								$str=substr($str,$vlen);
								$tmp=$this->getlen($str);
								$str=$tmp[1];
							}
							$tmp=$this->getlen($str);
							$str=$tmp[1];
							if($tmp[0]=="ROW END"){
								continue;
							}
						}
					}
				}
			}
		}
		return $result;
	}
	
	function getlen($string)
	{
		$p=strpos($string,"\r\n");
		if($p==0){
			return array("",substr($string,2));
		}
		if($string==""){
			return null;
		}
		return array(substr($string,0,$p),substr($string,$p+2));
	}
}
?>
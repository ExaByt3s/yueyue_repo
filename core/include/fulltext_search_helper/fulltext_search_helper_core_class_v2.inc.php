<?
include_once("/disk/data/htdocs233/mypoco/fulltext_search_helper/MemCachedClient.inc.php");
class fulltext_search_helper_core_class
{
	var $host = "";		//IP��ַ
	var $port = "";		//�˿�

	function fulltext_search_helper_core_class($host="183.60.210.12",$port=9970)
	{
		
		$this->mc = new memcached(array( 
			"servers" => array($host.":".$port), 
			"debug"   => false, 
			"compress_threshold" => 10240, 
			"persistant" => true)
		);
	}
	
	###������������_1###
	#$insert_1��$indexName������������$values�����飩
	#$result��������� 0�ɹ� 6������ 4��ʽ����
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
	
	###������������_2###
	#$insert_2��$indexName������������$id_value����������$text_value���ֶΣ�
	#$result��������� 0�ɹ� 6������ 4��ʽ����
	function insert_simple($indexName,$id_value,$text_value,$add_time)
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

	###ɾ��###
	#$delete��$indexName������������$values���ֶ���=���ݣ�
	#$result��������� 0�ɹ� 6������ 4��ʽ����
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

	###������ѯ###
	#�����ֶν��ͣ�indexname����������fields�������ֶΣ�
	#where����ѯ���ݣ� text:giggs AND sex:�� ֻ�����������������ϵ�����
	# text:giggs OR sex:�� ֻҪ������һ�����������ݶ����أ��������������ϵ����ݲ�����
	# text:giggs liaocc �����������ȳ������������ģ��ٳ���һ���������ϵģ�
	#order�����򷽷� ��ʽ��credit_point desc�� limit���������� ��ʽ��0,10��
	#source����Դ�� keyword���ؼ��֣� act_type_id�����ࣩ
	function SelectFood($arr)
	{
	
		$key="SearchFood";
		$query_times=count($arr);
		for($n=0;$n<$query_times;$n++)
		{
			$arr[$n]["where"] = ereg_replace("[ ]{1,}"," ",$arr[$n]["where"]);
			$arr[$n]["keyword"] = ereg_replace("[ ]{1,}"," ",$arr[$n]["keyword"]);
			if($arr[$n]["keyword"]!="")
			{
				$keywords = explode(" ",$arr[$n]["keyword"]);
				for($i=0;$i<count($keywords);$i++)
				{
					if($i>0)
					{
						$a1 .= " AND res_name:".$keywords[$i];
						$a2 .= " AND main_food:".$keywords[$i];
						$a3 .= " AND address:".$keywords[$i];
						$a4 .= " AND text:".$keywords[$i];
					}
					else 
					{
						$a1 = "res_name:".$keywords[$i];
						$a2 = "main_food:".$keywords[$i];
						$a3 = "address:".$keywords[$i];
						$a4 = "text:".$keywords[$i];
					}
				}
				$newwhere = "( ".$a1." ) OR ( ".$a2." ) OR ( ".$a3." ) OR ( ".$a4." )";
				//echo $where;
				$arr[$n]["where"] = substr(preg_replace("/".$arr[$n]["keyword"]."/",$newwhere,$arr[$n]["where"]),5);
			}
			$key.="|".$arr[$n]["indexname"].":".$arr[$n]["fields"].":".bin2hex("LUCENE ".$arr[$n]["where"]." ").":".$arr[$n]["order"].":".$arr[$n]["limit"].":".$arr[$n]["source"].":".bin2hex($arr[$n]["keyword"]).":".$arr[$n]["act_type_id"].":".$arr[$n]["remark"];
		}
		$str = $this->mc->get($key);
		
		$str=substr($str,2);
		$tmp=$this->getlen($str);
		$result["indexname"]=$tmp[0];
		$str=$tmp[1];
		$tmp=$this->getlen($str);
		$result["err_code"]=$tmp[0];
		$str=$tmp[1];
		$tmp=$this->getlen($str);
		$result["total"]=$tmp[0];
		$str=$tmp[1];
		$tmp=$this->getlen($str);
		$result["result_num"]=$tmp[0];
		$str=$tmp[1];
		$result["result"]=unserialize(substr($str,0,$result["result_num"]));
		
		return $result;
	}
	
	###��ѯ###
	#�����ֶν��ͣ�indexname����������fields�������ֶΣ�
	#where����ѯ���ݣ� text:giggs AND sex:�� ֻ�����������������ϵ�����
	# text:giggs OR sex:�� ֻҪ������һ�����������ݶ����أ��������������ϵ����ݲ�����
	# text:giggs liaocc �����������ȳ������������ģ��ٳ���һ���������ϵģ�
	#order�����򷽷� ��ʽ��credit_point desc�� limit���������� ��ʽ��0,10��
	#source����Դ�� keyword���ؼ��֣� act_type_id�����ࣩ
	#remark����ע �磺��98������Ҫ�󰴻��������jifen_sort��
	function Select($arr){

		$key="Searchpact";
		$query_times=count($arr);
		for($n=0;$n<$query_times;$n++)
		{
			//$___tmp = $arr[$n]["where"];//��ʱ����
			
			$arr[$n]["where"] = ereg_replace("[ ]{1,}"," ",$arr[$n]["where"]);
			$arr[$n]["keyword"] = ereg_replace("[ ]{1,}"," ",$arr[$n]["keyword"]);
			if($arr[$n]["indexname"]=="98")
			{
				$arr[$n]["where"] = preg_replace('/(\d+) (\d+)/i','$1 OR act_type_id:$2',$arr[$n]["where"]);
				$arr[$n]["where"] = preg_replace('/(\d+) (\d+)/i','$1 OR act_type_id:$2',$arr[$n]["where"]);
			}
			/*
			elseif ($arr[$n]["indexname"]=="121")
			{
				//���룬��ʱlogһ��
				$sql = "INSERT IGNORE INTO `test`.`travel_121_fullsearch_log_tbl` 
						(hash, keyword_tmp, keyword, referer, url)
						values
						(:x_hash, :x_keyword_tmp, :x_keyword, :x_referer, :x_url)";
				if (function_exists('db_simple_getdata') && function_exists('sqlSetParam')) 
				{
					sqlSetParam($sql, 'x_hash', md5($arr[$n]["where"]));
					sqlSetParam($sql, 'x_keyword_tmp', $___tmp);
					sqlSetParam($sql, 'x_keyword', $arr[$n]["where"]);
					sqlSetParam($sql, 'x_referer', $_SERVER['HTTP_REFERER']);
					sqlSetParam($sql, 'x_url', $_SERVER['REQUEST_URI']);
					db_simple_getdata($sql,false,2);
				}
			}
			*/

			/*
			if($arr[$n]["indexname"]=="107")
			{
				$keywords = explode(" ",$arr[$n]["keyword"]);
				for($i=0;$i<count($keywords);$i++)
				{
					if($i>0)
					{
						$a1 .= " AND res_name:".$keywords[$i];
						$a2 .= " AND main_food:".$keywords[$i];
						$a3 .= " AND address:".$keywords[$i];
						$a4 .= " AND text:".$keywords[$i];
					}
					else 
					{
						$a1 = "res_name:".$keywords[$i];
						$a2 = "main_food:".$keywords[$i];
						$a3 = "address:".$keywords[$i];
						$a4 = "text:".$keywords[$i];
					}
				}
				$newwhere = "( ".$a1." ) OR ( ".$a2." ) OR ( ".$a3." ) OR ( ".$a4." )";
				//echo $where;
				$arr[$n]["where"] = substr(preg_replace("/".$arr[$n]["keyword"]."/",$newwhere,$arr[$n]["where"]),5);
			}
			*/
			//echo $arr[$n]["where"];
			$key.="|".$arr[$n]["indexname"].":".$arr[$n]["fields"].":".bin2hex("LUCENE ".$arr[$n]["where"]." ").":".$arr[$n]["order"].":".$arr[$n]["limit"].":".$arr[$n]["source"].":".bin2hex($arr[$n]["keyword"]).":".$arr[$n]["act_type_id"].":".$arr[$n]["remark"];
		}

		$str = $this->mc->get($key);
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
							$result["result"][$a][$fields[$b]]=($value && $value!=='-Infinity') ? $value : '';
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
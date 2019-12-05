<?
include_once("MemCachedClient.inc.php");
class fulltext_search_helper_core_class
{
	var $host = "";		//IP��ַ
	var $port = "";		//�˿�

	function fulltext_search_helper_core_class($host="172.18.5.27",$port=9970)
	{
		$this->mc = new memcached(array( 
			"servers" => array($host.":".$port), 
			"debug"   => false, 
			"compress_threshold" => 10240, 
			"persistant" => true)
		); 
	}
	###�������###
	function check_sock()
	{
		$result=$this->mc;
		return $result;
	}

	###��ѯ_1###
	#$query��$indexName������������$fields�������ֶΣ�Ҫ�����ֶο��Բ����$where����ѯ���ݣ��ֶ�=��ѯ���ݣ���$order�������ֶ����򣬿���Ϊ�գ���$limit����������������Ϊ�գ�
	#$remark����ע�� $source����Դ��
	#$result������
	function select_1($indexName,$where,$fields="*",$order="",$limit="",$source="",$remark="")
	{
		$key="Search|".$indexName.":".$fields.":".bin2hex($where).":".$order.":".$limit.":".$source.":".$remark;
		$val = $this->mc->get($key);
		//print_r($val);
		$str=substr($val,2);
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
		return $result;
	}

	###��ѯ_2###
	#$query��$indexName������������$fields�������ֶΣ�Ҫ�����ֶο��Բ����$where_values����ѯ���ݣ���$order�������ֶ����򣬿���Ϊ�գ���$limit����������������Ϊ�գ�
	#$remark����ע�� $source����Դ��
	#$result������
	function select_simple($indexName,$where_values,$limit="",$order="",$fields="*",$source="",$remark="")
	{
		$key="Search|".$indexName.":".$fields.":".bin2hex("text=".$where_values).":".$order.":".$limit.":".$source.":".$remark;
		$val = $this->mc->get($key);
		//echo $var;
		$str=substr($val,2);
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
		return $result;
	}
	
	#��������ѯ
	function MultiSelect($arr){
		$key="Search";
		$query_times=count($arr);
		for($n=0;$n<$query_times;$n++)
		{
			$key.="|".$arr[$n]["indexname"].":".$arr[$n]["fields"].":".bin2hex($arr[$n]["where"]).":".$arr[$n]["order"].":".$arr[$n]["limit"].":".$arr[$n]["source"].":".$arr[$n]["remark"];
		}
		$str = $this->mc->get($key);
		//echo $str;
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
		return $result;
	}
	
	###���ϲ�ѯ###
	#�������	��$indexName ��ѯ���ݣ�$where_values �����ֶΣ�$fields="*" 
	#�ֶ�����$order="" ����������$limit="" ����ʽ��$sequence
	#���ࣺ$act_type_id ��Դ��$source ��ע��$remark
	function select_sort($indexName,$where_values,$fields="*",$order="",$limit="",$sequence="",$act_type_id="",$source="",$remark="")
	{
		$key="SearchSort|".$indexName.":".$fields.":".bin2hex($where_values).":".$order.":".$limit.":".$sequence.":".$source.":".$act_type_id.":".$remark;
		$val = $this->mc->get($key);
		//echo $key;
		$str=substr($val,2);
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
		return $result;
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
	
	###����###
	function admin($directive)
	{
		$result = $this->mc->set("MamageAdmin", $directive);
		return $result;
	}
	
	###����###	#$indexName������������$fields���ֶ�������$sql����ѯsql��䣩��$value�����ݣ���$db_set�����ݿ�ѡ�� mysql csv ����$host�����ݿ��ַ����$port�����ݿ�˿ڣ���$db�����ݿ�������$user�����ݿ��ʺţ���$pass�����ݿ����룩��$dir��csv�ļ���ŵ�ַ����$creat_time����ʱ����ʱ�䣩
	#$result��������� 0�ɹ� 5���������ڽ����� 4��ʽ���� 8���ݿ�ѡ�����
	function new_create($indexname,$fields,$sql,$value,$db_set,$host="",$port="",$db="",$user="",$pass="",$dir="",$creat_time="",$is_recreate="",$analyzer="")
	{
		if($db_set=="mysql")
		{	
			if($port!="")
			{
				$url = "jdbc:mysql://".$host.":".$port."/".$db;
				$driver="org.gjt.mm.mysql.Driver";
				$result=1;
			}
			else 
			{
				$url = "jdbc:mysql://".$host."/".$db;
				$driver="org.gjt.mm.mysql.Driver";
				$result=2;
			}
		}
		else if($db_set=="csv")
		{	
			$url = "jdbc:relique:csv:".$dir;
			$driver="org.relique.jdbc.csv.CsvDriver";
			$result=3;
		}
		else
		{
			$result=8;
		}
		if($result!=8)
		{
			$key="CreateIndex|".$indexname;
			$field_arr=explode(",",$fields);
			for($i=0;$i<count($field_arr);$i++)
			{
				$values.="field=".$field_arr[$i]."\r\n";
			}
			$values.="create_sql=".$sql."\r\n";
			$values.="create_field=".$value."\r\n";
			$values.="create_driver=".$driver."\r\n";
			$values.="create_dburl=".$url."\r\n";
			$values.="create_dbuser=".$user."\r\n";
			$values.="create_dbpass=".$pass."\r\n";
			$values.="recreate_time=".$creat_time."\r\n";
			$values.="is_recreate=".$is_recreate."\r\n";
			$values.="analyzer=".$analyzer."\r\n";
			$result = $this->mc->set($key, $values);
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
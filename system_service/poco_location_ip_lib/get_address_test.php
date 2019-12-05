<?php



# Create our client object.
$g_gmclient= new GearmanClient();
# Add default server (localhost).
//$g_gmclient->addServers("172.18.5.204:9230,172.18.5.204:9231");
#???ä»¥æ·»???ï¿??ï¿???????ï¿½ï¿½??
$g_gmclient->addServers("172.18.5.123:9411");
$g_gmclient->setTimeout(5000); // ÉèÖÃ³¬Ê±
define("GEARMAN_DO_SUC", 1);
define("GEARMAN_DO_FAIL", 0);

/**
 * Enter ï¿??ï¿??gearman?????ï¿½ï¿½??...
 *
 * @param unknown_type $func_name ??ï¿½ï¿½?ï¿½ï¿½??ï¿??
 * @param unknown_type $param ??ï¿½ï¿½?ï¿½ï¿½?????
 * @param unknown_type $timeout ï¿????ï¿½ï¿½?????
 * @return array($ret_val,$result);$ret_val: 1ï¿??ï¿???????????,$result??????ï¿??????????ï¿½ï¿½?? 0,å¤±è´¥,$result??????å¤±è´¥??????
 */
function gearman_do($func_name,$param,$timeout=10000)
{
	global $g_gmclient;
	#echo "Sending job\n";
	$retry_time=0;
	//???ï¿?????ï¿??ï¿??ï¿??ï¿??
	$max_retry=1;
	# Send reverse job
	$ret_val=GEARMAN_DO_FAIL;
	$break_while=0;
	//ï¿????ï¿½ï¿½?ï¿½ï¿½?ï¿½ï¿½??ï¿??è®¤ï¿½?ï¿½ç½®10ï¿??
	$g_gmclient->setTimeout($timeout);
	do
	{
	  #echo "cmd:$command\n";
	  $result = $g_gmclient->do($func_name, $param);
	  #echo "result:".$result."\n";
	  # Check for various return packets and errors.
	  switch($g_gmclient->returnCode())
	  {
	    case GEARMAN_WORK_DATA:
	      #echo "Data: $result\n";
	      break;
	    case GEARMAN_WORK_STATUS:
	      list($numerator, $denominator)= $g_gmclient->doStatus();
	      #echo "Status: $numerator/$denominator complete\n";
	      break;
	    case GEARMAN_WORK_FAIL:
	      #echo "Failed\n";
	      #exit;	      
	      #echo "fail! retry $retry_time";
		  if ($max_retry<$retry_time++) {
		  	#echo "retry fail return";
		  	$break_while = 1;
		  	//break;
		  }
	      break;
	    case GEARMAN_SUCCESS:
		  #echo "success\n";		  
		  $break_while = 1;
		  $ret_val = GEARMAN_DO_SUC;
	      break;
		case GEARMAN_TIMEOUT:
		  #echo "timeout please retry!\n";
		  #exit;
		  #echo "timeout retry $retry_time";
		  if ($retry_time++>2) {
		  	$ret_val = GEARMAN_DO_FAIL;
		  	#echo "retry fail return";
		  	$result = "TIMEOUT!";
		  	$break_while = 1;
		  	//break;
		  }
		  break;
	    default:
	      #echo "RET: " . $gmclient->returnCode() . "\n";
	      $break_while = 1;
	      $ret_val = GEARMAN_DO_FAIL;
	      $result = "UNKNOW ERROR!";
	      break;
	      #exit;
	  }
	  if ($break_while){
	  	break;
	  }
	}
	while($g_gmclient->returnCode() != GEARMAN_SUCCESS);	
	return array($ret_val,$result);
}



$param['ip']=$_GET["ip"];
//$param['file']="/disk/data/poco/get_ip_qqwry/qqwry_get_address/QQWry.Dat";



$t_begin= microtime(true);
//$result = gearman_do("cn.poco.VoteFunction",json_encode($param),10000000000000000);
$result = gearman_do("qqwry_get_ip",json_encode($param),5000);
$t_end=microtime(true);
$abc = json_decode($result[1]);
//var_dump($abc);
$xx = $abc->cmd_values;
$cc = json_decode($xx);

//var_dump($ccc);

//$act_obj->get_act_info_by_act_id($act_id);
echo "<table>";
foreach($cc as $value){
	$add = split(",",$value->address);
	for($i=0;$i<2;$i++){
		$address = pack('H*', $add[$i]);
		echo "<tr><td>$address</td><td>";
		echo"</tr>";
	}
}
echo "</table>";
echo "use time:".($t_end-$t_begin);//(($EndTime[sec]-$StartTime[sec])+($EndTime[usec]-$StartTime[usec])/1000000);
?>

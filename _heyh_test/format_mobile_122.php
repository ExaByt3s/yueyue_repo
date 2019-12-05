<?php
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$sql_str = "SELECT * FROM test.mobile_122_tbl ORDER BY id ASC";
$result = db_simple_getdata($sql_str, FALSE, 11);

foreach($result AS $key=>$val)
{
     $array_result = get_mobile_info($val['save_log']);
     
     if($array_result['client_id'])
    {
      
        $array_list['client_id']    = $array_result['client_id'];
        $array_list['client_sys']   = $array_result['client_sys'];
        $array_list['mobileuid']    = $array_result['uid'];
    }else{       
        $array_list['event_id']     = $array_result['event_id'];
        $array_list['event_time']   = $array_result['event_time']?$array_result['event_time']:$array_result['add_time'];
        $array_list['pid']          = $array_result['pid'];
        $array_list['mid']          = $array_result['mid'];
        $array_list['rmid']         = $array_result['rmid'];
        $array_list['did']          = $array_result['did'];
        $array_list['uid']          = $array_result['uid'];
        $array_list['vid']          = $array_result['vid'];
        $array_list['jid']          = $array_result['jid'];
        $array_list['rm']           = $array_result['rm'];
     }
     
    $sql_str = "INSERT INTO test.mobile_122_format_tbl(`uuid`, `client_id`, `client_sys`, `event_id`, `pid`, `mid`, `rmid`, `did`, `uid`, `vid`, `jid`, `rm`, `add_time`) 
                VALUES ('{$array_list[mobileuid]}', '{$array_list[client_id]}', '{$array_list[client_sys]}' ,'{$array_list[event_id]}', '{$array_list[pid]}', '{$array_list[mid]}', '{$array_list[rmid]}', '{$array_list[did]}', '{$array_list[uid]}', '{$array_list[vid]}', '{$array_list[jid]}', '{$array_list[rm]}', '{$array_list[add_time]}')";
    db_simple_getdata($sql_str, TRUE, 11);
}


function get_mobile_info($info)
{
	$array_url = explode('&',$info);
	
	foreach($array_url AS $key=>$val)
	{
		$v = explode('=',$val);
		if($v[0] == 'client_id')
		{
			$m = explode('_', $v[1]);
			$array_info['client_id'] = $m[0];
			switch($m[1])
			{
				case 1:
					$array_info['client_sys'] = 'iphone';
					break;
				case 2:
					$array_info['client_sys'] = 'ipad';
					break;
				case 3:
					$array_info['client_sys'] = 'android';
					break;
                case 4:
                    $array_info['client_sys'] = 'apad';
                    break;
               case 5:
                    $array_info['client_sys'] = 'win8';
                    break;
                    
                default:
                    $array_info['client_sys'] = $m[1];
			}
		}else{
			$array_info[$v[0]] = preg_replace('/\r|\n/','', trim($v[1])); 
		}

	}

	return $array_info;
}
?>
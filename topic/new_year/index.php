<?php 
$is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;

if ($is_weixin)
{
  	include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
   	$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
   	$url = $weixin_pub_obj->auth_get_authorize_url(array('route' => 'topic/88'), 'snsapi_base');
    header("Location:{$url}");
	exit;
}


include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

$cms_obj = new cms_system_class();
$tpl = new SmartTemplate ( "index.tpl.htm" );


$aera_config = array ("华南地区", "华东地区", "华北地区", "华中地区", "东北地区", "西北地区", "西南地区" );

$list = $cms_obj->get_last_issue_record_list ( false, '0,400', 'place_number ASC', 74 );


$current_locate_info = POCO::execute('common.get_ip_location_info', get_client_ip());

$current_city = str_replace("市","",$current_locate_info['city']);

if($_INPUT['location'])
{
	$location_name = $_INPUT['location'];
}
elseif($current_city)
{
	$location_name = $current_city;
}
else
{
	$location_name = "广州";
}

$location_name = urldecode($location_name);

foreach ( $aera_config as $aera_key => $aera )
{
	$i=0;
	$j=0;
	$n=0;
	foreach ( $list as $k => $record_val )
	{
		$remark_arr = explode ( "|", $record_val ['remark'] );
		$content_arr = explode ( "|", $record_val ['content'] );
		
		if ($remark_arr [0] == $aera)
		{
			if($remark_arr [1]!=$location_name)
			{
				$area_arr [$aera_key] ['area'] = $aera;
				$area_arr [$aera_key] ['area_id'] = $aera_key+1;
				$area_arr [$aera_key] ['city_arr'][$i]['area_id'] = $aera_key+1;
				$area_arr [$aera_key] ['city_count'] = $i+1;
				$area_arr [$aera_key] ['city_arr'][$i]['city_name'] = $remark_arr [1];
				$area_arr [$aera_key] ['city_arr'][$i]['user_id'] = $record_val ['user_id'];
				$area_arr [$aera_key] ['city_arr'][$i]['img_url'] = $record_val ['img_url'];
				$area_arr [$aera_key] ['city_arr'][$i]['time'] = $content_arr[0];
				$area_arr [$aera_key] ['city_arr'][$i]['num'] = $content_arr[1];
				
				if($i<=7)
				{
					$area_arr [$aera_key] ['city_arr'][$i]['display'] = 'block';
				}
				else
				{
					$area_arr [$aera_key] ['city_arr'][$i]['display'] = 'none';
				}
				
				$i++;
			}
			
			//匹配城市
			if($remark_arr [1]==$location_name)
			{
				$current_location_arr[$j]['city_count'] = $j+1;
				$current_location_arr[$j]['city_name'] = $remark_arr [1]; 
				$current_location_arr[$j]['user_id'] = $record_val ['user_id'];
				$current_location_arr[$j]['img_url'] = $record_val ['img_url'];
				$current_location_arr[$j]['time'] = $content_arr[0];
				$current_location_arr[$j]['num'] = $content_arr[1];
				
				if($j<=7)
				{
					$current_location_arr[$j]['display'] = 'block';
				}
				else
				{
					$current_location_arr[$j]['display'] = 'none';
				}
				
				
				$j++;
			}
			
			//默认城市
			if($remark_arr [1]=='广州')
			{
				$default_location_arr[$n]['city_count'] = $n+1;
				$default_location_arr[$n]['city_name'] = $remark_arr [1]; 
				$default_location_arr[$n]['user_id'] = $record_val ['user_id'];
				$default_location_arr[$n]['img_url'] = $record_val ['img_url'];
				$default_location_arr[$n]['time'] = $content_arr['time'];
				$default_location_arr[$n]['num'] = $content_arr['num'];
				
				if($n<=7)
				{
					$default_location_arr[$n]['display'] = 'block';
				}
				else
				{
					$default_location_arr[$n]['display'] = 'none';
				}
				
				
				$n++;
			}
			
		}
	}
	if(!isset($area_arr [$aera_key]['area']))
	{
		$area_arr [$aera_key] ['area'] = $aera;
	}
}


if($_INPUT['debug'])
{
	var_dump($location_name);
	print_r($current_locate_info);
	print_r($area_arr);
	print_r($current_location_arr);
	print_r($default_location_arr);
	
}

$get_city = get_city();

$recommend_city = recommend_city($location_name);

if(!$current_location_arr)
{
	$current_location_arr = $default_location_arr;
}

$count_current = count($current_location_arr);
if($count_current % 4!=0)
{
	$tpl->assign('download',1);
}

$tpl->assign('recommend_city',$recommend_city);
$tpl->assign('get_city',$get_city);
$tpl->assign('current_city',$location_name);
$tpl->assign('current_location_arr',$current_location_arr);
$tpl->assign('area_arr',$area_arr);
$tpl->assign('default_location_arr',$default_location_arr);

$tpl->output();


function get_city()
{
	global $aera_config;
	
	foreach($aera_config as $k=>$area)
	{
		$sql = "select city from pai_topic_db.pai_new_year_topic_area_tbl where area='{$area}'";
		$city_arr = db_simple_getdata($sql,false,101);
		
		$new_area[$k]['area'] = $area;
		$new_area[$k]['city'] = $city_arr;
	}
	
	return $new_area;
}


function recommend_city($city='')
{
	$sql = "select area from pai_topic_db.pai_new_year_topic_area_tbl where city='{$city}'";
	$area_arr = db_simple_getdata($sql,true,101);
	$area = $area_arr['area'];
	
	$sql = "select city from pai_topic_db.pai_new_year_topic_area_tbl where area='{$area}' and city !='{$city}' limit 4";
	return db_simple_getdata($sql,false,101);
	
}

?>
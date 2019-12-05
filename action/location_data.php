<?php

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
$area = include_once('/disk/data/htdocs232/poco/pai/m/config/area.conf.php');


function yue_get_pinyin_first_letter($str)
{
  $asc=ord(substr($str,0,1));
  if ($asc<160) //非中文
  {
    if ($asc>=48 && $asc<=57){
      return '1'; //数字
    }elseif ($asc>=65 && $asc<=90){
      return chr($asc); // A--Z
    }elseif ($asc>=97 && $asc<=122){
      return chr($asc-32); // a--z
    }else{
      return '~'; //其他
    }
  }
  else //中文
  {
    $asc=$asc*1000+ord(substr($str,1,1));
    //获取拼音首字母A--Z
    if ($asc>=176161 && $asc<176197){
      return 'A';
    }elseif ($asc>=176197 && $asc<178193){
      return 'B';
    }elseif ($asc>=178193 && $asc<180238){
      return 'C';
    }elseif ($asc>=180238 && $asc<182234){
      return 'D';
    }elseif ($asc>=182234 && $asc<183162){
      return 'E';
    }elseif ($asc>=183162 && $asc<184193){
      return 'F';
    }elseif ($asc>=184193 && $asc<185254){
      return 'G';
    }elseif ($asc>=185254 && $asc<187247){
      return 'H';
    }elseif ($asc>=187247 && $asc<191166){
      return 'J';
    }elseif ($asc>=191166 && $asc<192172){
      return 'K';
    }elseif ($asc>=192172 && $asc<194232){
      return 'L';
    }elseif ($asc>=194232 && $asc<196195){
      return 'M';
    }elseif ($asc>=196195 && $asc<197182){
      return 'N';
    }elseif ($asc>=197182 && $asc<197190){
      return 'O';
    }elseif ($asc>=197190 && $asc<198218){
      return 'P';
    }elseif ($asc>=198218 && $asc<200187){
      return 'Q';
    }elseif ($asc>=200187 && $asc<200246){
      return 'R';
    }elseif ($asc>=200246 && $asc<203250){
      return 'S';
    }elseif ($asc>=203250 && $asc<205218){
      return 'T';
    }elseif ($asc>=205218 && $asc<206244){
      return 'W';
    }elseif ($asc>=206244 && $asc<209185){
      return 'X';
    }elseif ($asc>=209185 && $asc<212209){
      return 'Y';
    }elseif ($asc>=212209){
      return 'Z';
    }else{
      return '~';
    }
  }
}

$s = iconv('GBK','UTF-8',$area['city']);
$s = json_decode($s,true);
$city_arr = poco_iconv_arr($s,'UTF-8','GBK');

$city = array();

foreach($city_arr as $key => $val)
{
  
  	foreach($city_arr[$key] as $t_key => $t_val)
	{
	     
	  	$sort_key = yue_get_pinyin_first_letter($t_val['text']);

	    $city[] = array(
		  "title" => $sort_key,
		  "id" => $sort_key,
		  "temp" => array(
			"city" => $t_val['text'],
			"location_id" => $t_val['id']  
		  )
		);
	  
		
	}
  	
}
array_multisort($city);

$temp_city = array();
$temp_idx = 0;

foreach($city as $key => $val)
{  
  
   if($city[$key]['title'] == $city[$key+1]['title'])
   {	 	
	 	$temp_city[$temp_idx]["title"] = $val['title'];
		$temp_city[$temp_idx]["id"] = $val['title'];
	    $temp_city[$temp_idx]["data"][] = $val['temp'];
	 
   }
   else
   {
	 	$temp_idx++;	 
   }
  
}

header('Content-Type: application/json');

// 构造JS格式的对象变量
if ($callback) 
{
	$temp_city = poco_iconv_arr($temp_city,'GBK','UTF-8');
	
	echo $callback."(".json_encode(array('code' => 1, 'msg' => 'success', 'verson' => '1.0.0', 'data' => $temp_city)).");";
} else 
{
    echo mobile_output(array('code' => 1, 'msg' => 'success', 'verson' => '1.0.0', 'data' => $temp_city));
}


?>
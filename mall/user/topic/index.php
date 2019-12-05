<?php
include_once 'config.php';

if(MALL_UA_IS_YUEYUE == 1)
{
	define('MALL_NOT_REDIRECT_LOGIN',1);

	// 权限检查
	$check_arr = mall_check_user_permissions($yue_login_id);

	// 账号切换时
	if(intval($check_arr['switch']) == 1)
	{
		$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
		header("Location:{$url}");
		die();
	}
}

// ========================= 初始化接口 start =======================
$topic_obj = POCO::singleton('pai_topic_class');

$user_agent_arr = mall_get_user_agent_arr();
/** 
 * 页面接收参数
 */
$id = intval($_INPUT['topic_id']) ;

$ret = $topic_obj->get_topic_info($id);

if($_INPUT['print'] == 1)
{
    var_dump($ret);
    die();
}

$ret['share_text']['url'] = $ret['share_text']['url_v3'];
$share_text = mall_output_format_data($ret['share_text']);


function mall_topic_page_replace_function($m)
{
    $html_array = include('../page_url_config.inc.php');

    $url = $m[2];
    $url_arr = parse_url($url);
	
	//var_dump($m);
	//var_dump($url_arr);


    if ($url_arr['scheme'] === 'yueyue')
    {
        $parse_arr = array();
        parse_str(htmlspecialchars_decode($url_arr['query']), $parse_arr);
        $pid = $parse_arr['pid'];
        $type = $parse_arr['type'];
		
		//var_dump($type);
        
        if ($type === 'inner_app') {
			
			// 针对外拍列表特殊处理！！！
			// hudw 2015.8.7
			if($pid == 1220076)
			{
				$pid = '0000001';
			}
			
            $new_url = $html_array[$pid] . '?' . $url_arr['query'];
			//var_dump($new_url);

        } elseif ($type === 'inner_web') {
			
			$temp_url = urldecode($parse_arr['url']);
			$temp_url_arr = parse_url($temp_url);
			
			if(preg_match('/www.yueus.com/',$_SERVER['SCRIPT_URI']))
			{
				$temp_url_arr['host'] = str_replace('yp.yueus.com','www.yueus.com',$temp_url_arr['host']);
				
				
			}
			
			if(preg_match('/test/',$_SERVER['SCRIPT_URL']))
			{
				$temp_url_arr['path'] = str_replace('user/','user/test/',$temp_url_arr['path']);
			}
            $new_url =  $temp_url_arr['scheme'].'://'.$temp_url_arr['host'].$temp_url_arr['path'].($temp_url_arr['query']?'?'.$temp_url_arr['query'] : '');
            
        }

        $m[0] = str_replace($url,$new_url,$m[0]);
    }

    return $m[0];
}
// ========================= 初始化接口 end =======================



// ========================= 区分pc，wap模板与数据格式整理 start  =======================
$user_agent_arr = mall_get_user_agent_arr();
if(MALL_UA_IS_PC == 1)
{
    //****************** pc版 ******************
    include_once './index-pc.php';
   
}
else
{
    
    //****************** wap版 ******************
    include_once './index-wap.php';
    
} 
// ========================= 区分pc，wap模板与数据格式整理 end  =======================




// ========================= 最终模板输出  =======================
$tpl->output();
?>
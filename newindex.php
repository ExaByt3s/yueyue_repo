<?php 
/**
 * @author hudw <hudw@poco.cn>
 * 新版首页
 */

// 配置是否使用test目录
$online_dir = '';

if(preg_match('/test||newindex/', $_SERVER['SCRIPT_URI']))
{
    $online_dir = 'test';
}

// 引用配置文件，使用绝对路径
include_once('/disk/data/htdocs232/poco/pai/mall/user/'.$online_dir.'/common.inc.php');

$index_template_root = '/disk/data/htdocs232/poco/pai/mall/user/'.$online_dir.'/';

// ========================= 初始化接口 start =======================
$ret = get_api_result('customer/buyer_index.php',array(
    'user_id' => $yue_login_id
    ));

$roll = get_api_result('event/get_hot_ad.php',array(
    'location_id' => empty($_COOKIE['yue_location_id']) ? 101029001 : $_COOKIE['yue_location_id'],
    'user_id' => $yue_login_id
    ));
    
    if($_INPUT['print'] == 1)
    {
        print_r($ret);
    }
    
foreach($roll['data']['list'] as $key => $val)
{
    $url_arr = parse_url($val['ad_url']);

    if ($url_arr['scheme'] === 'yueyue')
    {
        if(strpos($url_arr['fragment'],'topic/') == 0)
        {
            $topic_id = split('/',$url_arr['fragment']);
            $roll['data']['list'][$key]['ad_url'] = './topic/index.php?topic_id='.$topic_id[1];
        
        }
    }

    
}



if($_INPUT['print'] == 1)
{
    print_r($ret);
    die();
}
// ========================= 初始化接口 end =======================

// ========================= 区分pc，wap模板与数据格式整理 start  =======================
if(MALL_UA_IS_PC == 1)
{

	//****************** pc版 ******************
	include_once $index_template_root.'/index-pc.php';
}
else
{
	//****************** wap版 ******************
	include_once $index_template_root.'/index-wap.php';

}



// ========================= 最终模板输出  =======================
$tpl->output();
?>
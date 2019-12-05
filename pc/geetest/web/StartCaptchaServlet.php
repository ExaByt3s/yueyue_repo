<?php
/**
 * 使用Get的方式返回：challenge和capthca_id 此方式以实现前后端完全分离的开发模式 专门实现failback
 * @author Tanxu
 * $_COOKIE['yue_session_id']
 */

include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

//检查session_id
$yue_session_id = trim($_COOKIE['yue_session_id']);
if( strlen($yue_session_id)<1 )
{
	$result = array(
		'success' => 0,
		'gt' => '',
		'challenge' => '',
	);
	echo json_encode($result);
	exit();
}

//加载 极验校验类
require_once dirname(dirname(__FILE__)) . '/lib/class.geetestlib.php';
$GtSdk = new GeetestLib();

//访问极验服务器，如果服务器正常，就收到一个challenge码，返回1。
$rst = $GtSdk->register();
if( $rst )
{
    //把这个$_SESSION['gtserver'] = 1 改造成以下cache方法 私有前缀加yue_session_id
	$cache_key = 'G_YUEYUE_GEETEST_GTSERVER_' . $yue_session_id;
    POCO::setCache($cache_key, 1, array('life_time'=>86400));
    $result = array(
        'success' => 1,
        'gt' => CAPTCHA_ID,
        'challenge' => $GtSdk->challenge
    );
}
else
{
    //把这个$_SESSION['gtserver'] = 0; 改造成以下cache方法 前缀加yue_session_id
	$cache_key = 'G_YUEYUE_GEETEST_GTSERVER_' . $yue_session_id;
    POCO::setCache($cache_key, 0, array('life_time'=>86400));
    $rnd1 = md5(rand(0, 100));
    $rnd2 = md5(rand(0, 100));
    $challenge = $rnd1 . substr($rnd2, 0, 2);
    $result = array(
        'success' => 0,
        'gt' => CAPTCHA_ID,
        'challenge' => $challenge
    );
    //$_SESSION['challenge'] = $result['challenge'];
    $cache_key = 'G_YUEYUE_GEETEST_CHALLENGE_' . $yue_session_id;
    POCO::setCache($cache_key, $result['challenge'], array('life_time'=>86400));

}
echo json_encode($result);

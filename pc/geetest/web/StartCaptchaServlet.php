<?php
/**
 * ʹ��Get�ķ�ʽ���أ�challenge��capthca_id �˷�ʽ��ʵ��ǰ�����ȫ����Ŀ���ģʽ ר��ʵ��failback
 * @author Tanxu
 * $_COOKIE['yue_session_id']
 */

include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

//���session_id
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

//���� ����У����
require_once dirname(dirname(__FILE__)) . '/lib/class.geetestlib.php';
$GtSdk = new GeetestLib();

//���ʼ����������������������������յ�һ��challenge�룬����1��
$rst = $GtSdk->register();
if( $rst )
{
    //�����$_SESSION['gtserver'] = 1 ���������cache���� ˽��ǰ׺��yue_session_id
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
    //�����$_SESSION['gtserver'] = 0; ���������cache���� ǰ׺��yue_session_id
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

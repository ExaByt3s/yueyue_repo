<?php

/**
 * ��ȡ�����Ż�ȯ
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if( $yue_login_id<1 )
{
    $r_url = urlencode($_SERVER['HTTP_REFERER']);
    $__is_yueyue_app = (preg_match('/yue_pai/', $_SERVER['HTTP_USER_AGENT'])) ? true : false;
    if($__is_yueyue_app)
    {
        $j_url = "http://yp.yueus.com/mobile/auth_jump_page.php?r_url=".$r_url;
    }
    else
    {
        $j_url = "http://www.yueus.com/pc/login.php?r_url=".$r_url;
    }
    $j_url = str_replace('"', '\\"', $j_url);
    echo '<html lang="zh"><head><title></title><meta charset="gbk"><script>alert("�͹٣����ȵ�¼����ȯ");
    parent.location.href="'.$j_url.'";</script></head><body></body></html>';
    die();
    //echo '<html lang="zh"><head><title></title><meta charset="gbk"><script>alert("�͹٣����ȵ�¼����ȯ");</script></head><body></body></html>';
    //die();
}

$coupon_obj = POCO::singleton('pai_coupon_class');

//��ȡ�Ż�ȯ
$package_sn = trim($_INPUT['package_sn']);
$give_rst = $coupon_obj->give_coupon($yue_login_id, $package_sn, $b_valid=true);
if( $give_rst['result']!=1 )
{
	echo '<html lang="zh"><head><title></title><meta charset="gbk"><script>alert("'.$give_rst['message'].'");</script></head><body></body></html>';
    die();
}

echo '<html lang="zh"><head><title></title><meta charset="gbk"><script>alert("��ϲ�����Ż�ȯ��ȡ�ɹ�");</script></head><body></body></html>';

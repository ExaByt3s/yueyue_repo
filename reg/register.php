<?php
/** 
 * 
 * ԼԼģ��ע��ҳ
 * 
 * author ����
 * 
 * 
 * 2015-1-23
 * 
 * 
 */
 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

//��ȥȫվ��¼ҳ
$param_r_url = trim($_INPUT['r_url']);
$main_url = "http://www.yueus.com/pc/register.php?r_url=".$param_r_url;
header("location:{$main_url}");
exit;
//��ȥȫվ��¼ҳ


$r_url = urldecode(trim($_INPUT['r_url']));//��ʲô�����


//У�鷵�����ӽṹ������ȫ����
if(!empty($r_url) || $r_url!="")
{
    $parse_url_arr = parse_url($r_url);

    if($parse_url_arr['scheme']!="http")
    {
        echo "<script>
               alert('�������Ӳ��������ӽṹ');
               location.href='http://www.yueus.com/reg/register.php';
            </script>";
        exit();
    }
    if(!preg_match("/\.yueus\.com/",$parse_url_arr['host']))
    {
        echo "<script>
               alert('�������Ӳ���yueus����������');
               location.href='http://www.yueus.com/reg/register.php';
            </script>";
        exit();
    }
}

$tpl = $my_app_pai->getView('register.tpl.htm');

if(!empty($yue_login_id))
{
    //header("location:{$_SERVER['HTTP_REFERER']}");
    
    
    
    header("location:http://www.yueus.com/topic/party_topic/");
    
    
    
} 

// ������ʽ��js����
$Party_global_header = $my_app_pai->webControl('Party_global_header', array(), true);
$tpl->assign('Party_global_header', $Party_global_header);
$tpl->assign('r_url', $r_url);
$tpl->assign('encode_r_url',urlencode($r_url));// ͷ������

$header_html = $my_app_pai->webControl('PartyHeader', array(), true);
$tpl->assign('header_html', $header_html);
$tpl->assign("rand",201503091415);

$tpl->output();
 ?>
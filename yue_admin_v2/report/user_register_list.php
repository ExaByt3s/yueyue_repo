<?php
/**
 * @desc:   ע��������
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/18
 * @Time:   17:04
 * version: 1.0
 */
include_once('common.inc.php');
check_auth($yue_login_id,'user_register_list');//Ȩ�޿���
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//������
include_once(YUE_ADMIN_V2_PATH.'report/include/pai_reg_userinfo_class.inc.php');
$reg_userinfo_obj = new pai_reg_userinfo_class();
$page_obj = new show_page();
$show_count = 30;

$tpl = new SmartTemplate( REPORT_TEMPLATES_ROOT.'user_register_list.tpl.htm' );

$act = trim($_INPUT['act']);

$start_date = trim($_INPUT['start_date']);
$end_date = trim($_INPUT['end_date']);

$where_str = '';
$setParam = array();


/*
 * ��ѯһ�����ݣ�����ʵʱ����
 * */
$reg_userinfo_obj->add_reg_info();


if(preg_match("/\d\d\d\d-\d\d-\d\d/", $start_date) || preg_match("/\d\d\d\d\d\d\d\d/", $start_date))
{
    $start_date = date('Y-m-d',strtotime($start_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "DATE_FORMAT(add_time,'%Y-%m-%d')>='".mysql_escape_string($start_date)."'";
    $setParam['start_date'] = $start_date;
}
if(preg_match("/\d\d\d\d-\d\d-\d\d/", $end_date) || preg_match("/\d\d\d\d\d\d\d\d/", $end_date))
{
    $end_date = date('Y-m-d',strtotime($end_date));
    if(strlen($where_str)>0) $where_str .= ' AND ';
    $where_str .= "DATE_FORMAT(add_time,'%Y-%m-%d')<='".mysql_escape_string($end_date)."'";
    $setParam['end_date'] = $end_date;
}

if($act == 'export')
{
    $list = $reg_userinfo_obj->get_reg_list(false,$where_str,'add_time DESC,id DESC',"0,99999999");
    if(!is_array($list)) $list = array();

    $data = array();
    foreach($list as $key=>$v)
    {
        $data[$key]['add_time'] = $v['add_time'];
        $data[$key]['reg_count'] = $v['app_reg']+$v['weixin_reg']+$v['pc_reg']+$v['other_reg']+$v['org_num'];
        $data[$key]['app_reg'] = $v['app_reg'];
        $data[$key]['weixin_reg'] = $v['weixin_reg'];
        $data[$key]['pc_reg'] = $v['pc_reg'];
        $data[$key]['other_reg'] = $v['other_reg'];
        $data[$key]['org_num'] = $v['org_num'];
    }
    $fileName = '�û�ע���б�';
    //$title    = '���������б�';
    $headArr  = array("����","����","appע��","΢��ע��","pcע��","����ע��","�������");
    Excel_v2::start($headArr,$data,$fileName);
    unset($list);
    exit;
}
$page_obj->setvar($setParam);
$total_count = $reg_userinfo_obj->get_reg_list(true,$where_str);
$page_obj->set($show_count,$total_count);
$list = $reg_userinfo_obj->get_reg_list(false,$where_str,'add_time DESC,id DESC',$page_obj->limit());

if(!is_array($list)) $list = array();

foreach($list as &$v)
{
    $v['reg_count'] = $v['app_reg']+$v['weixin_reg']+$v['pc_reg']+$v['other_reg']+$v['org_num'] ;
}
$tpl->assign($setParam);
$tpl->assign('list',$list);
$tpl->assign('page',$page_obj->output(true));
$tpl->output();

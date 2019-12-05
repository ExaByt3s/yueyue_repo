<?php

include_once 'common.inc.php';

$tpl = new SmartTemplate ( TASK_TEMPLATES_ROOT."user_list.tpl.htm" );

$user_obj = POCO::singleton('pai_user_class');
$page_obj = new show_page ();

$search = $_INPUT['search'];
$text = $_INPUT['text'];

switch($search)
{
    case "user_id":
        $text = (int)$text;
        $where = "user_id={$text}";
        break;

    case "nickname":
        $where = "nickname LIKE '%{$text}%'";
        break;

    case "cellphone":
        $text = (int)$text;
        $where = "cellphone={$text}";
        break;
}


$show_count = 40;

$page_obj->setvar (array("search"=>$search,"text"=>$text));

$total_count = $user_obj->get_user_list(true,$where);

$page_obj->set ( $show_count, $total_count );

$list = $user_obj->get_user_list(false, $where, 'user_id desc',$page_obj->limit());

foreach($list as $k=>$val)
{
    $list[$k]['location'] = get_poco_location_name_by_location_id($val ['location_id']);
    $list[$k]['add_time'] = date("Y-m-d H:i:s",$val['add_time']);

    if($val['reset_pwd_time'])
        $list[$k]['reset_pwd_time'] = date("Y-m-d H:i:s",$val['reset_pwd_time']);
    else
        $list[$k]['reset_pwd_time'] = "";
}

$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
$tpl->assign('list', $list);
$tpl->assign('search', $search);
$tpl->assign('text', $text);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->output();

?>
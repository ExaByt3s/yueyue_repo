<?php 
/**
 * 引用资源文件定位，注意！确保引用路径争取
 */
$file_dir = dirname(__FILE__);
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once($file_dir. '/./webcontrol/head.php');
include_once($file_dir. '/./webcontrol/top.php');
include_once('common.php');

$payment_obj = POCO::singleton('pai_payment_class');
$page_obj = new show_page ();



$keyword = $_INPUT['keyword'];


/**********分页处理**********/
$total_count = $payment_obj->get_card_list_by_seller($yue_login_id, $keyword, true);

$show_count = 10;

$page_obj->setvar ( array ("keyword" => $keyword ));

$page_obj->set ( $show_count, $total_count );

$list = $payment_obj->get_card_list_by_seller($yue_login_id, $keyword, false, '', 'card_id ASC', $page_obj->limit());

foreach($list as $k=>$val)
{
	if($val['status']==8)
	{
		$list[$k]['num_text'] = activity_code_transfer($val['card_no'],4)."(已充值)";
	}
	else
	{
		$list[$k]['num_text'] = activity_code_transfer($val['card_no'],4);
	}
	
	
}

$tpl = $my_app_pai->getView('list.tpl.html');

// 总页数
$total_page = $page_obj->tpage;
// 当前页
$cur_page = $page_obj->curr;
// 上一页 下一页
$pre = $cur_page - 1 ;
$next = $cur_page + 1;
if($cur_page == 1){$pre = 1;}
if($cur_page == $total_page){$next = $total_page;}

if($total_count != 0)
{
	$tpl->assign ( "show_pager", 1);
}
else
{
	$tpl->assign ( "show_pager", 0);
}

// p 是默认页面参数
$tpl->assign ( "total_page", $total_page);
$tpl->assign ( "pre", $pre);
$tpl->assign ( "next", $next);
$tpl->assign ( "page_config_script", "<script>window.__page_config={total_page : ".$total_page.",pre:".$pre.",next:".$next.",cur_page:".$cur_page."}</script>");
/**********分页处理**********/



// 公共样式和js引入
$head_html = _get_wbc_head();
$top_html = _get_wbc_top();

$tpl ->assign('head_html',$head_html);
$tpl ->assign('top_html',$top_html);
$tpl ->assign('user_id',$yue_login_id);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign ( 'list', $list );

$tpl->output ();

if($_GET['print'] == 1)
{
	dump($list);
}

?>
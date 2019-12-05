<?php
/**
 * ���Ƶ��
 */

/**
 * common
 */
define("G_YUE_CMS_CHECK_ADMIN",1);
include_once("cms_common.inc.php");


/**
 * ģ��
 */
$tpl = new SmartTemplate("cms_issue_list.tpl.htm");


$rank_id = $_INPUT["rank_id"]*1;
$issue_id = $_INPUT["issue_id"]*1;
// ��ҳ
$page = $_INPUT['page'];
$page = ($page>0)?$page:1;
// ����
$order_id = $_INPUT["order_id"]*1;

// �������ñ�
$order_arr[0]['id'] = 0;
$order_arr[0]['order_by'] = "place_number ASC";
$order_arr[0]['str'] = "����������";

$order_arr[1]['id'] = 1;
$order_arr[1]['order_by'] = "log_id DESC";
$order_arr[1]['str'] = "����������";

$order_by = $order_arr[$order_id]['order_by'];
$tpl->assign('order_arr', $order_arr);
$tpl->assign('order_id', $order_id);


if ($rank_id < 1) die("��������rank_id={$rand_id}");

$cms_db_obj = POCO::singleton ( 'cms_db_class' );

/**
 * ��ʼȡ����
 */



/**
 * ȡ��
 */
$rank_info = $cms_db_obj->get_cms_info("rank_tbl", "rank_id={$rank_id}", "rank_id,channel_id,rank_url,rank_name,last_issue,img_size");
if (empty($rank_info)) js_pop_msg("δ���ҵ�idΪ����{$rank_id}�� �İ�");
$tpl->assign($rank_info);

/**
 * ȡƵ����Ϣ
 */
$channel_info = $cms_db_obj->get_cms_info("channel_tbl", "channel_id={$rank_info["channel_id"]}","channel_name");
$tpl->assign($channel_info);

/**
 * ������
 */
$issue_list = $cms_db_obj->get_cms_list("issue_tbl",
"rank_id={$rank_id}",
"issue_number,issue_name,issue_id,freeze",
"issue_number DESC");
$options_issue = array();
foreach ($issue_list as $val)
{
	$options_issue[$val["issue_id"]] = $val["freeze"] ? $val["issue_name"]."[����]" : $val["issue_name"];
}
$tpl->assign("options_issue", $options_issue);

/**
 * ȡ���ڰ�����
 */
$issue_info = array();
if ($issue_id > 0 )
{
	$issue_info = $cms_db_obj->get_cms_info("issue_tbl", "issue_id={$issue_id}");//ȡĳһ��
}

if ($issue_id >=0 && empty($issue_info))
{
	$issue_list = $cms_db_obj->get_cms_list("issue_tbl", "rank_id={$rank_id}", "*", "issue_number DESC", "", "0,1");//ȡ����һ��
	$issue_info = $issue_list[0];
}

$link_type_name_arr = array(
'inner_web'		=> '��web',
'outside_web'	=> '��web',
'inner_app'		=> '��app',
'outside_app'	=> '��app',
'other'			=> '����',
);

if (!empty($issue_info))
{
	/**
	 * ���Ѵ���
	 */
	if ($issue_info["rank_id"]!=$rank_id) die("��������issue_id={$issue_id}δ�ܶ�Ӧrank_id={$rank_id}");

	$tpl->assign($issue_info);
	$tpl->assign("issue_act", "issue_update");

	/**
	 * ȡ���û�
	 */
	$record_tbl_name = ($issue_info["freeze"]) ? "record_tbl_freeze" : "record_tbl";//�����������ŵ���ͬ�ı�

	// �������
	$record_count = $cms_db_obj->get_cms_count($record_tbl_name, "issue_id={$issue_info["issue_id"]}");
	$cls_page = new show_page();
	$cls_page->pvar = "page";
	$cls_page->setvar(array("rank_id" => $rank_id,
							"issue_id" => $issue_id,
							"order_id" => $order_id
	));
	$pagesize = 100;
	if ( $_COOKIE['yue_cms_pagesize']*1 > 0 )
	{
		$pagesize = $_COOKIE['yue_cms_pagesize'];
	}
	$tpl->assign("pagesize", $pagesize);
	$cls_page->set($pagesize,$record_count,$page);
	$page_str = $cls_page->output(true);
	$tpl->assign("page_str", $page_str);

	

	//var_dump($page_str);
	$record_list = $cms_db_obj->get_cms_list($record_tbl_name, "issue_id={$issue_info["issue_id"]}", "*", $order_by, "", $cls_page->limit());

	/* ȡ�û�ͷ�� */
	$user_icon_obj = POCO::singleton ( 'pai_user_icon_class' );
	foreach ($record_list as $k=>$v)
	{
		$record_list[$k]['user_icon'] = $user_icon_obj->get_user_icon($v['user_id'], 86);

		$record_list[$k]['link_type_name'] = $link_type_name_arr[$record_list[$k]['link_type']];
	}

	$tpl->assign("record_list", $record_list);
}
else
{
	/**
	 * �����һ��
	 */
	$issue_list = $cms_db_obj->get_cms_list("issue_tbl", "rank_id='{$rank_id}'", "issue_number", "issue_number DESC", "", "0,1");
	$issue_info = $issue_list[0];

	$this_issue 	= $issue_info["issue_number"]+1;

	$tpl->assign("issue_number", $this_issue);
	$tpl->assign("issue_name", "{$rank_info["rank_name"]}-��{$this_issue}��");
	$tpl->assign("begin_date", time());
	$tpl->assign("end_date", time());
	$tpl->assign("issue_act", "issue_add");
}

$tpl->assign("rank_id", $rank_id);

$tpl->assign("rand", md5(microtime(true)));

$tpl->output();
?>
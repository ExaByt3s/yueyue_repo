<?php
/**
 * �񵥵���
 * @author bowie
 */

/**
 * common
 */
define("G_YUE_CMS_CHECK_ADMIN",1);
include_once("cms_common.inc.php");
include_once(G_YUE_CMS_PATH."include/cms_parse_xml.inc.php");

/**
 * ģ��
 */

$tpl = new SmartTemplate("cms_import.tpl.htm");

/**
 * ��ʼ��������
 */
$xml_file = trim(urldecode($_REQUEST["xml_file"]));
$issue_id = $_INPUT["issue_id"]*1;
$rank_id = $_INPUT["rank_id"]*1;
$act = $_INPUT["act"];

$cms_db_obj = POCO::singleton ( 'cms_db_class' );

if ($rank_id < 1) js_pop_msg("�Բ�����������ѡ���㽫�������ݵİ�");

/**
 * ȡ��
 */
$rank_info = $cms_db_obj->get_cms_info("rank_tbl", "rank_id={$rank_id}", "rank_id,channel_id,rank_url,rank_name,last_issue");
if (empty($rank_info)) js_pop_msg("δ���ҵ�idΪ����{$rank_id}�� �İ�");
$tpl->assign($rank_info);

/**
 * �������ݲ���
 */
if ($issue_id > 0 && $xml_file && $act=="import")
{
	/**
	 * ����xml�ļ������ɲ�������
	 */
	$parse_xml_obj = new cms_xml_parse($xml_file);
	$record_arr = $parse_xml_obj->explain();

	$tpl->assign("record_arr", $record_arr);
	
	$issue_info = $cms_db_obj->get_cms_info("issue_tbl", "issue_id={$issue_id}", "issue_name,issue_number,begin_date,end_date,record_count");
	$tpl->assign($issue_info);
}
else 
{
	/**
	 * ������
	 */
	$issue_list = $cms_db_obj->get_cms_list("issue_tbl",
	"rank_id={$rank_id}",
	"issue_number,issue_name,issue_id",
	"issue_number DESC");
	$options_issue = array();
	foreach ($issue_list as $val)
	{
		$options_issue[$val["issue_id"]] = $val["issue_name"];
	}
	$tpl->assign("options_issue", $options_issue);
}

$tpl->assign("act", $act);
$tpl->assign("issue_id", $issue_id);
$tpl->assign("rank_id", $rank_id);
$tpl->output();
?>
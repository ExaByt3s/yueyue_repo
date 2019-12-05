<?php
/**
 * 专题公共尾
 *
 */
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

function _ctlyueshe_wx_topic_footer($attribs)
{
    global $tpl,$my_app_pai;

    

    $header_tpl	 = $my_app_pai->getView('/disk/data/htdocs232/poco/pai/webcontrols/yueshe_wx_topic_footer.tpl.htm',true);
    
    //获取微信JSSDK签名数据
	$app_id = 'wx25fbf6e62a52d11e';	//约约正式号
	$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
	$location_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$wx_sign_package = $weixin_helper_obj->wx_get_js_api_sign_package_by_app_id($app_id, $location_url);
	$header_tpl->assign('wx_sign_package',json_encode($wx_sign_package));
	
	if($attribs["id"])
	{
		$obj = new cms_system_class ();
		$cms_db_obj = POCO::singleton ( 'cms_db_class' );
		
		$record_list = $obj->get_record_list_by_issue_id(false, $attribs["id"], "0,1", "place_number ASC", $freeze=null, $where_str="");
		if($record_list[0]['remark'])
		{
			$record_list[0]['share_img'] = str_replace("-c","",$record_list[0]['remark']);
		}
		else
		{
			$record_list[0]['share_img'] = str_replace("-c","",$record_list[0]['img_url']);
		}
		
		$header_tpl->assign("record",$record_list[0]);
		
		$issue_info  = $cms_db_obj->get_cms_info("issue_tbl", "issue_id=".$attribs["id"], "issue_name");
		//print_r($record_list);
		$text_arr = explode("#",$issue_info['issue_name']);
		
		$header_tpl->assign("share_title",$text_arr[0]);
		$header_tpl->assign("share_content",$text_arr[1]);
	}
	$header_tpl->assign("id",$attribs["id"]);
	$header_tpl->assign("phone",$attribs["phone"]);
 
    $header_html = $header_tpl->result();
    return $header_html;
}


?>
<?php
/**
 * ר�⹫��ͷ
 *
 */

function _ctlyueshe_wx_topic_header($attribs)
{
    global $tpl,$my_app_pai,$yue_login_id;


    $header_tpl	 = $my_app_pai->getView('/disk/data/htdocs232/poco/pai/webcontrols/yueshe_wx_topic_header.tpl.htm',true);
    $header_tpl->assign("yue_login_id",$yue_login_id);
    $header_html = $header_tpl->result();
    return $header_html;
}


?>
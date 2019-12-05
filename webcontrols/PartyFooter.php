<?php
/**
 * 活动尾部控件
 * 
 * @param $attribs 控件的参数
 * 
 * @return string
 */

function _ctlPartyFooter($attribs)
{

    global $tpl,$my_app_pai;
    include_once('/disk/data/htdocs232/poco/pai/topic/party/party_common.inc.php');
    $footer_tpl = $my_app_pai->getView('/disk/data/htdocs232/poco/pai/webcontrols/PartyFooter.tpl.htm',true);
    $footer_tpl->assign("footer_iphone_link",G_IPHONE_DOWN_LINK);
    $footer_tpl->assign("footer_android_link",G_ANDROID_DOWN_LINK);
    
    
    return $footer_tpl->result();
}

?>
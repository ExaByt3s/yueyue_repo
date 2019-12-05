<?php

function _ctlParty_global_header($attribs)
{
    global $tpl;
    global $my_app_pai;
    global $yue_login_id;


    $header_tpl	 = $my_app_pai->getView('/disk/data/htdocs232/poco/pai/webcontrols/Party_global_header.tpl.htm',true);

    $dev = time();
    $header_tpl ->assign('_dev',$dev);
    $header_tpl ->assign("rand",201503091415);


    $header_html = $header_tpl->result();
    return $header_html;

}
?>
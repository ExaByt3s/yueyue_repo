<?php

function _ctlpc_global_top($attribs)
{
    global $tpl;
    global $my_app_pai;
    global $yue_login_id;


    $header_tpl	 = $my_app_pai->getView('./webcontrols/pc_global_top.tpl.htm',true);

    $header_tpl ->assign('_dev',time());
    $header_tpl ->assign("rand",date("YmdHis"));


    $header_html = $header_tpl->result();
    return $header_html;

}
?>
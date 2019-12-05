<?php

function _ctlpc_model_card_header($attribs)
{
    global $tpl;
    global $my_app_pai;
    global $yue_login_id;



    $tpl->assign('_dev',time());

    $header_tpl	 = $my_app_pai->getView('./webcontrols/pc_model_card_header.tpl.htm',true);
    $header_html = $header_tpl->result();
    return $header_html;

}
?>
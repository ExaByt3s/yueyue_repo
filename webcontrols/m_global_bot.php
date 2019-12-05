<?php

function _ctlm_global_bot($attribs)
{
    global $tpl;
    global $my_app_pai;
    global $yue_login_id;


    $header_tpl	 = $my_app_pai->getView('./webcontrols/m_global_bot.htm',true);

    $header_tpl ->assign('_dev',time());
    $header_tpl ->assign("rand",date("YmdHis"));


    $header_html = $header_tpl->result();
    return $header_html;

}
?>